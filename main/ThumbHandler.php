<?php

class ThumbHandler {

	public static function getThumbnail($path, $resolution) {
		$media = MediaElementFactory::createMediaElement($path);

		$caching = Config::getCachingConfig();
		if ($caching == 'disabled') {
			$thumb = $media->generateThumbnail($resolution);
		} else {
			$thumb = self::getCachedThumbnail($resolution, $media, $caching);
		}
		return $thumb;
	}

	private static function getCachedThumbnail($resolution, $media, $caching) {
		$mediaFile = $media->getFilename();
		$path = dirname($mediaFile);
		$filename = basename($mediaFile);

		$cachedDir = '.thumb-' . $resolution->toString();
		$cachedFile = $path . '/' . $cachedDir . '/' . $filename;

		// If it's cached, return the cached version
		if ($caching != 'wo' && file_exists($cachedFile)) {
			$stat = stat($mediaFile);
			$origts = $stat['mtime'];
			$stat = stat($cachedFile);
			$cachedts = $stat['mtime'];
			$cachedsize = $stat['size'];

			if ($cachedts >= $origts && $cachedsize > 0) {
				return file_get_contents($cachedFile);
			}
		}

		$thumb = $media->generateThumbnail($resolution);

		if ($caching == 'rw' || $caching == 'wo') {
			$cachedDir = dirname($cachedFile);
			$lvl = error_reporting();
			error_reporting(E_ERROR | E_PARSE);
			umask(0002);
			if (!is_dir($cachedDir)) { mkdir($cachedDir); }
			$c = fopen($cachedFile, 'w');
			if ($c !== false) {
				fwrite($c, $thumb);
				fclose($c);
			}
			error_reporting($lvl);
		}

		return $thumb;
	}

}
