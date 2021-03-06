<?php

/**
 * Created by John
 * Date: 9/30/14 9:45 PM
 * File: forum.controller.class.php
 */
class ForumController
{

    private $responseType;

    private $screen;

    private $forum;

    private $category = false;

    private $topic = false;

    private $page = 1;

    function __construct($args, OutputHandler $screen)
    {
        $this->responseType = $screen->response_type;
        $this->screen       = $screen;

        $this->forum = new Forum();

        if ( isset( $args[ 1 ] ) && !empty( $args[ 1 ] ) ) {
            $this->category = $this->forum->get_categories( $args[ 1 ] );
            if ( $this->category == false )
                $this->screen->send_404();

            if ( isset( $args[ 2 ] ) && !empty( $args[ 2 ] ) ) {

                if ( isset( $args[ 3 ] ) && !empty( $args[ 3 ] ) )
                    if ( is_numeric( $args[ 3 ] ) )
                        $this->page = $args[ 3 ];
                    else
                        $this->screen->send_404();

                $this->topic = $this->forum->get_topics( $args[ 2 ] );

                if ( $this->topic == false )
                    $this->screen->send_404();
                else
                    $this->topic = $this->topic[ 0 ];

                $this->list_posts();
            }
            else {
                $this->load_category();
            }
        }
        else {
            $this->list_topics();
        }


        if ( $this->responseType == "html" ) {
            $this->screen->add_side_title( "Categories" );
            $categories = $this->forum->get_categories();
            foreach ( $categories as $cat ) {
                $link = SITEURL . "/forum/" . $cat[ "cat_slug" ];
                $this->screen->add_side_link( $cat[ "cat_name" ], $link );
                if ( !empty( $cat_slug ) && $cat[ "cat_slug" ] == $cat_slug ) {
                    $this->screen->add_meta( "title", $cat[ "cat_name" ] );
                }
            }
        }

    }

    private function load_category()
    {
        if ( $this->category == false )
            $this->screen->send_404();

        if ( $this->responseType == "HTML" ) {
            $this->screen->add_meta( "title", $this->category[ "cat_name" ] );
        }
        $this->list_topics();
    }

    private function list_topics()
    {
        global $settings;
        if ( $this->category == false )
            $title = 'All Discussions';
        else
            $title = $this->category[ "cat_name" ];

        $topics = $this->forum->get_topics( $this->category[ "cat_id" ] );
        if ( $topics == false ) {
            $this->screen->add_fragment( "TEXT", "No topics found" );
            return;
        }

        foreach ( $topics as &$topic ) {

            $topic[ "topic_link" ] = SITEURL . "/forum/" . $topic[ "cat_slug" ] . "/" . $topic[ "topic_slug" ];
            $topic[ "created_on" ] = date( $settings[ "dateFormat" ], strtotime( $topic[ "created_on" ] ) );
        }
        $this->screen->add_meta( "title", $title );
        $this->screen->add_fragment( "forum/topic-list", ["topics" => $topics, "title" => $title] );
    }

    private function list_posts()
    {
        global $settings;
        if ( $this->page == 1 ) // Show the original post on the first page
            $this->screen->add_fragment( "forum/topic", $this->topic );

        $posts = $this->forum->get_posts( $this->topic[ "topic_id" ], $this->page );
        $this->screen->add_meta( "nextPage", $this->page + 1 );
        $this->screen->add_meta( "title", $this->topic[ "topic_title" ] );
        if ( $posts == false ) {
            $this->screen->add_fragment( "TEXT", "There doesn't seem to be anything here " );
            // TODO Display reply form
        }
        else {
            foreach ( $posts as $post ) {
                $post[ "posted_on" ] = date( $settings[ "dateFormat" ], strtotime( $post[ "posted_on" ] ) );
                $this->screen->add_fragment( "forum/post", $post );
            }

        }

    }

}