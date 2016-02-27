<?php

class UrlHandler {

	const URL_MODE_QUERY_STRING = 0;
	const URL_MODE_PATH_INFO = 1;
	const URL_MODE_REWRITE = 2;

	static private $uriMode;
	static private $baseUrl;
	static private $baseStaticUrl;
	static private $args;

	static function parseUrl() {
		self::$uriMode = Config::getUrlMode();

		$requestUri = $_SERVER['REQUEST_URI'];
		if ($_SERVER['QUERY_STRING'] !== '') {
			$queryString = $_SERVER['QUERY_STRING'];
			$requestUri = str_replace('?' . $queryString, '', $requestUri);
		}
		// Normalize URL (url decode, avoid duplicate /, and remove trailing /)
		$requestUri = urldecode($requestUri);
		$requestUri = preg_replace(',/+,', '/', $requestUri);
		$requestUri = rtrim($requestUri, '/');

		$scriptName = $_SERVER['SCRIPT_NAME'];
		$basePath = preg_replace(',/index.php$,', '', $scriptName);

		self::$baseStaticUrl = $basePath . '/static';

		switch (self::$uriMode) {
			case self::URL_MODE_QUERY_STRING:
				self::$baseUrl = $requestUri;
				$argString = '';
				if (isset($_GET['q'])) {
					$argString = $_GET['q'];
					$argString = preg_replace(',/+,', '/', $argString);
				}
				break;
			case self::URL_MODE_PATH_INFO:
				self::$baseUrl = $scriptName;
				$argString = ltrim($_SERVER['PATH_INFO'], '/');
				$argString = preg_replace(',/+,', '/', $argString);
				break;
			case self::URL_MODE_REWRITE:
				self::$baseUrl = $basePath;
				$argString = preg_replace(",^$basePath/?,", '', $requestUri);
				break;
		}

		if ($argString == '') {
			self::$args[] = '';
		} else {
			self::$args = explode('/', $argString);
		}
	}

	static function getArguments() {
		return self::$args;
	}

	static function buildLink($argument) {
		switch (self::$uriMode) {
			case self::URL_MODE_QUERY_STRING:
				$link = self::$baseUrl . '?q=' . $argument;
				break;
			case self::URL_MODE_PATH_INFO:
			case self::URL_MODE_REWRITE:
				$link = self::$baseUrl . '/' . $argument;
				break;
		}
		return self::urlEncode($link);
	}

	static function getStaticPath() {
		return self::$baseStaticUrl;
	}

	static private function urlEncode($string) {
		// '%' is the first one to avoid double substitutions
		$orig = array(  '%',  ' ',  '!',  '"',  '#',  '$',  '&', '\'',  '(',  ')',  '*',  '+',  ',');
		$dest = array('%25','%20','%21','%22','%23','%24','%26','%27','%28','%29','%2a','%2b','%2c');
		return str_replace($orig, $dest, $string);
	}


}
