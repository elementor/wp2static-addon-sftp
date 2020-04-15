<?php

namespace WP2StaticSFTP;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use phpseclib\Net\SFTP;
use WP2Static\WsLog;

class Deployer {

    public function upload_files( string $processed_site_path ) : void {
        if ( ! is_dir( $processed_site_path ) ) {
            return;
        }

        $port = Controller::getValue( 'password' ) ? (int) Controller::getValue( 'port' ) : 22;

        $connection = new SFTP( Controller::getValue( 'host' ), $port );

        if (
            Controller::getValue( 'username' ) &&
            Controller::getValue( 'password' )
        ) {
            $username = Controller::getValue( 'username' );
            $password = \WP2Static\CoreOptions::encrypt_decrypt(
                'decrypt',
                Controller::getValue( 'password' )
            );

            if ( ! $connection->login( $username, $password ) ) {
                WsLog::l( 'Failed to login to sFTP with credentials provided (user/pass)' );
                return;
            }
        }

        // TODO: use priv key w optional passphrase authentication

        // iterate each file in ProcessedSite
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $processed_site_path,
                RecursiveDirectoryIterator::SKIP_DOTS
            )
        );

        foreach ( $iterator as $filename => $file_object ) {
            $base_name = basename( $filename );
            if ( $base_name != '.' && $base_name != '..' ) {
                $real_filepath = realpath( $filename );

                if ( \WP2Static\DeployCache::fileisCached( $filename ) ) {
                    continue;
                }

                if ( ! $real_filepath ) {
                    $err = 'Trying to add unknown file to: ' . $filename;
                    \WP2Static\WsLog::l( $err );
                    continue;
                }

                // Standardise all paths to use / (Windows support)
                $filename = str_replace( '\\', '/', $filename );

                if ( ! is_string( $filename ) ) {
                    continue;
                }

                $key =
                    Controller::getValue( 'remote_root' ) ?
                    Controller::getValue( 'remote_root' ) .
                        '/' . ltrim( str_replace( $processed_site_path, '', $filename ), '/' ) :
                    ltrim( str_replace( $processed_site_path, '', $filename ), '/' );

                // TODO: chown chgrp

                // go to home
                $connection->chdir( '/' );

                // for each dir in path, create, own and enter before creating target file
                $dirs_in_path = explode( '/', $key );
                // trim first and last elements
                $dirs_in_path = array_slice( $dirs_in_path, 1, -1 );

                foreach ( $dirs_in_path as $dir ) {
                    $connection->mkdir( $dir );
                    $connection->chdir( $dir );
                }

                if ( $connection->put( basename( $key ), $filename, SFTP::SOURCE_LOCAL_FILE ) ) {
                    error_log( 'put success' );
                    \WP2Static\DeployCache::addFile( $filename );
                } else {
                    \WP2Static\WsLog::l( "sFTP put failed for $key" );
                }
            }
        }
    }
}

