<?php
/**
 * Created by John
 * Date: 10/2/14 11:37 PM
 * File: categories.php
 */
?>
<h1>Discussions</h1>
<table class="table table-striped">
    <tr>
        <th>Category Title</th>
        <th>Description</th>
    </tr>
    <? foreach ( $category as $cat ): ?>
        <tr>
            <td><a href="<?= SITEURL . "/forum/category/" . $cat[ "cat_slug" ]; ?>"><?= $cat[ "cat_name" ]; ?></a></td>
            <td><?= $cat[ "cat_description" ]; ?></td>
        </tr>
    <? endforeach; ?>
</table>
