<?php

/**
 * Plugin Name:       WP2Static Add-on: sFTP Deployment
 * Plugin URI:        https://wp2static.com
 * Description:       sFTP deployment add-on for WP2Static.
 * Version:           1.0-alpha-002
 * Author:            Leon Stafford
 * Author URI:        https://ljs.dev
 * License:           Unlicense
 * License URI:       http://unlicense.org
 * Text Domain:       wp2static-addon-sftp
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'WP2STATIC_SFTP_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP2STATIC_SFTP_VERSION', '1.0-alpha-002' );

require WP2STATIC_SFTP_PATH . 'vendor/autoload.php';

function run_wp2static_addon_sftp() {
    $controller = new WP2StaticSFTP\Controller();
    $controller->run();
}

register_activation_hook(
    __FILE__,
    [ 'WP2StaticSFTP\Controller', 'activate' ]
);

register_deactivation_hook(
    __FILE__,
    [ 'WP2StaticSFTP\Controller', 'deactivate' ]
);

run_wp2static_addon_sftp();

