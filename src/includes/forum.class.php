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

    private $user_table = "user_meta";

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

    public function get_topics($id = '', $page = 1)
    {
        $this->set_page( $page );
        if ( is_numeric( $id ) )
            $searchColumn = "topic_cat";
        else
            $searchColumn = "topic_slug";

        if ( !empty( $id ) ) {
            $result = $this->query( "SELECT topic_id,topic_title,topic_content,topic_slug,topic_cat,created_on,nice_name,cat_name,cat_slug
                                     FROM $this->topic_table
                                     INNER JOIN $this->user_table ON created_by = user_id
                                     INNER JOIN $this->category_table ON topic_cat = cat_id
                                     WHERE $searchColumn = ? ", $id );
        }
        else
            $result = $this->query( "SELECT topic_id,topic_title,topic_content,topic_slug,topic_cat,created_on,nice_name,cat_name,cat_slug
                                     FROM $this->topic_table
                                     INNER JOIN $this->user_table ON created_by = user_id
                                     INNER JOIN $this->category_table ON topic_cat = cat_id" );

        return $result;
    }

    public function get_posts($topic_id, $page = 1)
    {
        $this->set_page( $page );
        if ( !empty( $topic_id ) ) {
            $result = $this->query( "SELECT post_content,
                                     posted_on,
                                     nice_name
                                     FROM $this->post_table
                                     INNER JOIN $this->user_table ON posted_by = user_id WHERE post_topic = ? ", $topic_id );
        }
        else
            $result = $this->query( "SELECT post_content,
                                     posted_on,
                                     nice_name
                                     FROM $this->post_table" );

        return $result;
    }
}