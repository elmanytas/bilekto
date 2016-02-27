<?php

require BASE_PATH . "/main/ThumbHandler.php";

class ThumbController extends BaseController {

	protected $caching = true;

	private $gallery;
	private $resolutionString;
	private $path;
	private $type;

	function __construct($gallery, $args) {
		$this->gallery = $gallery;
		$this->resolutionString = array_shift($args);
		$this->path = $this->buildSanitizedPath($args);
	}

	function handle() {
		$fullPath = $this->gallery->getFullPath($this->path);

		try {
			$resolution = Resolution::createFromComplexString($this->resolutionString);
		} catch (Exception $e) {
			# TODO: Add info to X-Debug header
			http_response_code(400);
			return;
		}

		if (!$this->isAllowedResolution($resolution)) {
			# TODO: Add info to X-Debug header
			http_response_code(403);
			return;
		}

		$thumb = ThumbHandler::getThumbnail($fullPath, $resolution);

		$this->sendCacheHeader();
		header('Content-type: image/jpeg');
		header('Content-length: ' . strlen($thumb));
		echo $thumb;
	}

	private function isAllowedResolution($resolution) {
		$allowedResolutions = Config::getThumbResolutions();
		$allowedResolutions[] = Config::getSlideResolution();
		foreach ($allowedResolutions as $allowed) {
			if ($resolution == $allowed) {
				return true;
			}
		}
		return false;
	}

}
