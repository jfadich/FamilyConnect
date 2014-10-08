<?php

/**
 * Created By: John
 * Date: 9/15/14 1:51 PM
 * Class to encapsulate all info from a single user
 */
class User extends Model
{
    private $user_table = "users";
    private $meta_table = "user_meta";
    private $new_user = true;
    private $userData;

    function __construct($identifier = 0)
    {
        parent::__construct(); // Establish a connection with the DB

        if ( empty( $identifier ) ) {
            $this->userData = ["uid"         => 0,
                               "user_name"   => "",
                               "user_pass"   => "",
                               "user_email"  => "",
                               "user_perms"  => 0,
                               "user_since"  => null,
                               "is_verified" => "",
                               "user_salt"   => "",
                               "user_meta"   => [
                                   "first_name" => "",
                                   "last_name"  => ""]
            ];
            return false;
        }
        else {
            $this->userData = $this->load_user( $identifier );
            if ( $this->userData )
                $this->new_user = false;
        }

    }

    public function get_uid()
    {
        return $this->userData[ "uid" ];
    }

    public function get_user_name()
    {
        return $this->userData[ "user_name" ];
    }

    public function get_user_pass()
    {
        return $this->userData[ "user_pass" ];
    }

    public function get_user_email()
    {
        return $this->userData[ "user_email" ];
    }

    public function get_user_perms()
    {
        return $this->userData[ "user_perms" ];
    }

    public function get_user_since($format = "F j, Y")
    {
        return date( $format, $this->userData[ "user_since" ] );
    }

    public function is_verified()
    {
        return (boolean)$this->userData[ "is_verified" ];
    }

    public function get_user_salt()
    {
        return $this->userData[ "user_salt" ];
    }

    public function get_first_name()
    {
        return $this->userData[ "user_meta" ][ "first_name" ];
    }

    public function set_user_email($email)
    {
        if ( $this->userData[ "uid" ] == $_SESSION[ "uid" ] || $this->new_user ) // Only allow the current user or admin change user settings. Allow new users  TODO Add admin permissions
        {
            $this->userData[ "user_email" ] = $email;
        }
    }

    /**
     * @param $identifier
     * @return array
     */
    private function load_user($identifier)
    {
        if ( is_numeric( $identifier ) )
            $searchColumn = "uid";
        else if ( is_string( $identifier ) ) {
            if ( filter_var( $identifier, FILTER_VALIDATE_EMAIL ) ) {
                $searchColumn = "user_email";
            }
            else
                $searchColumn = "user_name";
        }

        else
            $searchColumn = "uid";

        $result = $this->query( "SELECT * FROM $this->user_table WHERE $searchColumn = ?", $identifier );

        if ( $result == false )
            return false;
        else
            return $result[ 0 ];
    }

    public function add_user($user_obj)
    {
        $columns = ["user_name", "user_pass", "email"];
        $values  = [$user_obj[ "user_name" ], $user_obj[ "user_pass" ]];

        // TODO separate meta tables from use_obj
        $metaCol    = ["user_id"];
        $metaValues = [1];

        if ( isset( $user_obj[ "user_name" ] ) && isset( $user_obj[ "user_pass" ] ) && isset( $user_obj[ "email" ] ) ) {
            $this->start_transaction();
            $this->insert_row( $columns, $values, $this->$user_table );
            $this->insert_row( $metaCol, $metaValues, $this->$meta_table );
            $this->commit();
        }
        else
            return false;
    }

    public function update_user()
    {
        // TODO Implement update_user();
    }
}