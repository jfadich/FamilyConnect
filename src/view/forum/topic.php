<?php
/**
 * Created by John
 * Date: 10/6/14 1:05 AM
 * File: topic.php
 */
?>
<h1><?= $topic_title; ?></h1>
<div class="panel panel-primary">
    <div class="panel-heading">
        <small>Posted by <?= $created_by; ?> on <?= $created_on; ?></small>
    </div>
    <div class="panel-body">
        <?= $topic_content; ?>
    </div>
</div>