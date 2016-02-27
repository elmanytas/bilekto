<?php

class Config {
	const VERSION = '1.0';

	static private $defaults = array(
		'allow_downloads' => true,
		'estimate_archive_size' => true,
		'gallery_name' => 'Image Gallery',
		'gallery_path' => 'images/',
		'url_mode' => 'query_string',
		'ignore_videos' => false,
		'exif_thumbnails' => false,
		'caching' => 'disabled',
		'thumb_format' => 'croptofit',
		'thumb_resolutions' => array('360x360'),
		'thumb_quality' => 80,
		'slideshow_resolution' => '1920x1920',
		'slideshow_quality' => 80,
	);

	static private $config = array();

	static function init($configFile) {
		if (file_exists($configFile)) {
			self::$config[] = self::parseConfigFile($configFile);
		}
		self::$config[] = self::$defaults;
	}

	static function parseConfigFile($configFile) {
		$config = parse_ini_file($configFile);
		// Adding typing to the ini strings
		foreach ($config as $k => $v) {
			($v == 'true') && $config[$k] = true;
			($v == 'false') && $config[$k] = false;
			(is_numeric($v)) && $config[$k] = (int)$v;
		}
		return $config;
	}

	static function addExtraConfig($extraConfig) {
		array_unshift(self::$config, $extraConfig);
	}

	static function getUrlMode() {
		$urlModeString = self::getConfigValue('url_mode');
		switch ($urlModeString) {
			case 'path_info':
				$urlMode = UrlHandler::URL_MODE_PATH_INFO; break;
			case 'rewrite':
				$urlMode = UrlHandler::URL_MODE_REWRITE; break;
			default:
				# TODO: Add debug information (we are falling back to a default value)
			case 'query_string':
				$urlMode = UrlHandler::URL_MODE_QUERY_STRING; break;
		}
		return $urlMode;
	}

	static function getAllowDownloads() {
		return self::getConfigValue('allow_downloads');
	}

	static function getEstimateArchiveSize() {
		return self::getConfigValue('estimate_archive_size');
	}

	static function getGalleryName() {
		return self::getConfigValue('gallery_name');
	}

	static function getGalleryPath() {
		return self::getConfigValue('gallery_path');
	}

	static function getCachingConfig() {
		return self::getConfigValue('caching');
	}

	static function getIgnoreVideos() {
		return self::getConfigValue('ignore_videos');
	}

	static function useExifThumbnails() {
		return self::getConfigValue('exif_thumbnails');
	}

	static function getThumbResolutions() {
		$resolutionStrings = self::getConfigValue('thumb_resolutions');
		$quality = self::getThumbQuality();
		$formatString = self::getThumbFormat();
		switch ($formatString) {
			case 'letterbox':
				$format = Resolution::FORMAT_LETTERBOX; break;
			default:
				# TODO: Add debug information (we are falling back to a default value)
			case 'croptofit':
				$format = Resolution::FORMAT_CROPTOFIT; break;
		}

		$resolutions = array();
		foreach ($resolutionStrings as $resolutionString) {
			$resolutions[] = Resolution::createFromBasicString($resolutionString, $quality, $format);
		}
		return $resolutions;
	}

	static function getSlideResolution() {
		$resolutionString = self::getConfigValue('slideshow_resolution');
		if ($resolutionString == 'original') {
			$resolution = Resolution::createOriginalResolution();
		} else {
			$quality = self::getSlideQuality();
			$resolution = Resolution::createFromBasicString($resolutionString, $quality, Resolution::FORMAT_RESIZE);
		}
		return $resolution;
	}

	static function getThumbFormat() {
		return self::getConfigValue('thumb_format');
	}

	static function getThumbQuality() {
		return self::getConfigValue('thumb_quality');
	}

	static function getSlideQuality() {
		return self::getConfigValue('slideshow_quality');
	}

	static private function getConfigValue($value) {
		foreach (self::$config as $c) {
			if (isset($c[$value])) {
				return $c[$value];
			}
		}
		return null;
	}
}
