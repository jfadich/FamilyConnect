<?php

/**
 * Created by: John
 * Date: 9/17/14 2:51 PM
 * File: model.class.php
 */
class Model
{

    private static $handle; // db driver

    private $is_transaction = false;

    private $paginate = false;

    private $limit;

    private $page = 1;

    function __construct()
    {
        $this->connectToDB();
    }

    private function connectToDB()
    {
        if ( !isset( $this->handle ) ) {
            try {
                // connect to database
                $this->handle = new PDO( "mysql:dbname=" . DATABASE . ";host=" . DBHOST, DBUSER, DBPASS );

                // ensure that PDO::prepare returns false when passed invalid SQL
                $this->handle->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
            } catch (Exception $e) {
                trigger_error( $e->getMessage(), E_USER_ERROR );
                exit;
            }
        }
    }

    public function start_transaction()
    {
        $this->handle->beginTransaction();
        $this->is_transaction = true;
    }

    public function commit()
    {
        $this->handle->comit();
        $this->is_transaction = false;
    }

    /**
     * @param array $columns
     * @param string $table
     * @param string $condition
     * @param string $limit
     * @return array
     */
    protected function get_rows($columns, $table, $condition, $limit = '')
    {
        ( !empty( $limit ) ) && is_numeric( $limit ) ? $limit = "LIMIT $limit" : $limit = ''; // If the limit is provided and is numeric, prepend the SQL LIMIT command.

        if ( is_array( $columns ) )
            $columns = implode( ",", $columns );

        $sql = "SELECT $columns FROM  $table WHERE $condition $limit"; // TODO fix parametrized query
        print( $sql );
        return $this->query( $sql );
    }

    protected function set_page($limit = 10, $page = 1)
    {
        $this->paginate = true;
        $this->page     = $page;
        $this->limit    = $limit;
    }

    /**
     * Executes SQL statement, possibly with parameters, returning
     * an array of all rows in result set or false on (non-fatal) error.
     */
    protected function query( /* $sql [, ... ] */)
    {
        // SQL statement
        $sql = func_get_arg( 0 );

        // parameters, if any
        $parameters = array_slice( func_get_args(), 1 );


        // prepare SQL statement
        $statement = $this->handle->prepare( $sql );
        if ( $statement === false ) {
            trigger_error( $this->handle->errorInfo()[ 2 ], E_USER_ERROR );
            exit;
        }

        // execute SQL statement
        $results = $statement->execute( $parameters );

        // return result set's rows, if any
        if ( $results !== false ) {
            return $statement->fetchAll( PDO::FETCH_ASSOC );
        }
        else {
            if ( $this->is_transaction )
                $this->handle->rollBack();
            return false;
        }
    }
    // get

    /**
     * @param  array $columns
     * @param  array $values
     * @param  string $table
     * @return bool
     */
    protected function insert_row($columns, $values, $table)
    {
        $columns = implode( ",", $columns );
        $values  = implode( ",", $values );
        $sql     = "INSERT INTO ? ( ? ) VALUES ( ? )";
        return $this->query( $sql, $table, $columns, $values );
    }

} 