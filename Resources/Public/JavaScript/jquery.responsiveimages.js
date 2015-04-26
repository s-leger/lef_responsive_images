/**
 * This is a adjusted version of Luís Almeida jQuery Unveil Script
 * http://luis-almeida.github.com/unveil
 * 
 * include throttle and verge before this one in the minimified version
 */

;(function($) {

    $.extend(verge);

    // RESPONSIVEIMAGES PLUGIN DEFINITION
    // =================================
    $.fn.responsiveimages = function (options,callback) {
      // The rule is :
      // if the layout is responsive 
      // the value is the entry point of the breakpoint
      
        var defaults = {
                threshold: 0,
                breakpoints: {
                    0: 'xsmall',
                    480: 'small',
                    768: 'medium',
                    992: 'large',
                    1200: 'bigger'
                },
                scrolldirection : 'y',	// x y xy
                skip_invisible: false,
                retina: true,
                container: window,
                preloadClass:"preload"
            },
            options = $.extend({}, defaults, options),
            $window = $(window),
            threshold = options.threshold || 0,
            breakpoints = options.breakpoints,
            retina = options.retina && window.devicePixelRatio > 1,
            attrib = "data-bigger",
            datakey = "bigger",
            images = this,
            scrolldirection = options.scrolldirection || 'y',
            originimages = this,
            loaded,
            smallest_breakpoint = 1e16;
      
      function findSmallestBreakpoint(){
        var bp = 0;
        $.each(breakpoints, function(breakpoint,dk) {
            bp = parseInt(breakpoint);
            if(bp < smallest_breakpoint){
                    smallest_breakpoint = bp;
                }
        });  
      }
      
      function getDataKey(){
            datakey = "bigger",
            bw = bp = 0,
            ww = $.viewportW();
        
        // window width < smallest_breakpoint
        if (ww < smallest_breakpoint){
           datakey = breakpoints[smallest_breakpoint];
            return;
          };
        
        // find largest breakpoint < window 
        $.each(breakpoints, function(breakpoint,dk) {
            bp = parseInt(breakpoint);
            if(bp <= ww && bp >= bw ){
              bw = bp;
              datakey = dk;
            }
          });
    
      }
 
        this.on("responsiveimages", function() {
            var source  = this.getAttribute(attrib);
            // fallback to non retina or src when not found
            source = source || (retina ? this.getAttribute( "data-" + datakey ) : false) || this.getAttribute("data-src");
            if(source){
                this.setAttribute("src", source);
                if(typeof callback === "function") callback.call(this);
            }
        });

        function checkviewport(){
            old_attrib = attrib;
            getDataKey();
            attrib =  "data-" + (retina ? "retina-" : "") + datakey;
     
            if(old_attrib != attrib){
                images = originimages;
            }
            responsiveimages();
        }
      
        function responsiveimages() {            
            var inview = images.filter(function() {
              var $element = $(this);
              if (options.skip_invisible && $element.is(":hidden")) return;
              if (options.preloadClass && $element.hasClass(options.preloadClass)) return true;
              switch(scrolldirection){
              	case 'x':return $.inX($element, threshold);
             	break;
              	case 'xy':return $.inViewport($element, threshold);
              	break;
              	default:
              	case 'y':return $.inY($element, threshold);
				}
            });
            loaded = inview.trigger("responsiveimages");
            images = images.not(loaded);
        }
        // use throttle plugin when present, otherwise this paceholder 
        if ($.throttle === undefined){$.throttle = function(delay, fun){return fun;};}; 
        $(options.container).scroll($.throttle(250,checkviewport));
   
        $window.resize(checkviewport);
        
        // allow external call to $.fn.responsiveimages.update()
        // eg on ascensor page transition
        $.fn.responsiveimages.update = checkviewport;
        
        findSmallestBreakpoint();
        checkviewport();
       
        return this;

    }

})(window.jQuery);