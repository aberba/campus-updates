$(function() {
   
   $("body").delegate("load", ".notification", function() {
   	  $(this).addClass("blue");
   });

   $(".navigation .navmenu").on("click", function() {
       Global.showMenu();
   });

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

Global = {
    showNotificationMessage: function($message) {
        Global.removeNotificationMessage();
        
        clearTimeout(this.timer);
        if($message == null) throw "$message is not defined in Global.showNotificationMessage()";
        var $p         = $("<p />").append($message);
        var $container = $("<div />", { "class":"notification blue" }).append($p);

        $("body").append($container);
        $container.hide().fadeIn("slow");

        this.timer = setTimeout(function() {
            Global.removeNotificationMessage();
        }, 7000);
    },

    removeNotificationMessage: function() {
        $(".notification").fadeOut("slow").replaceWith(" ");
    },

    redirectTo: function($location) {
        window.location.href = $location;
    },

    authenticate: function(callback) {
        var $label     = $("<label />", {"text":"Please authenticate with your password to proceed."});
        var $input     = $("<input />", {"type":"password", "name":"pass", "placeholder":"Enter your account password"});
        var $s         = $("<input />", {"type":"hidden", "name":"authenticate", "value":"yes"});
        var $s_btn     = $("<button />", {"class":"authenticate-btn button", "text":"Authenticate"});
        var $c_btn     = $("<button />", {"class":"cancel-btn button", "text":" Cancel "}).bind("click", function(e) {
           e.preventDefault();
           $(".lightbox").fadeOut("slow").replaceWith(" ");
        });
        
        var $form      = $("<form />", {"class":"authentication-form form"}).append($label).append($input).append($s).append($c_btn).append($s_btn);
        var $container = $("<div />", {"class":"authentication-container"}).append($form);
        var $lightbox  = $("<div />", {"class":"lightbox"}).append($container);

        $s_btn.bind("click", function(e) {
            e.preventDefault();
            var $form = $(".authentication-form").serialize();
            $(".lightbox").fadeOut("slow").replaceWith(" ");

            $.post("./ajax/ajax.session.php", $form, function(e) {
                $result = (e.indexOf("granted") != -1);
                callback($result);
            });
        });     

        $("body").append($lightbox);
        $lightbox.hide().fadeIn("slow");
    }
}
