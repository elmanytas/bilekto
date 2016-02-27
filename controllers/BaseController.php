<?php

abstract class BaseController {

	const TEMPLATES_SUBDIR = 'templates';
	const TEMPLATE_EXTENSION = '.tpl.php';

	protected $caching = false;

	protected function render($templateName, $data = array()) {
		$templateFile = BASE_PATH . '/' . self::TEMPLATES_SUBDIR . '/' . $templateName . self::TEMPLATE_EXTENSION;
		if (!file_exists($templateFile)) {
			throw new Exception('Template ' . $templateName . ' does not exist');
		}
		extract($data);
		include $templateFile;
	}

	protected function sendCacheHeader() {
		if ($this->caching) {
			header("Cache-Control: max-age=3600, private");
		} else {
			header("Cache-Control: no-cache");
		}
	}

	protected function buildSanitizedPath($args) {
		$sanitized = array();
		foreach ($args as $arg) {
			if ($arg[0] != '.') {
				$sanitized[] = $arg;
			}
		}
		return implode("/", $sanitized);
	}
}
