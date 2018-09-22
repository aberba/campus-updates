$(function() {
   

   $("body").delegate("load", ".notification", function() {
   	  $(this).addClass("blue");
   });

   $(".navigation .navmenu").on("click", function() {
       Global.showMenu();
   });

   $("#header .search-form").on("submit", function(e) {
       e.preventDefault();
       Global.makeSearch();
   });

   $("#header .status-section ul li select[name=category]").on("change", function(e) {
       e.preventDefault();
       Global.sessionSetSearchCat();
   });

   Global.initAsideAd();
   Global.initBannerAd();

});


function isInExtensions($extensions, $extension) {
      var i=0, $length = $extensions.length;

      $extension = $extension.toLowerCase().trim();

      for (; i < $length; i++) {
          if ($extension === $extensions[i]) {
              return true;
          }
          return false;
      }
}

var Global = {

   showNotificationMessage: function($message) {
        Global.removeNotificationMessage();
        
        clearTimeout(this.timer);
        if($message == null) throw "$message is not defined in Global.showNotificationMessage()";
        var $p         = $("<p />").append($message);
        var $container = $("<div />", { "class":"notification" }).append($p);

        Global.removeNotificationMessage();
        $("body").append($container);
        $container.hide().fadeIn("slow");

        this.timer = setTimeout(function() {
            Global.removeNotificationMessage();
        }, 9000);
    },

    removeNotificationMessage: function() {
        $(".notification").fadeOut("slow").replaceWith(" ");
    },

    showMenu: function() {
        $navmenu = $(".navigation .navmenu");
        $navmenu.children("ul").fadeIn("slow");

        $navmenu.children("li").bind("click", function(e) {
           Global.hideMenu();
        });

        $("#content").bind("click", function() {
           Global.hideMenu();
        });

        $(".navigation .navmenu ul li.close").bind("click", function(e) {
             e.preventDefault();
             Global.hideMenu();
        });

        $(".search-form input[type=search]").bind("focus", function() {
           Global.hideMenu();
        });
    },

    hideMenu: function() {
        $(".navigation .navmenu").children("ul").fadeOut("slow");
    },

    makeSearch: function() {
        var $param = $("#header .search-form input[name=s]").val().trim();
        var $category = $("#header .status-section ul li select[name=category] option:selected").attr("value");
        if($param.length > 1) {
            window.location.href = "/search/" + encodeURI($param);
        }
    },

    sessionSetSearchCat: function() {
        var $category = $("#header .status-section ul li select[name=category] option:selected").attr("value");
        var $url      = "set_search_category=yes&category=" + $category;
        $.post("/ajax/ajax.session.php", $url, function(e) {
          // do nothing
        });
    },

    slideBannerAd: function() {

    },

    initAsideAd: function() {
        var $box = $("#aside  advertisement-box .slider");
        $box.children("div.slide:first").addClass("current");

        setInterval(function() {
           Global.asideSlideNext();
        }, 15000);
    },

    asideSlideNext: function() {
        var $box = $("#aside .advertisement-box .slider");

        var $current = $box.children(".slide.current");
        var $next    = $current.next();

        if (!$next.is("div.slide")) {
            $next = $box.children("div.slide:first");
        }
        //alert($next.html());

        $next.css({
            opacity: 0.5,
            left: "300px"
        });
        $current.removeClass("current").addClass("previous");
        $next.addClass("current").animate({ opacity: 0.8, left: "0px" }).animate({ opacity: 1 }, "swing", function() {
             $current.removeClass("previous");
        });
    },

    initBannerAd: function() {
        var $box = $("#header .banner-section .slider");
        $box.children("div.slide:first").addClass("current");

        setInterval(function() {
           Global.bannerSlideNext();
        }, 15000);
    },

    bannerSlideNext: function() {
        var $box = $("#header .banner-section .slider");

        var $current = $box.children(".slide.current");
        var $next    = $current.next();

        if (!$next.is("div.slide")) {
            $next = $box.children("div.slide:first");
        }

        $next.css({
            opacity: 0.5,
            left: "300px"
        });
        $current.removeClass("current").addClass("previous");
        $next.addClass("current").animate({ opacity: 0.8, left: "0px" }).animate({ opacity: 1 }, "swing", function() {
             $current.removeClass("previous");
        });
    }
}
