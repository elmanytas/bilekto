<?php

require_once BASE_PATH . "/main/ArchiveManager.php";

class ArchiveController extends BaseController {

	function __construct($gallery, $args) {
		$this->gallery = $gallery;
		$this->path = $this->buildSanitizedPath($args);
	}

	function handle() {
		$this->sendCacheHeader();

		$basepath = $this->gallery->getFullPath();
		list($folderList, $fileList) = $this->gallery->getElements($this->path, true);
		if ($fileList === null) {
			# TODO: Add info to X-Debug header
			http_response_code(403);
			echo "ZIP archive too big";
			return;
		}
		$manager = new ArchiveManager($basepath, $this->path, $fileList);

		$archiveName = $manager->getArchiveName();
		$archiveSize = $manager->calculateArchiveSize();

		header('Cache-Control: no-cache no-store');
		header('Content-Type: application/zip');
		header("Content-Disposition: attachment; filename=\"$archiveName.zip\"");
		header("Content-Length: $archiveSize");

		$manager->dumpZIP($fileList);
	}

}
