$(function() {
  
    $(".information-form button.save").on("click", function(e) {
          e.preventDefault();
          Add.addNew();
    });

    $(".information-form button.cancel").on("click", function(e) {
          e.preventDefault();
          Global.redirectTo("cms_Adds.php");
    });
});

Add = {
	 addNew: function() {
      var $title   = $(".information-form input[name=title]").val();
      var $content = $(".information-form textarea[name=content]").val();

      if($title == "") {
        Global.showNotificationMessage("Please enter event title");
        return;
      }

      if($content == "") {
          Global.showNotificationMessage("Please enter event content");
          return;
      }

      var $url = "add_new_event=yes&title=" + $title + "&content=" + $content;
      $.post("./ajax/ajax.save.php", $url, function(e) {
        	Global.showNotificationMessage(e);
      });
	 }
}