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

    private $limit = 10;

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

    protected function set_page($page = 1, $limit = 10)
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

        $type = explode( " ", $sql )[ 0 ];

        // parameters, if any
        $parameters = array_slice( func_get_args(), 1 );

        if ( $this->paginate ) {
            $offset = ( $this->page - 1 ) * $this->limit;
            $sql    = $sql . " LIMIT " . $offset . ", $this->limit";
        }

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
            if ( $type == "SELECT" )
                $rows = $statement->fetchAll( PDO::FETCH_ASSOC );
            else
                $rows = $statement->fetch( PDO::FETCH_NUM );

            return $rows;
        }
        else {
            if ( $this->is_transaction )
                $this->handle->rollBack();
            return false;
        }
    }

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