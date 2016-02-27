<?php

class Resolution {

	public $width;
	public $height;
	public $quality;
	public $format;

	const FORMAT_ORIGINAL = 0;
	const FORMAT_RESIZE = 1;
	const FORMAT_CROPTOFIT = 2;
	const FORMAT_LETTERBOX = 3;

	/**
	 * Valid formats:
	 * - 360x360x80: normal resize, keeping proportions (so actual size will be different)
	 * - 360_360_80: crop to fit the desired size
	 * - 360~360~80: letterbox inside a desired size, filling with white borders
	 */
	public static function createFromComplexString($resolutionString) {
		$args = preg_split('/([x~_])/', $resolutionString, -1, PREG_SPLIT_DELIM_CAPTURE);
		if (count($args) != 5) {
			throw new Exception("Resolution string $resolutionString is not valid");
		}
		$width = $args[0];
		$height = $args[2];
		$quality = $args[4];
		switch ($args[1]) {
			case 'x': $format = self::FORMAT_RESIZE; break;
			case '_': $format = self::FORMAT_CROPTOFIT; break;
			case '~': $format = self::FORMAT_LETTERBOX; break;
		}
		$res = new self();
		$res->width = (int)$width;
		$res->height = (int)$height;
		$res->quality = (int)$quality;
		$res->format = $format;
		return $res;
	}

	public static function createFromBasicString($resolutionString, $quality, $format) {
		$args = explode('x', $resolutionString);
		if (count($args) != 2 || !is_numeric($args[0]) || !is_numeric($args[1])) {
			throw new Exception("Resolution string $resolutionString is not valid");
		}
		list($width, $height) = $args;
		$res = new self();
		$res->width = (int)$width;
		$res->height = (int)$height;
		$res->quality = (int)$quality;
		$res->format = $format;
		return $res;
	}

	public static function createOriginalResolution() {
		$res = new self();
		$res->format = self::FORMAT_ORIGINAL;
		return $res;
	}

	public function calculateResizedResolution($origWidth, $origHeight) {
		$heightForTargetWidth = (int)round($origHeight * ($this->width / $origWidth));
		$widthForTargetHeight = (int)round($origWidth * ($this->height / $origHeight));
		$resizedWidth = min($this->width, $widthForTargetHeight, $origWidth);
		$resizedHeight = min($this->height, $heightForTargetWidth, $origHeight);
		return array($resizedWidth, $resizedHeight);
	}

	public function toString() {
		switch ($this->format) {
			case self::FORMAT_ORIGINAL: // shouldn't happen
			case self::FORMAT_RESIZE: $sep = 'x'; break;
			case self::FORMAT_CROPTOFIT: $sep = '_'; break;
			case self::FORMAT_LETTERBOX: $sep = '~'; break;
		}
		return $this->width . $sep . $this->height . $sep . $this->quality;
	}

}

