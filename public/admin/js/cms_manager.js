$(function() {
    $(".show-form-btn").on("click", function(e) {
    	e.preventDefault();
    	$(this).fadeOut("slow");
    	$(".tag-form").fadeIn("slow");
    });

    $(".tag-form .add-btn").on("click", function(e) {
    	e.preventDefault();
    	Manager.addTag();
    });

    $(".tag-form .cancel-btn").on("click", function(e) {
    	e.preventDefault();
    	$(".show-form-btn").fadeIn("slow");
    	$(".tag-form").fadeOut("slow");
    });

    $(".tags-table .edit").on("click", function(e) {
    	e.preventDefault();
        Manager.changeEditing();
    });

    $(".tags-table td.edit-name").on("blur", function() {
        Manager.updateTagName( $(this).parent().attr("id").split("tag")[1], $(this).text() );
    });

    $(".tags-table .delete").on("click", function(e) {
    	e.preventDefault();
        Manager.removeTag( $(this).parent().parent().attr("id").split("tag")[1] );
    });

});

var Manager =  {
	editingAllowed: false,

	addTag: function() {
		var $tag_name = $(".tag-form input[name=tag_name]").val();
		var $type     = $(".tag-form select[name=tag_type] option:selected").attr("value");

		if ($type === undefined) {
			Global.showNotificationMessage("Please select tag type");
			return;
		}

		if ($tag_name === "") {
			Global.showNotificationMessage("Please enter a tag name");
			return;
		}

        var $url      = "add_new_tag=yes&tag_name=" + $tag_name + "&tag_type="+ $type;
        $.post("./ajax/ajax.save.php", $url, function(e) {
         	Global.showNotificationMessage(e);
        });
	},

	changeEditing: function() {
        var $text = ( $(".tags-table .edit").text() == "Edit" ) ? "OK" : "Edit";
        $(".tags-table .edit").text($text);

        if (!Manager.editingAllowed) {
            $(".tags-table tr td.edit-name").attr("contenteditable", "true");
            Manager.editingAllowed = true;
        } else {
            $(".tags-table tr td.edit-name").attr("contenteditable", "false");
            Manager.editingAllowed = false;
        } 
	},

	updateTagName: function($tag_id, $value) {
		if ($tag_id == null) throw "$tag_id was not set in Manager.updateTagName()";
		if ($value == null) throw "$value was not set in Manager.updateTagName()";
        
        if ($value === "") {
        	Global.showNotificationMessage("Tag name cannot be empty");
        	return;
        }

		var $url = "update_tag_name=yes&tag_id=" +$tag_id + "&value="+ $value;
		$.post("./ajax/ajax.save.php", $url, function(e) {
			Global.showNotificationMessage(e);
		});
	},

	removeTag: function($tag_id) {
        if ($tag_id == null) throw "$tag_id was not set in Manager.removeTag()";

        var $url = "delete_tag=yes&tag_id=" + $tag_id;
        $.post("./ajax/ajax.delete.php", $url, function(e) {
        	Global.showNotificationMessage(e);
        });
	}
}