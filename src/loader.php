<?php
/**
 * User: John
 * Date: 9/15/14 12:55 AM
 *  Sets Constants, loads configuration details, initializes core components
 */

require_once( "config.php" );
require_once( "includes/functions.php" );

define( 'ROOT', __DIR__ );

ini_set( "session.use_strict_mode", 1 ); // disallow uninitialized SIDs

spl_autoload_register( "loadClass" );

if ( $settings[ "debug" ] ) {
    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );
}

if ( $settings[ "forceHTTPS" ] ) {
    // TODO force HTTPS
}