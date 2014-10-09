<?php

/**
 * created by: John
 * Date: 9/15/14
 * Time: 9:32 PM
 * Assembles the template files.
 */
class Display implements OutputHandler
{
    // TODO Refactor JSON into it's own display class
    public $response_type = "html";
    private $template_fragments = []; // [ [template name], [ [ template_args ] ] ];
    private $breadCrumbs = [["Home" => SITEURL]];
    private $sideNav = [["name" => "Home", "link" => SITEURL]];
    private $sideHeader;
    private $title = "";
    private $headerHTML = "";
    private $footerHTML = "";
    private $widgets = [];
    private $meta;

    /**
     * @param $widgetHTML
     */
    public function add_widget($widgetHTML) // TODO Implement add_widget
    {

    }

    public function add_breadcrumb($name, $link)
    {
        $this->breadCrumbs = [$name => $link];
    }

    public function add_asset($type, $name)
    {
        $fileLocation = ROOT . "/public/assets/" . $type . "/" . $name . "." . $type;
        $fileHREF     = SITEURL . "/assets/" . $type . "/" . $name . "." . $type;

        if ( file_exists( $fileLocation ) ) {
            if ( $type == "js" ) {
                $html = "<script type='text/javascript' src='$fileHREF'></script>";
                $this->footerHTML .= $html;
            }
            else if ( $type == "css" ) {
                $html = "<link rel='stylesheet' href='$fileHREF'>";
                $this->headerHTML .= $html;
            }
        }
    }

    public function send_404()
    {
        // Discard values, recreate page with error template
        unset ( $this->template_fragments );
        header( 'HTTP/1.0 404 Not Found' );

        $this->add_meta( "title", "404" );
        $this->add_fragment( "404" );
        $this->send_page();

        exit;
    }

    public function add_meta($metaName, $metaValue)
    {
        if ( $metaName = "title" )
            $this->set_title( $metaValue );
        else
            $this->meta[ $metaName ] = $metaValue;
    }

    private function set_title($title)
    {
        global $settings;
        $replace     = ["@pageName" => $title,
                        "@siteName" => $settings[ "siteName" ]];
        $this->title = str_replace_assoc( $replace, $settings[ "pageTitle" ] );
    }

    /**
     * @param string $elementName
     * @param array $data
     */
    public function add_fragment($elementName, $data = [])
    {
        if ( empty( $elementName ) ) {
            trigger_error( 'Failed to add element to cache. Unspecified $elementName ', E_USER_NOTICE );
        }
        else {
            $this->template_fragments[ ] = [$elementName, $data];
        }
    }

    public function send_page($withTemplate = true)
    {
        $this->render_page( $withTemplate );
    }

    /**
     *
     */
    private function render_page($withTemplate = true)
    {
        if ( empty( $this->title ) ) {
            global $settings;
            $this->title = $settings[ "siteName" ];
        }
        if ( $withTemplate ) {
            $this->render_fragment( "header", ["title"      => $this->title,
                                               "headerHTML" => $this->headerHTML] );

            $this->render_fragment( "sidebar", ["sideNav" => $this->sideNav, "sideHeader" => $this->sideHeader] );
        }


        for ( $i = 0; $i < count( $this->template_fragments ); $i++ ) {
            $name = $this->template_fragments[ $i ][ 0 ];
            $data = $this->template_fragments[ $i ][ 1 ];
            $this->render_fragment( $name, $data );
        }

        if ( $withTemplate )
            $this->render_fragment( "footer", ["footerHTML" => $this->footerHTML] );

        exit;
    }

    /**
     * Will render a preloaded fragments if only a name is passed. Otherwise it will take a fragmentData object
     *
     * @param string $templateName
     * @param array $templateData
     */
    private function render_fragment($templateName, $templateData = [])
    {
        $data = array();

        // If template data was passed, render it. Else search for the fragment in the cache.
        if ( !empty( $templateData ) ) {
            $data = $templateData;
        }
        else {
            foreach ( $this->template_fragments as $n => $d ) {
                if ( $templateName == $n ) {
                    $data = $d;
                    break;
                }
            }
        }
        if ( $templateName == "TEXT" ) {
            print( "<p>" . $templateData . "</p>" );
        }
        else if ( $templateName == "HTML" ) {
            print( $templateData );
        }

        else if ( file_exists( ROOT . "/view/$templateName.php" ) ) {
            extract( $data ); // extract variables into local scope
            include( ROOT . "/view/$templateName.php" );
        }
        else {
            trigger_error( "Missing template file: $templateName", E_USER_WARNING );
        }

    }

    public function add_side_link($name, $link)
    {
        $this->sideNav[ ] = ["name" => $name, "link" => $link];
    }

    public function add_side_title($title)
    {
        $this->sideHeader = $title;
    }

    public function send_403()
    {

    }

    public function add_menu()
    {
        // TODO implement add_menu
    }
} 