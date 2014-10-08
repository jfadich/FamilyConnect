<?php
/**
 * Created by: John
 * Date: 9/17/14 8:59 PM
 * File: sidebar.php
 */
?>

<!-- sidebar -->
<div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
    <? if ( isset( $sideHeader ) ) : ?> <h3> <?= $sideHeader; ?></h3> <? endif; ?>
    <ul class="nav">

        <? foreach ( $sideNav as $link ): ?>
            <li><a href="<?= $link[ "link" ]; ?>"><?= $link[ "name" ]; ?></a></li>
        <? endforeach; ?>

    </ul>
</div>

<!-- main area -->
<div class="col-xs-12 col-sm-9">