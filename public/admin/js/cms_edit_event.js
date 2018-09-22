$(function() {
    Edit.hideAllTabs();
    Edit.showTab("information");
    
    /********  toggle Tabs ****************/
    $(".tabs-section li a").on("click", function(e) {
           e.preventDefault();
           $(".tabs-section li a").removeClass("current");
           $(this).addClass("current");

           var $id = $(this).attr("id");
           if(!$id) throw "No ID is set for tab in tabs section";
           Edit.showTab($id);
    });

    /******** Add Tag ****************/
    $(".information-form .tag-list .add-tag").on("click", function(e) {
          e.preventDefault();
          Edit.addTag();
    });
    
    $(".information-form .tag-list ul li.tag .delete").on("click", function(e) {
          e.preventDefault();
          Edit.removeTag( $(this).parent().attr("id") );
    });


    $(".information-form button.save").on("click", function(e) {
          e.preventDefault();
          Edit.saveEvent();
    });

    $(".information-form button.cancel").on("click", function(e) {
          e.preventDefault();
          Global.redirectTo("cms_events.php");
    });


	  $(".images-form button[name=upload-btn]").on("click", function(e) {
	        e.preventDefault();
          Edit.uploadImage();
	  });

    $(".images-form button[name=remove-btn]").on("click", function(e) {
          e.preventDefault();
          Edit.removeImage();
    });

    $(".images-form button[name=preview-btn]").on("click", function(e) {
          e.preventDefault();
          Edit.previewImage();
    });
});

Edit = {
	 hideAllTabs: function() {
        $(".form").hide();
	 },

	 showTab: function(type) {
		/*
          options are information, images and attachement
		*/
		switch(type) {
            case 'information':
               Edit.hideAllTabs();
               $(".information-form").show();
               break;
            case 'images':
               Edit.hideAllTabs();
               $(".images-form").show();
               break;
            case 'attachments':
               Edit.hideAllTabs();
               $(".attachment-form").show();
               break;
            default:
               return;
               break;
		}
	 },

   addTag: function() {
      var $tag_id  = $(".information-form .tag-list select[name=tags] option:selected").attr("value");
      var $item_id = $(".information-form input[name=event_id]").val(); // from images form
     
      if ($tag_id === undefined) {
          Global.showNotificationMessage("Please select tag to add");
          return;
      }

      var $url = "tag_item=yes&type=event&item_id=" + $item_id +"&tag_id=" + $tag_id;

      $.post("./ajax/ajax.save.php", $url, function(e) {
          Global.showNotificationMessage(e);
      });
  },

  removeTag: function($tag_id) {
      if ($tag_id == null) throw "$tag_id was not set in Edit.removeTag()";
      var $item_id = $(".information-form input[name=event_id]").val(); // from images form
      var $url = "remove_item_tag=yes&type=event&item_id=" + $item_id +"&tag_id=" + $tag_id;

      $.post("./ajax/ajax.delete.php", $url, function(e) {
          Global.showNotificationMessage(e);
          if (e.indexOf("successfully") != -1) {
              $(".information-form .tag-list ul li#"+ $tag_id).fadeOut("slow");
          }
      });
  },


	 saveEvent: function() {
        var $data = $(".information-form").serialize();
        $.post("./ajax/ajax.save.php", $data, function(e) {
          	Global.showNotificationMessage(e);
        });
	 },

   uploadImage: function() {
    	  var $exts = ["png", "jpg", "gif"];
        var $event_id = $(".images-form input[name=event_id]").val();
        var $column  = $(".images-form select[name=image_type] option:selected").attr("value");
        var $file_name = $(".images-form input[name=file]").val();
        var $file = $(".images-form input[name=file]")[0].files[0];

        if($column === "" || $column === undefined) {
          Global.showNotificationMessage("Please select select image number");
          return;
        }
 
        if($file_name == "") {
        	Global.showNotificationMessage("Please select an image file to upload");
        	return;
        }
        var $ext  = String.substr($file_name, $file_name.lastIndexOf(".") +1);
      
        if(!$ext in $exts) {
        	Global.showNotificationMessage("File extension is not supported");
        	return;
        }
        
        if(!window.FormData){
        	Global.showNotificationMessage("Sorry, your browser does not support form FormData");
        	return;
        }

        var $formdata = new FormData();
        $formdata.append("file", $file);
        $formdata.append("upload_event_image", "yes");
        $formdata.append("event_id", $event_id);
        $formdata.append("type", "event");
        $formdata.append("column", $column);
        
        
        $.ajax({
        	type: "POST",
        	url: "./ajax/ajax.upload.php",
        	data: $formdata,
        	contentType: false,
        	processData: false,
        	success: function(e) {
                Global.showNotificationMessage(e);
        	},
        	error: function(e) {
                 Global.showNotificationMessage("Opps! error connecting to server");
        	}
        });
   },

   removeImage: function() {
        var $event_id = $(".images-form input[name=event_id]").val();
        var $column  = parseInt($(".images-form select[name=image_type] option:selected").attr("value"), 10);
        if($column === "" || $column === undefined) {
            Global.showNotificationMessage("Please select image type to remove");
            return;
        }

        $url = "remove_image=yes&type=event&event_id="+$event_id+"&column="+$column;
        $.post("./ajax/ajax.delete.php", $url, function(e) {
            Global.showNotificationMessage(e);

            if(e.indexOf("successfully") != -1) {
                $image_class = null;

                switch(parseInt($column, 10)) {
                  case 1:
                      $image_class = "image-one";
                      break;
                  case 2:
                      $image_class = "image-two";
                      break;
                  case 3:
                      $image_class = "image-three";
                      break;
                  default:
                      return;
                      break;
                }

                $(".current-images img."+$image_class).attr("src", "");
            }
        });

   },

   previewImage: function() {
         var $selected, $file_class, $lightbox, $container, $img, $src, $close;
         $selected  = parseInt($(".images-form select[name=image_type] option:selected").attr("value"), 10);

         switch($selected) {
            case 1:
                $file_class = "image-one";
                break;
            case 2:
                $file_class = "image-two";
                break;
            case 3:
                $file_class = "image-three";
                break;
            case 4:
                $file_class = "image-four";
                break;
            case 5:
                $file_class = "image-five";
                break;
            case 6:
                $file_class = "image-six";
                break;
            default:
                $file_class = null;
                break;
         }

         if($file_class === null) {
             Global.showNotificationMessage("Please select an image type to preview");
             return;
         }

         $src       = $(".current-images ." + $file_class).attr("src");
         $lightbox  = $("<div />", {"class":"lightbox"});
         $container = $("<div />", {"class":"container"});
         $close     = $("<p />", {"text":"x", "class":"close"});
         $img       = $("<img />", {"class":"preview-image", "src":$src, "alt":"No preview available"});
         $container.append($close).append($img);
         $lightbox.append($container);
         $("body").append($lightbox);
         $lightbox.hide().fadeIn("slow");

         $close.bind("click", function() {
             $lightbox.fadeOut("slow").replaceWith(" ");
         });
   }
}