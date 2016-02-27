<?php

class ArchiveManager {

	public function __construct($basepath, $subdir, $filelist) {
		// If the path we are requesting is the root of the gallery, go up one directory
		// to avoid a tarbomb when the archive is uncompressed
		if ($subdir == '') {
			$subdir = basename($basepath);
			$basepath = dirname($basepath);
			$filelist = array_map(function ($f) use ($subdir) { return $subdir . '/' . $f; }, $filelist);
		}

		$this->basepath = $basepath;
		$this->subdir = $subdir;
		$this->filelist = $filelist;
	}

	public function dumpZIP() {
		sort($this->filelist);

		chdir($this->basepath);
		$spec = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
		);
		set_time_limit(0);
		$proc = proc_open('zip -q -X -0 -@ -', $spec, $pipes);
		fwrite($pipes[0], implode("\n", $this->filelist));
		fclose($pipes[0]);
		$output = fopen("php://output", "w");
		stream_copy_to_stream($pipes[1], $output);
	}

	public function calculateArchiveSize() {
		$size = 22;
		foreach ($this->filelist as $file) {
			// 92 = 30 (file header) + 46 (central directory file header) + 16 (data descriptor)
			$size += 92;
			// Filename length x2 (one for file header, one for central directory file header)
			$size += 2 * strlen("$file");
			// File size (we're storing without compression by using -0)
			$size += filesize($this->basepath . "/$file");
		}
		return $size;
	}

	public function getArchiveName() {
		$archiveName = str_replace('/', '__', $this->subdir);
		setlocale(LC_CTYPE, 'en_US.UTF8');
		$asciiName = iconv("UTF-8","US-ASCII//TRANSLIT", $archiveName);
		return $asciiName;
	}

}
