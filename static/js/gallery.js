var initPhotoSwipeFromDOM = function() {

    var gallery;
    var items = [];

    var initGalleryItems = function() {
        var galleryNodes = document.querySelectorAll('img.galleryitem');
        var numNodes = galleryNodes.length;

        for(var i = 0; i < numNodes; i++) {
            galleryNodes[i].setAttribute('data-pswp-uid', i+1);
            galleryNodes[i].onclick = onThumbnailsClick;

            node = galleryNodes[i];

            item = {
                src: node.getAttribute('data-slide'),
                w: node.getAttribute('data-slide-width'),
                h: node.getAttribute('data-slide-height'),
                title: node.getAttribute('data-title'),
                fullsrc: node.parentNode.getAttribute('href'),
            };

            items.push(item);
        }
    };

    var onThumbnailsClick = function(e) {
        e = e || window.event;
        e.preventDefault ? e.preventDefault() : e.returnValue = false;

        var node = e.target || e.srcElement;

		openPhotoSwipe(node.getAttribute('data-pswp-uid'));

        return false;
    };

    var openPhotoSwipe = function(initial) {
        var pswpElement = document.querySelectorAll('.pswp')[0];

        var options = {
            index: initial-1,
            galleryUID: 0,
            showAnimationDuration: 0,
            preload: [1,1],
            loop: false,
            closeOnScroll: false,
            closeOnVerticalDrag: false,
            escKey: true,
            arrowKeys: true,
            shareButtons: [
                {id:'newwindow', label:'View original', url:'{{raw_image_url}}', download:false},
                {id:'download', label:'Download original', url:'{{raw_image_url}}', download:true}
            ],
            getImageURLForShare: function( /* shareButtonData */ ) {
                return gallery.currItem.fullsrc || '';
            }
        };

        // Pass data to PhotoSwipe and initialize it
        gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
        gallery.init();
    };

    var photoswipeParseHash = function() {
        var hash = window.location.hash.substring(1),
        params = {};

        if(hash.length < 5) {
            return params;
        }

        var vars = hash.split('&');
        for (var i = 0; i < vars.length; i++) {
            if(!vars[i]) {
                continue;
            }
            var pair = vars[i].split('=');  
            if(pair.length < 2) {
                continue;
            }           
            params[pair[0]] = pair[1];
        }

        if(params.gid) {
            params.gid = parseInt(params.gid, 10);
        }

        if(!params.hasOwnProperty('pid')) {
            return params;
        }
        params.pid = parseInt(params.pid, 10);
        return params;
    };

    // Initialize gallery elements
    initGalleryItems();

    // Parse URL and automatically open gallery if it contains #pid=n
    var hashData = photoswipeParseHash();
    if(hashData.pid > 0) {
        openPhotoSwipe(hashData.pid - 1);
    }
};

var calculateSizes = function() {
    var pixelRatio = window.devicePixelRatio;
    var viewportWidth = document.documentElement.clientWidth;

    var availableWidths = [];
    var thumbSizes = {};
    var galleryNode = document.getElementsByClassName('gallery')[0];
    var thumbResolutions = galleryNode.getAttribute('data-thumb-sizes').split(' ');
    var numThumbResolutions = thumbResolutions.length;
    for (var i = 0; i < numThumbResolutions; i++) {
        var sizeParams = thumbResolutions[i].split(/[x~_]/);
        var imageWidth = parseInt(sizeParams[0]);
        var imageHeight = parseInt(sizeParams[1]);
        var cssWidth = imageWidth / pixelRatio;
        var cssHeight = imageHeight / pixelRatio;
        thumbSizes[cssWidth] = {
            imageWidth: imageWidth,
            imageHeight: imageHeight,
            cssWidth: cssWidth,
            cssHeight: cssHeight,
            resolution: thumbResolutions[i],
        };
        availableWidths.push(cssWidth);
    }

    availableWidths.sort(function(a,b){return a-b});

    var selectedWidth;
    var maxRequiredWidth = viewportWidth / 3;
    maxRequiredWidth = maxRequiredWidth - 2 * Math.max(1, Math.round(0.01*maxRequiredWidth));
    for (var i = 0; i < numThumbResolutions; i++) {
        selectedWidth = availableWidths[i];
        if (availableWidths[i] > maxRequiredWidth) {
            break;
        }
    }


    var actualWidth, actualHeight;
    for (var columns = 3; true; columns++) {
        actualWidth = Math.floor(viewportWidth / columns);
        actualWidth = actualWidth - 2 * Math.max(1, Math.round(0.01*actualWidth));
        if (actualWidth < selectedWidth) {
            break;
        }
    }

    actualHeight = actualWidth * thumbSizes[selectedWidth].cssHeight / thumbSizes[selectedWidth].cssWidth

    var images = document.querySelectorAll('img.thumbimage');
    var numImages = images.length;
    for (var i = 0; i < numImages; i++) {
        images[i].width = actualWidth;
        images[i].height = actualHeight;
        var url = images[i].getAttribute('data-thumb-url').replace('{{thumb_size}}', thumbSizes[selectedWidth].resolution);
        images[i].setAttribute('data-echo', url);
    }

    var thumbblocks = document.querySelectorAll('span.thumb');
    for (var i = 0; i < thumbblocks.length; i++) {
        thumbblocks[i].style.margin = Math.max(1, Math.round(actualWidth * 0.01)) + 'px';
    }

}
