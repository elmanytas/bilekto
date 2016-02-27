<?php

require_once BASE_PATH . '/main/ArchiveManager.php';

class ViewGalleryController extends BaseController {

	function __construct($gallery, $args) {
		$this->gallery = $gallery;
		$this->subdir = $this->buildSanitizedPath($args);
	}

	public function handle() {
		$this->sendCacheHeader();

		$title = $this->gallery->getTitle();
		$this->render('PageHeader', array(
			'static_url' => UrlHandler::getStaticPath(),
			'title' => $title,
			'gallery' => true
		));

		$downloadLink = null;
		$archiveSize = null;
		if (Config::getAllowDownloads()) {
			$downloadLink = UrlHandler::buildLink('archive/' . $this->subdir);
			if (Config::getEstimateArchiveSize()) {
				list($folderList, $fileList) = $this->gallery->getElements($this->subdir, true);
				if (empty($fileList)) {
					// Disable the download link if there are no files or the max number of files is exceeded
					$downloadLink = null;
				} else {
					$archiveManager = new ArchiveManager($this->gallery->getFullPath(), $this->subdir, $fileList);
					$archiveSize = $this->getHumanSize($archiveManager->calculateArchiveSize());
				}
			}
		}

		list($folderList, $fileList) = $this->gallery->getElements($this->subdir);
		if ($this->gallery->isReverseOrder($this->subdir)) {
			rsort($fileList);
			rsort($folderList);
		} else {
			sort($fileList);
			sort($folderList);
		}

		$folderElems = array();
		$fileElems = array();

		foreach ($folderList as $f) {
			$filename = basename($f);
			$relative = Gallery::joinPath($this->subdir, $filename);
			$folderElems[] = array(
				'name' => basename($filename),
				'dest' => UrlHandler::buildLink('view/' . $relative),
			);
		}

		$slideResolution = Config::getSlideResolution();
		$thumbResolutions = Config::getThumbResolutions();
		$availableThumbs = array();
		foreach ($thumbResolutions as $resolution) {
			$availableThumbs[] = $resolution->toString();
		}
		$availableThumbs = implode(' ', $availableThumbs);

		foreach ($fileList as $f) {
			$filename = basename($f);
			$relativePath = Gallery::joinPath($this->subdir, $filename);
			$media = MediaElementFactory::createMediaElement($this->gallery->getFullPath($relativePath));

			$fullUrl = UrlHandler::buildLink('get/' . $relativePath);
			$thumbUrl = UrlHandler::buildLink('thumb/{{thumb_size}}/' . $relativePath);

			$params = array(
				'name' => basename($filename),
				'slideshow' => false,
				'full_url' => $fullUrl,
				'thumb_url' => $thumbUrl,
			);

			if (MediaElementFactory::isImage($this->gallery->getFullPath($relativePath))) {
				list($origWidth, $origHeight) = $media->getDimensions();
				if ($slideResolution->format == Resolution::FORMAT_ORIGINAL) {
					$slideWidth = $origWidth;
					$slideHeight = $origHeight;
					$slideUrl = $fullUrl;
				} else {
					list($slideWidth, $slideHeight) = $slideResolution->calculateResizedResolution($origWidth, $origHeight);
					$slideUrl = UrlHandler::buildLink('thumb/' . $slideResolution->toString() . '/' . $relativePath);
				}

				$params['slideshow'] = true;
				$params['slide_url'] = $slideUrl;
				$params['slide_width'] = $slideWidth;
				$params['slide_height'] = $slideHeight;
			}

			$fileElems[] = $params;
		}

		$this->render('GalleryView', array(
			'static_url' => UrlHandler::getStaticPath(),
			'folders' => $folderElems,
			'files' => $fileElems,
			'thumb_sizes' => $availableThumbs,
			'navlinks' => $this->buildNavigationLinks(),
			'downloadLink' => $downloadLink,
			'archiveSize' => $archiveSize,
		));

		$this->render('Footer', array(
			'static_url' => UrlHandler::getStaticPath(),
			'version' => Config::VERSION,
		));

		return;
	}

	protected function buildNavigationLinks() {
		$links = array();

		$components = explode('/', $this->subdir);
		$current = array_pop($components);
		if ($current) {
			$links['current'] = array(
				'name' => $current
			);

			$parentName = end($components) ?: $this->gallery->getTitle();
			$parentSubdir = implode('/', $components);
			if ($parentSubdir !== "") {
				$parentSubdir = $parentSubdir;
			}
			$links['parent'] = array(
				'name' => $parentName,
				'link' => UrlHandler::buildLink('view/' . $parentSubdir),
			);

			list($siblings, $_) = $this->gallery->getElements($parentSubdir);
			$position = array_search($current, $siblings);
			if (isset($siblings[$position-1])) {
				$links['previous'] = array(
					'name' => $siblings[$position-1],
					'link' => UrlHandler::buildLink('view/' . $parentSubdir . '/' . $siblings[$position-1]),
				);
			}
			if (isset($siblings[$position+1])) {
				$links['next'] = array(
					'name' => $siblings[$position+1],
					'link' => UrlHandler::buildLink('view/' . $parentSubdir . '/' . $siblings[$position+1]),
				);
			}
		} else {
			$links['current'] = array(
				'name' => $this->gallery->getTitle(),
			);
		}
		return $links;
	}

	protected function getHumanSize($size) {
		if ($size<1000) {
			$human = "$size bytes";
		} else if ($size<1000000) {
			$kb = round($size / 1024);
			$human = "$kb Kbytes";
		} else if ($size<1000000000) {
			$mb = round($size / 1048576);
			$human = "$mb Mbytes";
		} else {
			$gb = sprintf("%.2f", ($size / 1073741824));
			$human = "$gb Gbytes";
		}
		return $human;
	}

}
