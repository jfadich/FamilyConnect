<?php

/**
 * Created by John
 * Date: 10/1/14 1:28 AM
 * File: json.class.php
 */
class JSON implements OutputHandler
{
    public $response_type = "json";
    private $template_fragments = [];

    private $meta = [];

    public function send_page()
    {
        header( "content-type:application/json" );
        print( json_encode( $this->template_fragments, JSON_UNESCAPED_SLASHES ) );
        exit;
    }

    public function send_404()
    {
        header( 'HTTP/1.0 404 Not Found' );
        exit;
    }

    public function send_403()
    {

    }

    public function add_fragment($elementName, $data = [])
    {
        if ( empty( $elementName ) ) {
            trigger_error( 'Failed to add element to cache. Unspecified $elementName ', E_USER_NOTICE );
        }
        else {
            $this->template_fragments[ ] = [$elementName => $data];
        }
    }

    public function add_meta($metaName, $metaValue)
    {

    }
}