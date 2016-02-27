<?php

require_once BASE_PATH . '/main/MediaElement.php';
require_once BASE_PATH . '/main/MediaElementImage.php';
require_once BASE_PATH . '/main/MediaElementVideo.php';

class MediaElementFactory {

	public static function isValid($path) {
		$extension = MediaElement::getExtension($path);
		return (self::isImageExtension($extension) || self::isVideoExtension($extension));
	}

	public static function createMediaElement($path) {
		$element = null;
		if (self::isImage($path)) {
			$element = new MediaElementImage($path);
		} elseif (!Config::getIgnoreVideos() && self::isVideo($path)) {
			$element = new MediaElementVideo($path);
		}
		return $element;
	}

	public static function isImage($path) {
		$extension = MediaElement::getExtension($path);
		return self::isImageExtension($extension);
	}

	public static function isVideo($path) {
		$extension = MediaElement::getExtension($path);
		return self::isVideoExtension($extension);
	}

	private static function isImageExtension($extension) {
		return (isset(MediaElementImage::$extensions[$extension]));
	}

	private static function isVideoExtension($extension) {
		return (isset(MediaElementVideo::$extensions[$extension]));
	}

}
