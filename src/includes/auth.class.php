<?php

/**
 * Created by John
 * Date: 9/21/14 2:22 AM
 * File: auth.class.php
 */
Final class Auth
{
    private $siteKey = "12345"; // TODO  dynamically generate at install

    private $savedSession;

    function __construct()
    {
        $this->savedSession = new SessionInfo();
        $this->start_session();
    }

    private function start_session()
    {
        session_start();
        if ( isset( $_COOKIE[ "accessToken" ][ "sid" ] ) && !isset( $_SESSION[ "uid" ] ) ) {
            if ( $this->savedSession->read( $_COOKIE[ "accessToken" ][ "sid" ] ) ) {

                if ( $_COOKIE[ "accessToken" ][ "uid" ] == $this->savedSession->user_id && $_COOKIE[ "accessToken" ][ "token" ] == $this->savedSession->token ) {
                    $_SESSION[ "uid" ]       = $this->savedSession->user_id;
                    $user                    = new User( $_SESSION[ "uid" ] );
                    $_SESSION[ "user_name" ] = $user->get_user_name();
                    $this->refresh_auth_token();
                }
                else {
                    $this->logout();
                    session_start();
                }
            }
            else {
                $this->logout();
                session_start();
            }
        }
    }

    private function refresh_auth_token()
    {
        $this->savedSession->token = $this->create_token();
        $expires                   = strtotime( " + 14 days" );
        setcookie( "accessToken[uid]", $this->savedSession->user_id, $expires );
        setcookie( "accessToken[sid]", $this->savedSession->id, $expires );
        setcookie( "accessToken[token]", $this->savedSession->token, $expires );
        return $this->savedSession->write();
    }

    private function create_token()
    {
        return $this->create_hash( $this->create_salt( 128 ) );
    }

    private function create_hash($data)
    {
        return hash_hmac( "sha512", $data, $this->siteKey );
    }

    private function create_salt($length = 50)
    {
        return mcrypt_create_iv( $length, MCRYPT_DEV_URANDOM );
    }

    public function logout()
    {
        if ( isset( $_COOKIE[ "accessToken" ][ "sid" ] ) ) {
            if ( !isset( $this->savedSession ) )
                $this->savedSession = new SessionInfo();

            if ( $this->savedSession->read( $_COOKIE[ "accessToken" ][ "sid" ] ) )
                $this->savedSession->delete();

            unset( $_COOKIE[ "accessToken" ][ "uid" ] );
            unset( $_COOKIE[ "accessToken" ][ "sid" ] );
            unset( $_COOKIE[ "accessToken" ][ "token" ] );
            setcookie( "accessToken[uid]", null, time() - 3600 );
            setcookie( "accessToken[sid]", null, time() - 3600 );
            setcookie( "accessToken[token]", null, time() - 3600 );
        }

        session_destroy();
        $_SESSION = [];

    }

    public function login($userName, $password, $remember = false) // Error Returns 1 => user not found, 2 => user not verified, 3 => wrong password
    {
        $user = new User( $userName );

        if ( $user == false )
            return 1; // user not found

        $match    = $this->check_password( $password, $user->get_user_salt(), $user->get_user_pass() );
        $verified = $user->is_verified();

        if ( $match ) {
            if ( $verified ) {
                $_SESSION[ 'uid' ]       = $user->get_uid();
                $_SESSION[ "user_name" ] = $user->get_user_name();
                if ( $remember ) {
                    $this->savedSession->user_id = $user->get_uid();
                    $this->savedSession->token   = $this->create_token();
                    $this->savedSession->id      = $this->create_token();
                    $expires                     = strtotime( " + 14 days" );
                    $this->savedSession->write();
                    // set cookie
                    setcookie( "accessToken[uid]", $this->savedSession->user_id, $expires );
                    setcookie( "accessToken[sid]", $this->savedSession->id, $expires );
                    setcookie( "accessToken[token]", $this->savedSession->token, $expires );
                }

            }
            else {
                // Not verified
                return 2;
            }
        }
        else {
            // Password Mismatch
            //TODO Throttle logging in after X number of failed attempts
            return 3;
        }

    }

    private function check_password($pass, $salt, $knownHash)
    {
        $sameString = 1;
        $hash       = $this->create_hash( $pass . $salt );
        $passLength = strlen( $hash );

        if ( $passLength != strlen( $knownHash ) )
            return false;
        else {
            for ( $c = 0; $c < $passLength; $c++ ) {
                if ( $knownHash[ $c ] != $hash[ $c ] )
                    $sameString = 0;
            }
        }

        return $sameString;
    }

    public function create_password($pass)
    {
        $salt                 = $this->create_salt();
        $return[ "password" ] = $this->create_hash( $pass . $salt );
        $return[ "salt" ]     = $salt;
        return $return;
    }

    public function logged_in()
    {
        return isset( $_SESSION[ 'uid' ] );
    }

    public function authenticate()
    {

    }
}