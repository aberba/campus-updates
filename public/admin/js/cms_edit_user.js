$(function() {
    $(".edit-form .save-btn").on("click", function(e) {
    	e.preventDefault();
    	Edit.save();
    });
});

var Edit = {
	save: function() {
		$(".edit-form .save-btn").fadeOut("slow");
		var $form = $(".edit-form").serialize();
		$.post("./ajax/ajax.save.php", $form, function(e) {
			Global.showNotificationMessage(e);
			$(".edit-form .save-btn").fadeIn("slow");
			if (e.indexOf("successfully") != -1) {
				window.location.href = "cms_users.php";
			}
		});
	}
}