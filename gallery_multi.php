<?php

require '/path/to/gallery/install/gallery.php';

$extraConfig = array(
	'gallery_name' => 'My other gallery',
	'gallery_path' => '/path/to/other/gallery',
);

Config::addExtraConfig($extraConfig);
