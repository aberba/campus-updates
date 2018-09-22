$(function() {
    $(".information-form button.save").on("click", function(e) {
        e.preventDefault();
        Add.newPost();
    });

    $(".information-form button.cancel").on("click", function(e) {
        e.preventDefault();
        Global.redirectTo("cms_posts.php");
    });
});

Add = {
	newPost: function() {
		var $title   = $(".information-form input[name=title]").val();
		var $content = $(".information-form textarea[name=content]").val();

		if($title == "") {
			Global.showNotificationMessage("Please enter post title");
			return;
		}

        if($content == "") {
            Global.showNotificationMessage("Please enter post content");
            return;
        }


        var $data = $(".information-form").serialize();
        $.post("./ajax/ajax.save.php", $data, function(e) {
          	Global.showNotificationMessage(e);
        });
	}
}