<?php

abstract class MediaElement {

	protected $filename;
	protected $format;

	protected static $extensions;
	protected $_extensions;
	protected $contentTypes;

	public function __construct($filename) {
		if (!is_file($filename)) {
			throw new Exception("File does not exist");
		}
		$this->filename = $filename;

		$extension = self::getExtension($filename);
		if (isset(static::$extensions[$extension])) {
			$this->format = static::$extensions[$extension];
		} else {
			throw new Exception("Unsupported file format");
		}
	}

	public function getContentType() {
		if (isset($this->contentTypes[$this->format])) {
			return $this->contentTypes[$this->format];
		} else {
			return 'application/octet-stream';
		}
	}

	public function getFilename() {
		return $this->filename;
	}

	public static function getExtension($file) {
		$offset = strrpos($file, '.');
		return ($offset === False) ? '' : substr($file, $offset + 1);
	}

	abstract public function getDimensions();

	abstract public function generateThumbnail($resolution);

}
