$(function() {
       $(".capture img").on("click", function() {
           var $id = $(this).parent().parent().attr("id").split("capture")[1];
           Capture.showLightbox($id);
       });
});


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