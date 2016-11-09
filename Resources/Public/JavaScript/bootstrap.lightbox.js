$(function () {

    /**
     * Add PhotoSwipe template to dom if lightbox elements exist.
     */
    if ($('a.lightbox').length > 0) {
        var photoswipeTemplate = '\
            <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">\
                <div class="pswp__bg"></div>\
                <div class="pswp__scroll-wrap">\
                    <div class="pswp__container">\
                        <div class="pswp__item"></div>\
                        <div class="pswp__item"></div>\
                        <div class="pswp__item"></div>\
                    </div>\
                    <div class="pswp__ui pswp__ui--hidden">\
                        <div class="pswp__top-bar">\
                            <div class="pswp__counter"></div>\
                            <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>\
                            <button class="pswp__button pswp__button--share" title="Share"></button>\
                            <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>\
                            <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>\
                            <div class="pswp__preloader">\
                                <div class="pswp__preloader__icn">\
                                    <div class="pswp__preloader__cut">\
                                        <div class="pswp__preloader__donut"></div>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">\
                            <div class="pswp__share-tooltip"></div> \
                        </div>\
                        <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>\
                        <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>\
                        <div class="pswp__caption">\
                            <div class="pswp__caption__center"></div>\
                        </div>\
                    </div>\
                </div>\
            </div>';
        $('body').append(photoswipeTemplate);
    }

    /**
     * Open PhotoSwipe
     */
    var openPhotoSwipe = function(pid, gid) {
        var photoswipeContainer = document.querySelectorAll('.pswp')[0];
        var items = $('a.lightbox[rel=' + gid + ']')
            .map(function(i, el){
              var $el = $(el);
              var caption = '';
              if($el.data('lightbox-caption')) {
                caption = $el.data('lightbox-caption');
              } else {
                caption = $el.next('figcaption').text();
              }
              return {
                src: $el.attr('href'),
                title: $el.attr("title") || caption != '',
                alt: $el.attr("alt"),
                w: $el.data('lightbox-width'),
                h: $el.data('lightbox-height'),
                caption: caption,
                pid: $el.index('a.lightbox[rel=' + gid + ']'),
              };
            });
        var options = {
            index: pid,
            addCaptionHTMLFn: function(item, captionEl, isFake) {
               var innerHtml = ['<div>'];
                if(item.title && item.title !== true) {
                   innerHtml.push('<div>');
                   innerHtml.push(item.title);
                   innerHtml.push('</div>');
                }
                if(item.caption) {
                   innerHtml.push('<div>');
                   innerHtml.push(item.caption);
                   innerHtml.push('</div>');
                }
                if (innerHtml.length < 2)
                  return false;
                
                innerHtml.push('</div>');
                captionEl.children[0].innerHTML = innerHtml.join("");
                return true;
            },
            
            // barsSize: {top:44, bottom:'auto'}, 
          
            spacing:        0.12,
            loop:           true,
            bgOpacity:      1,
            closeOnScroll:  true,
            history:        true,
            galleryUID:     gid,
            galleryPIDs:    true,
            closeEl:        true,
            captionEl:      true,
            fullscreenEl:   true,
            zoomEl:         true,
            shareEl:        true,
            counterEl:      true,
            arrowEl:        true,
            preloaderEl:    true,
        };
        if(items.length > 0) {
            var gallery = new PhotoSwipe( photoswipeContainer, PhotoSwipeUI_Default, items, options);
            gallery.init();
        }
    }

    /**
     * Get PhotoSwipe params from url hash and open gallery
     */
    var getPhotoSwipeParamsFromHash = function() {
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
        return params;
    };
    var photoSwipeHashData = getPhotoSwipeParamsFromHash();
    if(photoSwipeHashData.pid && photoSwipeHashData.gid) {
        openPhotoSwipe( parseInt(photoSwipeHashData.pid) , photoSwipeHashData.gid);
    }

    /**
     * Register listener
     */
    $('body').on('click', 'a.lightbox', function(e){
        e.preventDefault();
        var $el = $(this);
        var gid = $el.attr('rel');
        var pid = $el.index('a.lightbox[rel=' + gid + ']');
        openPhotoSwipe(pid, gid);
    });

});