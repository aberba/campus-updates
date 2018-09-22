$(function() {
       $(".capture img").on("click", function() {
       	   var $id = $(this).parent().parent().attr("id").split("capture")[1];
       	   Capture.showLightbox($id);
       });

       $(".container .slide").touchwipe({
             wipeLeft: function() { Capture.slideBack(); },
             wipeRight: function() { Capture.slideNext(); },
             wipeUp: function() { },
             wipeDown: function() { },
             min_move_x: 20,
             min_move_y: 20,
             preventDefaultEvents: true
        });
});


/***************** JQuery Touchwipe ************************************/

/**
 * jQuery Plugin to obtain touch gestures from iPhone, iPod Touch and iPad, should also work with Android mobile phones (not tested yet!)
 * Common usage: wipe images (left and right to show the previous or next image)
 * 
 * @author Andreas Waltl, netCU Internetagentur (http://www.netcu.de)
 * @version 1.1.1 (9th December 2010) - fix bug (older IE's had problems)
 * @version 1.1 (1st September 2010) - support wipe up and wipe down
 * @version 1.0 (15th July 2010)
 */
(function($){$.fn.touchwipe=function(settings){var config={min_move_x:20,min_move_y:20,wipeLeft:function(){},wipeRight:function(){},wipeUp:function(){},wipeDown:function(){},preventDefaultEvents:true};if(settings)$.extend(config,settings);this.each(function(){var startX;var startY;var isMoving=false;function cancelTouch(){this.removeEventListener('touchmove',onTouchMove);startX=null;isMoving=false}function onTouchMove(e){if(config.preventDefaultEvents){e.preventDefault()}if(isMoving){var x=e.touches[0].pageX;var y=e.touches[0].pageY;var dx=startX-x;var dy=startY-y;if(Math.abs(dx)>=config.min_move_x){cancelTouch();if(dx>0){config.wipeLeft()}else{config.wipeRight()}}else if(Math.abs(dy)>=config.min_move_y){cancelTouch();if(dy>0){config.wipeDown()}else{config.wipeUp()}}}}function onTouchStart(e){if(e.touches.length==1){startX=e.touches[0].pageX;startY=e.touches[0].pageY;isMoving=true;this.addEventListener('touchmove',onTouchMove,false)}}if('ontouchstart'in document.documentElement){this.addEventListener('touchstart',onTouchStart,false)}});return this}})(jQuery);




var Capture = {

    showLightbox: function(captureID) {
    	 if(captureID == null) throw new Error("captureID is no set in showLightbox()");

    	 var $url = "show_capture_lightbox=yes&id="+captureID;
    	 $.get("/ajax/ajax.query.php", $url, function(data) {
    	 	 
             if(data == 0) return false;

             
             $close = $("<p />", {"class":"close", "text":" x "}).bind("click", function() {
                Capture.removeLightbox();
             });

             $next_img = $("<img />", {"src":"/img/icons/arr-next.png", "alt":"Next"});
             $prev_img = $("<img />", {"src":"/img/icons/arr-prev.png", "alt":"Prev"});

             $next = $("<span />", {"class":"next arrow", "html":$next_img}).bind("click", function() {
                Capture.slideNext();
             });

             $prev      = $("<span />", {"class":"previous arrow", "html":$prev_img}).bind("click", function() {
                Capture.slideBack();
             });

             $slideWrap = $("<div />", {"class":"slide-wrap"});
             $subContainer = $("<div />", {"class":"sub-container"}).append($slideWrap);
             $container = $("<div />", {"class":"container"}).append($close).append($next).append($prev).append($subContainer);
             $lbox      = $("<div />", {"class":"capture-lightbox"}).append($container);

             var $data  = JSON.parse(data);
             for (var i in $data) {
             	 $img = $("<img />", {"class":"slide", "id":"slide"+ $data[i].capture_id, "alt":$data[i].caption, "src":"/uploads/captures/"+ $data[i].file_name});
             	 $slideWrap.append($img);
             }
             $current = $slideWrap.children("img#slide"+captureID);
             $current.addClass("current");
             $subContainer.append($("<div />", {"class":"caption", "html":$current.attr("alt")}));
             
             $("body").append($lbox).hide().fadeIn("slow");
    	 });
    },

    removeLightbox: function() {
       $(".capture-lightbox").fadeOut("slow").replaceWith(" ");
    },

    slideNext: function() {
        $(".capture-lightbox .slide-wrap img.slide").removeClass("previous");
        var $current = $(".capture-lightbox .slide-wrap img.current");
        var $next    = $current.next();
        if(!$next.is("img.slide")) {
            $next    = $(".capture-lightbox .slide-wrap img.slide:first");
        }

        $next.animate({left: $(".capture-lightbox .slide-wrap").width()+"px"}, 1000, function() {
            $next.removeClass("previous").addClass("current").addClass("topmost");
        });
        $current.addClass("previous").removeClass("current");
        $next.animate({left: 0}, 1000, function() {
            $current.removeClass("current");
            $next.removeClass("topmost");
            $(".capture-lightbox .container .caption").html($next.attr("alt"));
        });  
    },

    slideBack: function() {
        $(".capture-lightbox .slide-wrap img.slide").removeClass("previous");
        var $current = $(".capture-lightbox .slide-wrap img.current");
        var $prev    = $current.prev();
        if(!$prev.is("img.slide")) {
            $prev    = $(".capture-lightbox .slide-wrap img.slide:last");
        }

        $prev.animate({left: -$(".capture-lightbox .slide-wrap").width()+"px"}, 1000, function() {
            $prev.removeClass("previous").addClass("current").addClass("topmost");
        });
        $current.addClass("previous").removeClass("current");
        $prev.animate({left: 0}, 1000, function() {
            $current.removeClass("current");
            $prev.removeClass("topmost");
            $(".capture-lightbox .container .caption").html($prev.attr("alt"));
        });  
    }

}


