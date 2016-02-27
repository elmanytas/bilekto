<?php

class ImageHelper {

	public static function resizeImage($im, $targetWidth, $targetHeight, $format) {
		$origWidth = imagesx($im);
		$origHeight = imagesy($im);

		// Directly return the image if no actual resizing is needed
		if ($origWidth == $targetWidth && $origHeight == $targetHeight) {
			return $im;
		}

		// Calculate dimensions for the resize operation:
		// $origWidth/$origHeight     Resolution of the original image
		// $targetWidth/$targetHeight Resolution of the requested thumbnail image
		// $sourceWidth/$sourceHeight Dimensions of the fragment we are taking from the original image
		// $destWidth/$destHeight     Dimensions taken by the resized fragment in the created thumbnail
		if ($format == Resolution::FORMAT_CROPTOFIT) {
			// Crop to fit thumb format
			if ($origWidth < $targetWidth || $origHeight < $targetHeight) {
				// For images smaller than the target resolution we want to crop but not to fit,
				// as this would involve upscaling the image, which is not desirable
				$sourceWidth = min($origWidth, $targetWidth);
				$sourceHeight = min($origHeight, $targetHeight);
			} else {
				$sourceWidth = min($origWidth, (int)round($origHeight * $targetWidth / $targetHeight));
				$sourceHeight = min($origHeight, (int)round($origWidth * $targetHeight / $targetWidth));
			}
			$destWidth = min($targetWidth, $sourceWidth);
			$destHeight = min($targetHeight, $sourceHeight);
		} else {
			// Letterbox and resize thumb formats
			$sourceWidth = $origWidth;
			$sourceHeight = $origHeight;
			$destWidth = min($origWidth, $targetWidth, (int)round($targetHeight * $origWidth / $origHeight));
			$destHeight = min($origHeight, $targetHeight, (int)round($targetWidth * $origHeight / $origWidth));
			if ($format == Resolution::FORMAT_RESIZE) {
				$targetWidth = $destWidth;
				$targetHeight = $destHeight;
			}
		}

		// Calculate the offsets so the thumbnail is centered in case white bars need to be added,
		// (which is the case for letterbox and croptofit for small images)
		$sourceOffsetX = round(($origWidth - $sourceWidth) / 2);
		$sourceOffsetY = round(($origHeight - $sourceHeight) / 2);
		$destOffsetX = round(($targetWidth - $destWidth) / 2);
		$destOffsetY = round(($targetHeight - $destHeight) / 2);

		proc_nice(10);
		$imnew = imagecreatetruecolor($targetWidth, $targetHeight);
		$background = imagecolorallocate($imnew, 255, 255, 255);
		imagefill($imnew, 0, 0, $background);
		imagecopyresampled($imnew, $im, $destOffsetX, $destOffsetY, $sourceOffsetX, $sourceOffsetY, $destWidth, $destHeight, $sourceWidth, $sourceHeight);
		proc_nice(0);

		return $imnew;
	}

	public static function gdimage2jpeg($im, $quality = 80) {
		ob_start();
			imagejpeg($im, null, $quality);
			$jpeg = ob_get_contents();
		ob_end_clean();
		return $jpeg;
	}

	public static function rotateImage($im, $orientation) {
		$hflip = false;
		$angle = 0;

		if ($orientation == 1) { return $im; }                  // 1: top/left - nothing to do
		if ($orientation == 2) { $hflip = true; }               // 2: top/right - hflip
		if ($orientation == 3) { $angle = 180; }                // 3: bottom/right - 180
		if ($orientation == 4) { $hflip = true; $angle = 180; } // 4: bottom/left - hflip + 180 (= vflip)
		if ($orientation == 5) { $hflip = true; $angle = 90; }  // 5: left/top - hflip + 90 left
		if ($orientation == 6) { $angle = -90; }                // 6: right/top - 90 right
		if ($orientation == 7) { $hflip = true; $angle = -90; } // 7: right/bottom - hflip + 90 right
		if ($orientation == 8) { $angle = 90; }                 // 8: left/bottom - 90 left

		if ($hflip) {
			$x = imagesx($im);
			$y = imagesy($im);
			$imnew = imagecreatetruecolor($x, $y);
			imagecopyresampled($imnew, $im, 0, 0, ($x-1), 0, $x, $y, -$x, $y);
			$im = $imnew;
		}

		if ($angle != 0) {
			$imnew = imagerotate($im, $angle, 0);
			$im = $imnew;
		}

		return $im;
	}

}
