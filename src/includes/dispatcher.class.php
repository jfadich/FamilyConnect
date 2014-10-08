<?php

/**
 * Created by: John
 * Date: 9/17/14 2:56 AM
 */
class Dispatcher
{

    private $screen;
    private $current_user;
    private $public_pages = ["auth"];
    private $response_type = "html";
    private $auth;

    function __construct()
    {
        $this->auth = new Auth();
        $mod        = $this->parse_uri( $_SERVER[ "REQUEST_URI" ] );;

        if ( $this->response_type == "json" )
            $this->screen = new JSON();
        else
            $this->screen = new Display();

        if ( !empty( $mod[ "modArgs" ][ 0 ] ) && $mod[ "modArgs" ][ 0 ] == "login" ) { // TODO Refactor authorization
            if ( isset( $_POST[ "email" ] ) ) {
                $status = $this->auth->login( $_POST[ "email" ], $_POST[ "password" ], isset( $_POST[ "remember-me" ] ) ); // Error Returns 1 => user not found, 2 => user not verified, 3 => wrong password

                if ( $status == 1 || $status == 3 ) {
                    $json[ "message" ] = "Invalid username or password" . $status;
                }
                else if ( $status == 2 ) {
                    $json[ "message" ] = "Email not validated"; // TODO include link to resend verification email
                }
                else {
                    $json[ "message" ] = "ok";
                    if ( isset( $_SESSION[ "loginRedirect" ] ) ) {
                        $json[ "redirect" ] = $_SESSION[ "loginRedirect" ];
                        unset( $_SESSION[ "loginRedirect" ] );
                    }
                }
                header( "content-type:application/json" );
                print( json_encode( $json ) );
                exit;
            }
            else {

                $this->screen->add_meta( "title", "Login" );
                if ( isset( $_SESSION[ "loginMessage" ] ) ) {
                    $message = $_SESSION[ "loginMessage" ];
                    unset ( $_SESSION[ "loginMessage" ] );
                }
                else
                    $message = '';
                $this->screen->add_fragment( "sign_in", ["title" => "Log in", "message" => $message] );
                $this->screen->send_page( false );
            }
        }
        else if ( !empty( $mod[ "modArgs" ][ 0 ] ) && $mod[ "modArgs" ][ 0 ] == "logout" ) {
            $this->auth->logout();
            session_start();
            $_SESSION[ "loginMessage" ] = "Logged out successfully";
            header( "Location:" . SITEURL . "/login" );
        }

        else if ( !in_array( $mod[ "modName" ], $this->public_pages ) && !$this->auth->logged_in() ) {
            $_SESSION[ "loginMessage" ]  = "You need to be signed in to do that.";
            $_SESSION[ "loginRedirect" ] = SITEURL . $_SERVER[ "REQUEST_URI" ];
            header( 'HTTP/1.0 403 Forbidden' );
            header( "Location:" . SITEURL . "/login" );
            exit;
        }
        if ( isset( $_SESSION[ "uid" ] ) ) {
            $user = new User( (int)$_SESSION[ "uid" ] );
            if ( $user->get_uid() != 0 )
                $this->current_user = $user;
            else {
                $_SESSION[ "loginMessage" ] = "Invalid session.";
                header( 'HTTP/1.0 403 Forbidden' );
                header( "Location:" . SITEURL . "/login" );
                exit;
            }

        }

        if ( $mod[ "modName" ] == "404" ) {
            $this->screen->send_404();
        }

        $this->load_mod( $mod );
        $this->screen->send_page();
    }

    /**
     * @param $request $_SERVER['REQUEST_URI']
     * @return ["moduleName", ["moduleArgs"] ]
     */
    private function parse_uri($request)
    {

        // TODO dynamically load valid slugs
        $specialSlugs = [ // Special slugs allow multiple URL paths to redirect to an module. Also used to alias modules
            "logout"   => "auth",
            "login"    => "auth",
            "register" => "auth"
        ];
        $safeSlugs    = ["photos", "messages", "admin", "forum"];

        $nodes = explode( "/", $request );

        $json = array_search( ".json", $nodes );
        if ( $json != false ) {
            $this->response_type = "json";
            unset ( $nodes[ $json ] );
        }

        if ( strpos( $request, "?q=" ) !== false ) // Search
        {
            $data[ "modName" ] = ["search"];
            $data[ "modArgs" ] = [$nodes[ 1 ]]; // limit search to current module data. Global search if empty.
        }
        else if ( empty( $nodes[ 1 ] ) ) { // Root of the site.
            $data[ "modName" ] = "activity";
            $data[ "modArgs" ] = [];
        }
        else if ( in_array( $nodes[ 1 ], $safeSlugs ) ) {
            $data[ "modName" ] = $nodes[ 1 ];
            $data[ "modArgs" ] = array_splice( $nodes, 1 ); // only return the data after the module name
        }
        else // Check for modules that use a different or multiple slugs
        {
            foreach ( $specialSlugs as $slug => $modName ) {
                if ( $slug == $nodes[ 1 ] ) {
                    $data[ "modName" ] = $modName;
                    $data[ "modArgs" ] = array_splice( $nodes, 1 ); // only return the data after the module name
                    break;
                }
            }
        }

        if ( isset ( $data[ "modName" ] ) == false ) {
            $data[ "modName" ] = "404";
        }
        return $data;
    }

    /**
     * Load Module : module will have access to $this, $modArgs, and $modName;
     *
     * @param $modData an object created by parse_uri
     */
    private function load_mod($modData) // TODO design decision load file or class?
    {
        $modName = $modData[ "modName" ];
        $modArgs = $modData[ "modArgs" ]; // Module file will have access to this information

        if ( file_exists( ROOT . "/includes/" . $modName . ".controller.php" ) ) {
            include_once( ROOT . "/includes/" . $modName . ".controller.php" );
            $className = ucfirst( $modName ) . "Controller";
            new $className( $modArgs, $this->screen );
        }
        else {
            trigger_error( "unable to load module: $modName", E_USER_ERROR );
        }
    }
}


