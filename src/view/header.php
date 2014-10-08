<?php
/**
 * Created by: John
 * Date: 9/16/14 7:19 PM
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title><?= $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link href="<?= SITEURL; ?>/assets/css/styles.css" rel="stylesheet">
    <?= $headerHTML; ?>
</head>
<body>
<div class="page-container">
    <!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Fadich Family</a>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="/">Activity Feed</a></li>
                    <li><a href="/forum">Discussions</a></li>
                    <li><a href="#">Photos</a></li>
                    <li><a href="#">Documents</a></li>
                    <li><a href="#">Inbox</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle"
                           data-toggle="dropdown"><?= current_user()->get_user_name(); ?><span
                                class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?= SITEURL; ?>/logout">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>

    <div class="container">
        <div class="row row-offcanvas row-offcanvas-left">
