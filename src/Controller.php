<?php

namespace WP2StaticSFTP;

class Controller {
    public function run() {
        // initialize options DB
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_sftp_options';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            value VARCHAR(255) NOT NULL,
            label VARCHAR(255) NULL,
            description VARCHAR(255) NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        $options = $this->getOptions();

        if ( ! isset( $options['host'] ) ) {
            $this->seedOptions();
        }

        add_filter( 'wp2static_add_menu_items', [ 'WP2StaticSFTP\Controller', 'addSubmenuPage' ] );

        add_action(
            'admin_post_wp2static_sftp_save_options',
            [ $this, 'saveOptionsFromUI' ],
            15,
            1
        );

        add_action(
            'wp2static_deploy',
            [ $this, 'deploy' ],
            15,
            1
        );

        add_action(
            'wp2static_post_deploy_trigger',
            [ 'WP2StaticSFTP\Deployer', 'cloudfront_invalidate' ],
            15,
            1
        );

        if ( defined( 'WP_CLI' ) ) {
            \WP_CLI::add_command(
                'wp2static sftp',
                [ 'WP2StaticSFTP\CLI', 'sftp' ]
            );
        }
    }

    // TODO: is this needed? confirm slashing of destination URLs...
    public function modifyWordPressSiteURL( $site_url ) {
        return rtrim( $site_url, '/' );
    }

    /**
     *  Get all add-on options
     *
     *  @return mixed[] All options
     */
    public static function getOptions() : array {
        global $wpdb;
        $options = [];

        $table_name = $wpdb->prefix . 'wp2static_addon_sftp_options';

        $rows = $wpdb->get_results( "SELECT * FROM $table_name" );

        foreach ( $rows as $row ) {
            $options[ $row->name ] = $row;
        }

        return $options;
    }

    /**
     * Seed options
     */
    public static function seedOptions() : void {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_sftp_options';

        $query_string =
            "INSERT INTO $table_name (name, value, label, description) VALUES (%s, %s, %s, %s);";

        $query = $wpdb->prepare(
            $query_string,
            'dir_permissions',
            '0755',
            'Remote dir permissions',
            'Set remote directories to this permission'
        );

        $wpdb->query( $query );

        $query = $wpdb->prepare(
            $query_string,
            'file_permissions',
            '0755',
            'Remote file permissions',
            'Set remote files to this permission'
        );

        $wpdb->query( $query );

        $query = $wpdb->prepare(
            $query_string,
            'group',
            '',
            'Remote group',
            'Set remote files and dirs to this group ownership'
        );

        $wpdb->query( $query );

        $query = $wpdb->prepare(
            $query_string,
            'host',
            'example.com',
            'Remote host',
            'Remote sFTP host name'
        );

        $wpdb->query( $query );

        $query = $wpdb->prepare(
            $query_string,
            'owner',
            '',
            'Remote owner',
            'Set remote files and dirs to this ownership'
        );

        $wpdb->query( $query );

        $query = $wpdb->prepare(
            $query_string,
            'passphrase',
            '',
            'Private key passphrase',
            ''
        );

        $wpdb->query( $query );

        $query = $wpdb->prepare(
            $query_string,
            'password',
            '',
            'sFTP password',
            ''
        );

        $wpdb->query( $query );

        $query = $wpdb->prepare(
            $query_string,
            'port',
            '22',
            'Port',
            'sFTP remote port'
        );

        $wpdb->query( $query );

        $query = $wpdb->prepare(
            $query_string,
            'private_key',
            '',
            'Private key path',
            'Location on server to private key'
        );

        $wpdb->query( $query );

        $query = $wpdb->prepare(
            $query_string,
            'remote_root',
            '',
            'sFTP Root Path',
            'Path in remote server that files will be uploaded to.'
        );

        $wpdb->query( $query );

        $query = $wpdb->prepare(
            $query_string,
            'username',
            '',
            'Username',
            'sFTP remote server username'
        );

        $wpdb->query( $query );
    }

    /**
     * Save options
     */
    public static function saveOption( $name, $value ) : void {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_sftp_options';

        $query_string = "INSERT INTO $table_name (name, value) VALUES (%s, %s);";
        $query = $wpdb->prepare( $query_string, $name, $value );

        $wpdb->query( $query );
    }

    public static function renderSFTPPage() : void {
        $view = [];
        $view['nonce_action'] = 'wp2static-sftp-options';
        $view['uploads_path'] = \WP2Static\SiteInfo::getPath( 'uploads' );
        $view['options'] = self::getOptions();

        require_once __DIR__ . '/../views/sftp-page.php';
    }


    public function deploy( $processed_site_path ) {
        \WP2Static\WsLog::l( 'sFTP Addon deploying' );

        $sftp_deployer = new Deployer();
        $sftp_deployer->upload_files( $processed_site_path );
    }

    public static function activate_for_single_site() : void {
        error_log( 'activating WP2Static sFTP Add-on' );
    }

    public static function deactivate_for_single_site() : void {
        error_log( 'deactivating WP2Static sFTP Add-on, maintaining options' );
    }

    public static function deactivate( bool $network_wide = null ) : void {
        error_log( 'deactivating WP2Static sFTP Add-on' );
        if ( $network_wide ) {
            global $wpdb;

            $query = 'SELECT blog_id FROM %s WHERE site_id = %d;';

            $site_ids = $wpdb->get_col(
                sprintf(
                    $query,
                    $wpdb->blogs,
                    $wpdb->siteid
                )
            );

            foreach ( $site_ids as $site_id ) {
                switch_to_blog( $site_id );
                self::deactivate_for_single_site();
            }

            restore_current_blog();
        } else {
            self::deactivate_for_single_site();
        }
    }

    public static function activate( bool $network_wide = null ) : void {
        error_log( 'activating sftp addon' );
        if ( $network_wide ) {
            global $wpdb;

            $query = 'SELECT blog_id FROM %s WHERE site_id = %d;';

            $site_ids = $wpdb->get_col(
                sprintf(
                    $query,
                    $wpdb->blogs,
                    $wpdb->siteid
                )
            );

            foreach ( $site_ids as $site_id ) {
                switch_to_blog( $site_id );
                self::activate_for_single_site();
            }

            restore_current_blog();
        } else {
            self::activate_for_single_site();
        }
    }

    public static function addSubmenuPage( $submenu_pages ) {
        $submenu_pages['sftp'] = [ 'WP2StaticSFTP\Controller', 'renderSFTPPage' ];

        return $submenu_pages;
    }

    public static function saveOptionsFromUI() {
        check_admin_referer( 'wp2static-sftp-options' );

        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_sftp_options';

        $simple_text_fields = [
            'dir_permissions',
            'file_permissions',
            'group',
            'host',
            'owner',
            'port',
            'private_key',
            'remote_root',
            'username',
        ];

        foreach ( $simple_text_fields as $option_name ) {
            $wpdb->update(
                $table_name,
                [ 'value' => sanitize_text_field( $_POST[ $option_name ] ) ],
                [ 'name' => $option_name ]
            );
        }

        $passphrase =
            $_POST['passphrase'] ?
            \WP2Static\Controller::encrypt_decrypt(
                'encrypt',
                sanitize_text_field( $_POST['passphrase'] )
            ) : '';

        $wpdb->update(
            $table_name,
            [ 'value' => $passphrase ],
            [ 'name' => 'passphrase' ]
        );

        $password =
            $_POST['password'] ?
            \WP2Static\Controller::encrypt_decrypt(
                'encrypt',
                sanitize_text_field( $_POST['password'] )
            ) : '';

        $wpdb->update(
            $table_name,
            [ 'value' => $password ],
            [ 'name' => 'password' ]
        );

        wp_safe_redirect( admin_url( 'admin.php?page=wp2static-sftp' ) );
        exit;
    }

    /**
     * Get option value
     *
     * @return string option value
     */
    public static function getValue( string $name ) : string {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_sftp_options';

        $sql = $wpdb->prepare(
            "SELECT value FROM $table_name WHERE" . ' name = %s LIMIT 1',
            $name
        );

        $option_value = $wpdb->get_var( $sql );

        if ( ! is_string( $option_value ) ) {
            return '';
        }

        return $option_value;
    }
}

