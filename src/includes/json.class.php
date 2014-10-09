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
    private $fragment_type = "html"; // html or text

    private $meta = [];

    public function send_page()
    {
        header( "content-type:application/json" );

        if ( $this->fragment_type == "html" ) {

            for ( $i = 0, $count = count( $this->template_fragments ); $i < $count; $i++ ) {
                $name                                    = key( $this->template_fragments[ $i ] );
                $data                                    = $this->template_fragments[ $i ][ $name ];
                $this->template_fragments[ $i ][ $name ] = $this->render_fragment( $name, $data );
            }
        }
        $this->add_fragment( "meta", $this->meta );
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
        $this->meta[ $metaName ] = $metaValue;
    }

    private function render_fragment($templateName, $data = [])
    {

        if ( file_exists( ROOT . "/view/$templateName.php" ) ) {
            ob_start();
            extract( $data ); // extract variables into local scope
            include( ROOT . "/view/$templateName.php" );

            return ob_get_clean();
        }
        else {
            trigger_error( "Missing template file: $templateName", E_USER_WARNING );
        }

    }
}