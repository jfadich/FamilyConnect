<?php

/**
 * Created by John
 * Date: 9/30/14 8:14 PM
 * File: forum.class.php
 */
Class Forum extends Model
{

    private $category_table = "forum_categories";

    private $topic_table = "forum_topics";

    private $post_table = "forum_posts";

    function _construct()
    {
        parent::__construct();
    }

    public function get_categories($cat_slug = '')
    {
        if ( !empty( $cat_slug ) ) {
            $result = $this->query( "SELECT * FROM $this->category_table WHERE cat_slug = ? ", $cat_slug );
            if ( $result != false )
                $result = $result[ 0 ];
            else
                $result = false;
        }
        else {
            $result = $this->query( "SELECT * FROM $this->category_table" );
        }

        return $result;
    }

    public function get_topics($id = '') // TODO add pagination
    {
        if ( is_numeric( $id ) )
            $searchColumn = "topic_cat";
        else
            $searchColumn = "topic_slug";

        if ( !empty( $id ) ) {
            $result = $this->query( "SELECT * FROM $this->topic_table WHERE $searchColumn = ? ", $id );
        }
        else
            $result = $this->query( "SELECT * FROM $this->topic_table" );

        return $result;
    }

    public function get_posts($topic_id)
    {
        if ( !empty( $topic_id ) ) {
            $result = $this->query( "SELECT * FROM $this->post_table WHERE post_topic = ? ", $topic_id );
        }
        else
            $result = $this->query( "SELECT * FROM $this->post_table" );

        return $result;
    }
    // get post

    // get forumInfo

    // get forumContents

    //
}