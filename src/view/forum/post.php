<?php
/**
 * Created by John
 * Date: 10/6/14 1:07 AM
 * File: post.php
 */
?>

<div class="panel panel-default">
    <div class="panel-body">
        <?= $post_content; ?>
    </div>
    <div class="panel-footer">
        <small>Posted by <?= $posted_by; ?> on <?= $posted_on; ?></small>
    </div>
</div>