<?php
/**
 * Created By: John
 * Date: 9/15/14 1:23 AM
 *
 * Utility functions
 */

/**
 * @param  string $className
 * Automatically loads class file if not already loaded.
 */
function loadClass($className)
{
    $file = ROOT . "/includes/" . strtolower( $className ) . ".class.php";

    if ( file_exists( $file ) ) {
        include_once( $file );
    }
    else {
        trigger_error( "unable to load class: $className", E_USER_ERROR );
    }
}

function current_user()
{
    if ( isset( $_SESSION[ "uid" ] ) ) {
        $user = new User( $_SESSION[ "uid" ] );
        if ( $user != false ) {
            return $user;
        }
        else {
            return false;
        }
    }
    return false;
}

function str_replace_assoc(array $replace, $subject)
{
    return str_replace( array_keys( $replace ), array_values( $replace ), $subject );
}