$(function() {
    $(".form .setting .delete").on("click", function(e) {
		e.preventDefault();
		Settings.delete($(this).attr("id"));
	});

	$(".form button.save").on("click", function(e) {
		e.preventDefault();
		Settings.save();
	});

	$(".form button.add").on("click", function(e) {
		e.preventDefault();
		Settings.add();
	});

});

var Settings = {
	save: function() {
		var $data = $(".settings-form").serialize();

        $.post("./ajax/ajax.save.php", $data, function(e) {
            Global.showNotificationMessage(e);
        });
	},

	add: function() {
		var $lightbox,
		    $container,
		    $close,
		    $form,
		    $label_name,
		    $label_value,
		    $input_name,
		    $input_value,
		    $save_btn,
		    $cancel_btn;

		$label_name  = $("<label />", {"for":"settin-name", "text":"Setting Name: "});
		$label_value = $("<label />", {"for":"settin-value", "text":"Setting Value: "});
		$input_name  = $("<input />", {"type":"text", "name":"settin-name", "placeholder":"Setting name here: "});
		$input_value = $("<input />", {"type":"text", "name":"settin-value", "placeholder":"Setting value here: "});

		$save_btn = $("<button />", {"type":"button", "class":"save-new button", "text":" Save "}).bind("click", function(e) {
            e.preventDefault();
            Settings.saveNew($input_name.val(), $input_value.val());
		});

		$cancel_btn = $("<button />", {"type":"button", "class":"cancel-new button", "text":" Cancel "}).bind("click", function(e) {
            e.preventDefault();
            Settings.removeLightbox();
		});

        $form      = $("<form />", {"class":"add-setting-form form"}).append($label_name).append($input_name).append($label_value).append($input_value).append($save_btn).append($cancel_btn);
		$container = $("<div />", {"class":"container"}).append($form);
		$lightbox  = $("<div />", {"class":"lightbox"}).append($container);
		$("body").append($lightbox);
		$lightbox.hide().fadeIn("slow");
	},

	saveNew: function($name, $value) {
		if($name == null) throw "$name was not set in saveNew()";
		if($value == null) throw "$value was not set in saveNew()";

		if($name == "") {
			Global.showNotificationMessage("Please enter name for setting (No white space eg. site_name)");
			return;
		}

		if($value == "") {
			Global.showNotificationMessage("Please enter value for "+ $name);
			return;
		}

		var $url = "add_setting=yes&name=" + $name + "&value=" + $value;
		$.post("./ajax/ajax.save.php", $url, function(e) {
			Global.showNotificationMessage(e);
			if(e.indexOf("successfully") != -1) {
				Settings.removeLightbox();
			}
		});
	},

	delete: function($setting_id) {
		if($setting_id == null) throw "$setting_id was not ser in Setting.delete()";
		var $url = "delete_setting=yes&setting_id=" + $setting_id;
		$.post("./ajax/ajax.delete.php", $url, function(e) {
			Global.showNotificationMessage(e);
			if(e.indexOf("successfully") != -1) {
				$("#setting" + $setting_id).fadeOut("slow");
			}
		});
	},

	removeLightbox: function() {
        $(".lightbox").fadeOut("slow").replaceWith(" ");
	}
}