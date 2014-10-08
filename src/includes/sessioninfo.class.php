<?php

/**
 * Created by John
 * Date: 9/20/14 9:40 PM
 * File: sessioninfo.class.php
 */
class SessionInfo Extends Model
{


    public $id;

    public $token;

    public $user_id;

    private $session_table = "users_logged_in";

    private $new_session = true;

    function __construct()
    {
        parent::__construct();
    }

    public function read($sid)
    {
        $result = $this->query( "SELECT * FROM $this->session_table WHERE session_id = ? AND last_updated <= TIMESTAMPADD(DAY, 14, now())", $sid ); // TODO create maintenance to remove expires sessions
        if ( $result == false ) {
            return false;
        }
        else {
            $result            = $result[ 0 ];
            $this->user_id     = $result[ "user_id" ];
            $this->id          = $result[ "session_id" ];
            $this->token       = $result[ "token" ];
            $this->new_session = false;
            return true;
        }
    }

    public function write()
    {
        if ( isset( $this->id ) && isset( $this->token ) && isset( $this->user_id ) ) {
            if ( $this->new_session ) {
                $this->query( "INSERT INTO $this->session_table(user_id, session_id, token, user_agent, user_ip) VALUES(? , ?, ?, ?, ?)", $this->user_id, $this->id, $this->token, $_SERVER[ "HTTP_USER_AGENT" ], $_SERVER[ "REMOTE_HOST" ] );
                $this->new_session = false;
            }
            else
                $this->query( "UPDATE $this->session_table SET token = ? WHERE session_id = ?", $this->token, $this->id );
        }
        else
            return false;
    }

    public function delete()
    {
        $result = $this->query( "DELETE FROM $this->session_table WHERE session_id = ?", $this->id );

        $this->id      = null;
        $this->token   = null;
        $this->user_id = null;
        return $result;
    }
} 