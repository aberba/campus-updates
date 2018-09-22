$(function() {
       $(".capture img").on("click", function() {
       	   var $id = $(this).parent().attr("id").split("capture")[1];
       	   Capture.showPreviewLightbox($id);
       });

       $("button.apply").on("click", function(e) {
           e.preventDefault();
           Capture.executeAction();
       });

       $("button.add").on("click", function(e) {
           e.preventDefault();
           Capture.addNew();
       });

       $(".capture a.edit").on("click", function() {
           var $id = $(this).parent().attr("id").split("capture")[1];
           Capture.edit($id);
       });
});


Capture = {

    querySelected: function() {
         var $ids = [];
         $("input[name=capture]:checked").each(function() {
             var $id = $(this).attr("value");
             $ids.push($id);
         });
         return $ids;
    },

    resetAction: function() {
         $("select[name=action] option:first").attr("selected", "selected");
    },

    executeAction: function() {
         var $ids = Capture.querySelected();
         var $action = $("select[name=action] option:selected").val();

         if($ids.length === 0) {
             Global.showNotificationMessage("No item has been checked!");
             return;
         }

         switch($action) {
            case 'delete':
               Capture.deleteCaptures($ids);
               break;
            case 'publish':
               Capture.changeCapturesStatus($ids);
               break;
            default:
               Global.showNotificationMessage("Please select an action to apply");
               return;
               break;
         }
    },

    addNew: function() {
        var $lightbox, 
            $container, 
            $close, 
            $form, 
            $label_caption,
            $label_file,
            $input_file, 
            $input_caption, 
            $save_btn, 
            $cancel_btn;

        $label_file    = $("<label />", { "for":"file", "text":"Select Image File to Upload" });
        $input_file    = $("<input />", { "type":"file", "name":"file" });
        $label_caption = $("<label />", { "for":"caption", "text":"Enter a caption for image file" });
        $input_caption = $("<input />", { "type":"text", "name":"caption" });

        $save_btn      = $("<button />", { "type":"button", "class":"save button", "text":" Save " }).bind("click", function() {
            Capture.saveNew();
        });

        $cancel_btn    = $("<button />", { "type":"button", "class":"cancel button", "text":" Cancel " }).bind("click", function() {
            Capture.removeLightbox();
        });
        
        $form          = $("<form />", {"class":"upload-form form"}).append($label_file).append($input_file).append($label_caption).append($input_caption).append($save_btn).append($cancel_btn);
        
        $close        = $("<p />", { "class":"close", "text":"x" }).bind("click", function() {
            Capture.removeLightbox();
        });

        $container    = $("<div />", { "class":"container" }).append($close).append($form);
        $lightbox     = $("<div />", { "class":"lightbox" }).append($container);

        $("body").append($lightbox);
        $container.hide().fadeIn("slow");
    },

    saveNew: function() {
        var $file_name, 
            $file, 
            $formdata;
        
        $caption   = $(".lightbox .container .upload-form input[name=caption]").val();
        $file_name = $(".lightbox .container .upload-form input[name=file]").val();
        $file      = $(".lightbox .container .upload-form input[name=file]")[0].files[0];

        if( $caption.length <= 0 ) {
             Global.showNotificationMessage("Please enter caption text for image file");
             return;
        }
        
        if($file_name === "") {
            Global.showNotificationMessage("Please select image file to uplaod");
            return;
        }
 
        if (!window.FormData) {
            Global.showNotificationMessage("Sorry, you browser does no support FormData()");
            return;
        }
        
        $formdata = new FormData();
        $formdata.append("upload_new_capture", "yes");
        $formdata.append("type", "capture");
        $formdata.append("caption", $caption);
        $formdata.append("file", $file);

        $.ajax({
            type: "POST",
            url: "./ajax/ajax.upload.php",
            data: $formdata,
            contentType: false,
            processData: false,
            cache: false,
            success: function(e) {
                Global.showNotificationMessage(e);

                if(e.indexOf("successfully") != -1) {
                    Capture.removeLightbox();
                }
            },
            error: function(e) {
                Global.showNotificationMessage("Ooops!, error connecting to server");
            }
        });
    },

    edit: function($capture_id) {
        if ($capture_id == null) throw "$capture_id was not set in edit()";
        var $url, 
            $data, 
            $label_caption, 
            $input_caption, 
            $id_input, 
            $form, 
            $close,
            $save_btn, 
            $cancel_btn, 
            $container, 
            $lightbox;

        $url = "fetch_capture=yes&capture_id=" + $capture_id;
        $.get("./ajax/ajax.query.php", $url, function(e) {
             if(e == 0) {
                Global.showNotificationMessage("Ooops!, error fetching capture: " + $capture_id);
                return;
             }

            $data = JSON.parse("["+ e + "]");

            $close = $("<p />", { "class":"close", "text":"x" }).bind("click", function() {
                Capture.removeLightbox();
            });
            $form = $("<form />", {"class":"edit-form form"}).append($close);

            for (i in $data) {
                $label_caption = $("<label />", {"for":"caption", "text":"Caption: "});
                $input_caption = $("<input />", {"type":"text", "name":"caption", "value":$data[i].caption, "placeholder":"Caption here"});
                $id_input      = $("<input />", {"type":"hidden", "name":"caption_id", "value":$capture_id});
                $form.append($label_caption).append($input_caption).append($id_input);
            }
            
            $save_btn   = $("<button />", {"type":"button", "class":"save button", "text":" Save "}).bind("click", function() {
                Capture.saveEditedCapture($capture_id, $input_caption.val());
            });

            $cancel_btn = $("<button />", {"type":"button", "class":"cancel button", "text":" Cancel "}).bind("click", function() {
                Capture.removeLightbox();
            });

            $form.append($save_btn).append($cancel_btn);

            $container = $("<div />", {"class":"container"}).append($form);
            $lightbox  = $("<div />", {"class":"lightbox"}).append($container);
            
            $("body").append($lightbox);
            $lightbox.hide().fadeIn("slow");
        });
    },

    saveEditedCapture: function($capture_id, $caption) {
        if ($capture_id == null) throw "$capture_id was not set in saveEditedCapture()";
        if ($caption == null) throw "$caption was not set in saveEditedCapture()";

        var $url = "save_edited_capture=yes&capture_id=" + $capture_id + "&caption=" + $caption;

        $.post("./ajax/ajax.save.php", $url, function(e) {
            Global.showNotificationMessage(e);

            if(e.indexOf("successfully") != -1) {
                Capture.removeLightbox();
            }
        });
    },

    changeCapturesStatus: function($ids) {
        if ($ids == null) throw "$ids was not set in changeCapturesStatus()";

        var $formdata, 
            $ids_lenght, 
            i, 
            $current_status;

        if(!window.FormData) {
            Global.showNotificationMessage("Sorry, your browser does not support FormData()");
            return;
        }

        $formdata = new FormData();

        $formdata.append("change_captures_status", "yes");
         
        $ids_lenght = $ids.length;
        for(i = 0; i < $ids_lenght; i++) {
            $formdata.append("capture_id[]", $ids[i]);
        }

        $.ajax({
            type: "post",
            url: "./ajax/ajax.save.php",
            data: $formdata,
            contentType: false,
            processData: false,
            cache: false,
            success: function(e) {
                Global.showNotificationMessage(e);
                
                if (e.indexOf("successfully") != -1) {
                    var i=0;
                    for ( i; i < $ids_lenght; i++ ) {
                        $current_status = $("#capture" + $ids[i]).children(".status").text();
                        $new_status = ($current_status.trim() == "Publish") ? "Unpublish" : "Publish";
                        $("#capture" + $ids[i]).children(".status").text($new_status);
                    }
                }
                Capture.resetAction();
            },
            error: function(e) {
                Global.showNotificationMessage("Ooops!, error connecting to server.");
            }
        });
    },

    deleteCaptures: function($ids) {
        var $formdata, i;

        if ($ids == null) throw "$ids was no set in deleteCaptures()";

        if(!window.FormData) {
            Global.showNotificationMessage("Sorry, your browser does not support FormData()");
            return;
        }

        $formdata = new FormData();

        $formdata.append("delete_captures", "yes");
         
        $ids_lenght = $ids.length;
        for(i = 0; i < $ids_lenght; i++) {
            $formdata.append("capture_id[]", $ids[i]);
        }

        $.ajax({
            type: "post",
            url: "./ajax/ajax.delete.php",
            data: $formdata,
            contentType: false,
            processData: false,
            cache: false,
            success: function(e) {
                Global.showNotificationMessage(e);

                if (e.indexOf("successfully") != -1) {
                    var i=0;
                    for ( i; i < $ids_lenght; i++ ) {
                        $("#capture" + $ids[i]).fadeOut("slow");
                    }
                }
                Capture.resetAction();
            },
            error: function(e) {
                Global.showNotificationMessage("Ooops!, error connecting to server.");
            }
        });
    },

    showPreviewLightbox: function(captureID) {
        var $url, 
            $close,
            $next,
            $prev,
            $slideWrap,
            $subContainer,
            $container,
            $lbox,

            $data,
            $img,
            $current;


    	 if(captureID === undefined || captureID == "") throw new Error("captureID is undefined");

    	 $url = "show_capture_lightbox=yes&id="+captureID;
    	 $.get("./ajax/ajax.query.php", $url, function(data) {
             if(data == 0) return false;
             
             $close = $("<p />", {"class":"close", "text":" x "}).bind("click", function() {
                Capture.removeLightbox();
             });

             $next = $("<span />", {"class":"next arrow", "text":"nex"}).bind("click", function() {
                Capture.slideNext();
             });

             $prev      = $("<span />", {"class":"previous arrow", "text":"pre"}).bind("click", function() {
                Capture.slideBack();
             });

             $slideWrap = $("<div />", {"class":"slide-wrap"});
             $subContainer = $("<div />", {"class":"sub-container"}).append($slideWrap);
             $container = $("<div />", {"class":"preview-container container"}).append($close).append($next).append($prev).append($subContainer);
             $lbox      = $("<div />", {"class":"lightbox"}).append($container);

             $data  = JSON.parse(data);
             for (var i in $data) {
             	 $img = $("<img />", {"class":"slide", "id":"slide"+ $data[i].capture_id, "alt":$data[i].caption, "src":"../uploads/captures/"+ $data[i].file_name});
             	 $slideWrap.append($img);
             }
             $current = $slideWrap.children("img#slide"+captureID);
             $current.addClass("current");
             $subContainer.append($("<div />", {"class":"caption", "html":$current.attr("alt")}));
             
             $("body").append($lbox).hide().fadeIn("slow");
    	 });
    },

    removeLightbox: function() {
        $(".lightbox").fadeOut("slow").replaceWith(" ");
    },

    slideNext: function() {
        $(".lightbox .slide-wrap img.slide").removeClass("previous");
        var $current = $(".lightbox .slide-wrap img.current");
        var $next    = $current.next();
        if(!$next.is("img.slide")) {
            $next    = $(".lightbox .slide-wrap img.slide:first");
        }

        $next.animate({left: $(".lightbox .slide-wrap").width()+"px"}, 1000, function() {
            $next.removeClass("previous").addClass("current").addClass("topmost");
        });
        $current.addClass("previous").removeClass("current");
        $next.animate({left: 0}, 1000, function() {
            $current.removeClass("current");
            $next.removeClass("topmost");
            $(".lightbox .container .caption").html($next.attr("alt"));
        });  
    },

    slideBack: function() {
        $(".lightbox .slide-wrap img.slide").removeClass("previous");
        var $current = $(".lightbox .slide-wrap img.current");
        var $prev    = $current.prev();
        if(!$prev.is("img.slide")) {
            $prev    = $(".lightbox .slide-wrap img.slide:last");
        }

        $prev.animate({left: -$(".lightbox .slide-wrap").width()+"px"}, 1000, function() {
            $prev.removeClass("previous").addClass("current").addClass("topmost");
        });
        $current.addClass("previous").removeClass("current");
        $prev.animate({left: 0}, 1000, function() {
            $current.removeClass("current");
            $prev.removeClass("topmost");
            $(".lightbox .container .caption").html($prev.attr("alt"));
        });  
    }

}

