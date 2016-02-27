<?php

require_once BASE_PATH . "/main/ImageHelper.php";

class MediaElementImage extends MediaElement {

	private $height, $width;
	private $exifInfo = null;

	const IMAGE_JPEG = 1;
	const IMAGE_PNG = 2;
	const IMAGE_TIFF = 3;

	public static $extensions = array(
		'jpg' => self::IMAGE_JPEG,
		'jpeg' => self::IMAGE_JPEG,
		'png' => self::IMAGE_PNG,
		'tif' => self::IMAGE_TIFF,
		'tiff' => self::IMAGE_TIFF,
	);

	protected $contentTypes = array(
		self::IMAGE_JPEG => 'image/jpeg',
		self::IMAGE_PNG => 'image/png',
		self::IMAGE_TIFF => 'image/tiff',
	);

	public function getDimensions() {
		list($width, $height) = $this->getResolution();
		$exifInfo = $this->getExifInfo();

		if (isset($exifInfo['orientation']) && $exifInfo['orientation'] > 4) {
			list($width, $height) = array($height, $width);
		}

		return array($width, $height);
	}

	public function generateThumbnail($resolution) {
		$exifInfo = $this->getExifInfo();

		if ($resolution->format == 'original') {
			return file_get_contents($this->filename);
		}

		$targetWidth = $resolution->width;
		$targetHeight = $resolution->height;

		if (Config::useExifThumbnails() && isset($exifInfo['thumbnail']) &&
				$exifInfo['thumb_width'] >= $targetWidth && $exifInfo['thumb_height'] >= $targetHeight) {
			$im = $this->extractExifThumbnail($exifInfo);
		} else {
			$im = $this->convert2gd();
		}

		$im = ImageHelper::resizeImage($im, $targetWidth, $targetHeight, $resolution->format);

		// Rotate the image if the orientation is not top-left
		if (isset($exifInfo['orientation']) && $exifInfo['orientation'] != 1) {
			$im = ImageHelper::rotateImage($im, $exifInfo['orientation']);
		}

		$jpeg = ImageHelper::gdimage2jpeg($im, $resolution->quality);
		return $jpeg;
	}

	private function getResolution() {
		if ($this->width === null || $this->height === null) {
			list($width, $height, $type, $attrs) = getimagesize($this->filename);
			$this->width = $width;
			$this->height = $height;
		}
		return array($this->width, $this->height);
	}

	private function getAspectRatio() {
		list($origWidth, $origHeight) = $this->getResolution();
		return ($origWidth / $origHeight);
	}

	private function getExifInfo() {
		if ($this->exifInfo === null) {
			$this->exifInfo = array();
			if ($this->format == self::IMAGE_JPEG || $this->format == self::IMAGE_TIFF) {
				$exifdata = @exif_read_data($this->filename, "ANY_TAG", true, true);
				if ($exifdata) {
					if (isset($exifdata['IFD0']['Orientation'])) {
						$this->exifInfo['orientation'] = $exifdata['IFD0']['Orientation'];
					}
					if (isset($exifdata['COMPUTED']['Thumbnail.Width'])) {
						$this->exifInfo['thumb_width'] = $exifdata["COMPUTED"]["Thumbnail.Width"];
					}
					if (isset($exifdata['COMPUTED']['Thumbnail.Height'])) {
						$this->exifInfo['thumb_height'] = $exifdata["COMPUTED"]["Thumbnail.Height"];
					}
					if (isset($exifdata['THUMBNAIL']['THUMBNAIL'])) {
						$this->exifInfo['thumbnail'] = $exifdata["THUMBNAIL"]["THUMBNAIL"];
					}
				}
			}
		}
		return $this->exifInfo;
	}

	private function extractExifThumbnail($exifInfo) {
		$thumbWidth = $exifInfo['thumb_width'];
		$thumbHeight = $exifInfo['thumb_height'];
		$aspectRatio = $this->getAspectRatio();
		if ($aspectRatio >= 1) {
			$croppedWidth = $thumbWidth;
			$croppedHeight = round($thumbWidth / $aspectRatio);
			# Special case: 160x107 should be treated as 160x104 to completely remove black bars in most DSLR
			if ($croppedWidth == 160 && $croppedHeight == 107) {
				$croppedHeight = 104;
			}
		} else {
			$croppedHeight = $thumbHeight;
			$croppedWidth = round($thumbHeight * $aspectRatio);
			# Special case: 160x107 (see above)
			if ($croppedHeight == 160 && $croppedWidth == 107) {
				$croppedWidth = 104;
			}
		}
		$im = imagecreatefromstring($exifInfo['thumbnail']);
		if ($croppedWidth != $thumbWidth || $croppedHeight != $thumbHeight) {
			$imnew = imagecreatetruecolor($croppedWidth, $croppedHeight);
			imagecopy($imnew, $im, 0, 0, round(($thumbWidth - $croppedWidth) / 2), round(($thumbHeight - $croppedHeight) / 2), $croppedWidth, $croppedHeight);
			$im = $imnew;
		}
		return $im;
	}

	private function convert2gd() {
		switch ($this->format) {
			case self::IMAGE_JPEG: $im = imagecreatefromjpeg($this->filename); break;
			case self::IMAGE_PNG:  $im = imagecreatefrompng($this->filename); break;
			case self::IMAGE_TIFF:
				$pipe = popen("convert \"".$this->filename."\" jpeg:-", "r");
				$jpegimg = "";
				while (!feof($pipe)) { $jpegimg .= fread($pipe,1024); }
				pclose($pipe);
				$im = imagecreatefromstring($jpegimg);
				break;
		}
		return $im;
	}

}
