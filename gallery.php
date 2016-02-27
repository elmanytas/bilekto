<?php

define('BASE_PATH', __DIR__);

require BASE_PATH . '/main/Config.php';
require BASE_PATH . '/main/Gallery.php';

Config::init(BASE_PATH . '/config.ini');
