<?php
# Syntax:
# php pregenerate-cache.php [--force]
#   --force   Force regeneration of all cached files even if they already exist

require 'gallery.php';
require 'main/ThumbHandler.php';

$cachingMode = 'rw';
if (isset($argv[1]) && $argv[1] == '--force') {
	$cachingMode = 'wo';
}

$extraConfig = array(
	'caching' => $cachingMode,
);
Config::addExtraConfig($extraConfig);

$gallery = new Gallery();

generateThumbnails($gallery, '');

function generateThumbnails($gallery, $subdir) {
	list($folders, $elems) = $gallery->getElements($subdir);
	foreach ($elems as $e) {
		$fullPath = $gallery->getFullPath("$subdir/$e");
		echo "$fullPath";
		$resolutions = Config::getThumbResolutions();
		$resolutions[] = Config::getSlideResolution();
		foreach ($resolutions as $res) {
			echo " " . $res->toString();
			$thumb = ThumbHandler::getThumbnail($fullPath, $res);
		}
		echo "\n";
	}
	foreach ($folders as $f) {
		generateThumbnails($gallery, "$subdir/$f");
	}
}
