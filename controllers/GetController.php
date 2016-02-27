<?php

class GetController extends BaseController {

	function __construct($gallery, $args) {
		$this->gallery = $gallery;
		$this->path = $this->buildSanitizedPath($args);
	}

	function handle() {
		$this->sendCacheHeader();

		$filePath = $this->gallery->getFullPath($this->path);
		$media = MediaElementFactory::createMediaElement($filePath);
		$contentType = $media->getContentType();
		header('Content-type: ' . $contentType);
		$contentLength = filesize($filePath);
		header('Content-length: ' . $contentLength);

		// Cleanup the output buffer so that readfile doesn't fail by OOM
		while (ob_get_level()) {
			ob_end_clean();
		}
		readfile($filePath);
	}
}
