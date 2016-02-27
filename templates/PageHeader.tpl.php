<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es">

<head>
<title><?= $title ?></title>

<?php
  if ($gallery === true) {
?>
<script type="text/javascript" src="<?= $static_url ?>/photoswipe/photoswipe.min.js"></script>
<script type="text/javascript" src="<?= $static_url ?>/photoswipe/photoswipe-ui-default.min.js"></script>
<script type="text/javascript" src="<?= $static_url ?>/js/echo.js"></script>
<script type="text/javascript" src="<?= $static_url ?>/js/gallery.js"></script>
<link rel="stylesheet" href="<?= $static_url ?>/photoswipe/photoswipe.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?= $static_url ?>/photoswipe/default-skin/default-skin.css" type="text/css" />
<?php
  }
?>
<link rel="stylesheet" href="<?= $static_url ?>/css/simplegallery.css" type="text/css" media="screen" />

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width">

</head>
<body>
