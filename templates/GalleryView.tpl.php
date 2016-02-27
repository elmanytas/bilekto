<div class="header">
<div class="navigation">
<?php
if (isset($navlinks['parent'])) {
?>
<a href="<?= $navlinks['parent']['link'] ?>"><?= $navlinks['parent']['name'] ?></a><br />
<?php
}

if (isset($navlinks['previous'])) {
?>
<a href="<?= $navlinks['previous']['link'] ?>" title="<?= $navlinks['previous']['name'] ?>"><img src="<?= $static_url ?>/images/arrow-left-active.svg" width="25" height="25" /></a>&nbsp;<?php
} else if (isset($navlinks['next'])) {
?>
<img src="<?= $static_url ?>/images/arrow-left-inactive.svg" width="25" height="25" />&nbsp;<?php
}
?>
<b><?= $navlinks['current']['name'] ?></b><?php
if (isset($navlinks['next'])) {
?>
&nbsp;<a href="<?= $navlinks['next']['link'] ?>" title="<?= $navlinks['next']['name'] ?>"><img src="<?= $static_url ?>/images/arrow-right-active.svg" width="25" height="25" /></a>
<?php
} else if (isset($navlinks['previous'])) {
?>
&nbsp;<img src="<?= $static_url ?>/images/arrow-right-inactive.svg" width="25" height="25" />
<?php
}

?>
<?php
if (isset($downloadLink)) {
?>
<br />
<a href="<?= $downloadLink ?>">Download as ZIP<?php if ($archiveSize) { ?> (<?= $archiveSize ?>)<?php } ?></a>
<?php
}
?>
</div>
<br />
<div class="subfolders"><?php
   foreach ($folders as $f) {
?>
<p class="folder"><a href="<?= $f['dest'] ?>"><?= $f['name'] ?></a></p>
<?php
   }
?>
</div>
</div>

<div class="gallery" data-thumb-sizes="<?= $thumb_sizes ?>" >
<?php
   foreach ($files as $f) {
?>
<span class="thumb">
<a href="<?= $f['full_url'] ?>">
<?php
      if ($f['slideshow']) {
?>
<img src="<?= $static_url ?>/images/blank.png" data-thumb-url="<?= $f['thumb_url'] ?>" class="thumbimage galleryitem" data-slide="<?= $f['slide_url'] ?>" data-slide-width="<?= $f['slide_width'] ?>" data-slide-height="<?= $f['slide_height'] ?>" data-title="<?= $f['name'] ?>" />
<?php
      } else {
?>
<img src="<?= $static_url ?>/images/blank.png" data-thumb-url="<?= $f['thumb_url'] ?>" class="thumbimage" />
<?php
      }
?>
</a>
</span><?php
   }
?>

</div>

<!-- Root element of PhotoSwipe. Must have class pswp. -->
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

    <!-- Background of PhotoSwipe. 
         It's a separate element, as animating opacity is faster than rgba(). -->
    <div class="pswp__bg"></div>

    <!-- Slides wrapper with overflow:hidden. -->
    <div class="pswp__scroll-wrap">

        <!-- Container that holds slides. 
                PhotoSwipe keeps only 3 slides in DOM to save memory. -->
        <div class="pswp__container">
            <!-- don't modify these 3 pswp__item elements, data is added later on -->
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>

        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
        <div class="pswp__ui pswp__ui--hidden">

            <div class="pswp__top-bar">

                <!--  Controls are self-explanatory. Order can be changed. -->

                <div class="pswp__counter"></div>

                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>

                <button class="pswp__button pswp__button--share" title="Share"></button>

                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>

                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

                <!-- Preloader demo http://codepen.io/dimsemenov/pen/yyBWoR -->
                <!-- element will get class pswp__preloader--active when preloader is running -->
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                      <div class="pswp__preloader__cut">
                        <div class="pswp__preloader__donut"></div>
                      </div>
                    </div>
                </div>
            </div>

            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div> 
            </div>

            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
            </button>

            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
            </button>

            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>

          </div>

        </div>

</div>

<script>
window.addEventListener("resize", calculateSizes);

calculateSizes();

echo.init({
    offset: 1000,
});

initPhotoSwipeFromDOM();
</script>
