<?php

require 'gallery.php';
require BASE_PATH . '/controllers/Router.php';
require BASE_PATH . '/main/UrlHandler.php';

UrlHandler::parseUrl();
$args = UrlHandler::getArguments();
Router::route($args);
