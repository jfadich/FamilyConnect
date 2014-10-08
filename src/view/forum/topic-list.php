<?php
/**
 * Created by John
 * Date: 9/18/14 2:11 AM
 * File: post-listing.php
 */
?>

<h1><?= $title; ?></h1>


<table class="table table-striped">
    <tr>
        <th>Title</th>
        <th>Posted On</th>

    </tr>
    <? foreach ( $topics as $t ): ?>
        <tr>
            <td><a href="<?= $t[ "topic_link" ]; ?>"><?= $t[ "topic_title" ]; ?></a></td>
            <td><?= $t[ "created_on" ]; ?></td>
        </tr>
    <? endforeach; ?>
</table>
