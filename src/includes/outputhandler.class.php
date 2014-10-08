<?php

/**
 * Created by John
 * Date: 9/19/14 3:04 AM
 * File: ResponseBuilder.php
 */
Interface OutputHandler
{

    function send_page();

    function send_404();

    function send_403();

    function add_fragment($elementName, $data = []);

    function add_meta($metaName, $metaValue);

} 