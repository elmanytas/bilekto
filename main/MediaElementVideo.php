<?php

require_once BASE_PATH . "/main/ImageHelper.php";

class MediaElementVideo extends MediaElement {

	const VIDEO_AVI = 1;
	const VIDEO_MOV = 2;
	const VIDEO_MP4 = 3;
	const VIDEO_MKV = 4;
	const VIDEO_WMV = 5;
	const VIDEO_MTS = 6;

	public static $extensions = array(
		'avi' => self::VIDEO_AVI,
		'mov' => self::VIDEO_MOV,
		'mp4' => self::VIDEO_MP4,
		'mkv' => self::VIDEO_MKV,
		'wmv' => self::VIDEO_WMV,
		'mts' => self::VIDEO_MTS,
	);
	protected $_extensions;

	protected $contentTypes = array(
		self::VIDEO_AVI => 'video/x-msvideo',
		self::VIDEO_MOV => 'video/quicktime',
		self::VIDEO_MP4 => 'video/mp4',
		self::VIDEO_MKV => 'video/x-matroska',
		self::VIDEO_WMV => 'video/x-ms-wmv',
	);

	public function getDimensions() {
		return array(null, null);
	}

	public function generateThumbnail($resolution) {
		$descriptorspec = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
			2 => array("pipe", "w"),
		);
		proc_nice(10);
		$process = proc_open("ffmpeg -ss 1 -i \"".$this->filename."\" -f image2 -vframes 1 -", $descriptorspec, $pipes);
		proc_nice(0);

		$frame = "";
		if ($process) {
			while (!feof($pipes[1])) {
				$frame .= fread($pipes[1], 1024);
			}
			fclose($pipes[1]);
		}

		$lvl = error_reporting();
		error_reporting(E_ERROR | E_PARSE);
		$im = imagecreatefromstring($frame);
		error_reporting($lvl);
		if (!$im) {
			// Use a default when ffmpeg could not be found or it returned an invalid image
			$im = imagecreatefrompng("static/images/playvideo.png");
		}

		$imnew = ImageHelper::resizeImage($im, $resolution->width, $resolution->height, $resolution->format);
		imagedestroy($im);
		$im = $imnew;

		// We add the film reel around the video thumbnail
		$reel = imagecreatefrompng("static/images/filmreel.png");
		imagecopy($im, $reel, 0, 0, 0, 0, 12, imagesy($im));
		imagecopy($im, $reel, imagesx($im)-12,0, 0, 0, 12, imagesy($im));
		imagedestroy($reel);

		$jpeg = ImageHelper::gdimage2jpeg($im);
		imagedestroy($im);

		return $jpeg;
	}

}
