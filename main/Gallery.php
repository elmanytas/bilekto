<?php

require_once BASE_PATH . '/main/MediaElementFactory.php';
require BASE_PATH . '/main/Resolution.php';

class Gallery {

	const MAX_FILES = 20000;

	protected $galleryPath = NULL;

	public function __construct() {
		$this->galleryPath = Config::getGalleryPath();
		if (!is_dir($this->galleryPath)) {
			throw new Exception('Gallery directory does not exist');
		}
	}

	public function getTitle() {
		return Config::getGalleryName();
	}

	public function getElements($subdir, $recursive = false) {
		$count = 0;
		$files = array();
		$folders = array();
		$foldersToVisit = array($subdir);

		while ($foldersToVisit) {
			$folder = array_pop($foldersToVisit);
			$fullpath = $this->getFullPath($folder);
			$dd = opendir($fullpath);
			if (!$dd) {
				continue;
			}
			while ($entry = readdir($dd)) {
				if ($entry[0] == '.') {
					continue;
				}
				if (is_dir("$fullpath/$entry")) {
					if ($recursive) {
						$folderpath = self::joinPath($folder, $entry);
						$folders[] = $folderpath;
						$foldersToVisit[] =  $folderpath;
					} else {
						$folders[] = $entry;
					}
				} else {
					if (MediaElementFactory::isValid($entry)) {
						if ($recursive) {
							$files[] = self::joinPath($folder, $entry);
						} else {
							$files[] = $entry;
						}
						$count += 1;
					}
				}
				if ($count > self::MAX_FILES) {
					return array(null, null);
				}
			}
			closedir($dd);
		}
		sort($folders);
		sort($files);

		return array($folders, $files);
	}

	public function isReverseOrder($subdir) {
		return file_exists($this->getFullPath($subdir) . '/.reverse');
	}

	public function getFullPath($subpath = '') {
		return self::joinPath($this->galleryPath, $subpath);
	}

	public static function joinPath($path1, $path2) {
		$joined = '';
		if ($path1 != '' && $path1 != '.') {
			$joined = $path1 . '/';
		}
		$joined .= $path2;
		return $joined;
	}

}
