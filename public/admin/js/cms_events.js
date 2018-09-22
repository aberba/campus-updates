$(function() {
     $(".event .options .publish").on("click", function(e) {
     	e.preventDefault();
     	var $event_id = $(this).parent().parent().parent().parent().attr("id").split("event")[1];
     	Events.changeStatus($event_id);
     });

     $(".event .options .confirm").on("click", function(e) {
     	e.preventDefault();
     	var $event_id = $(this).parent().parent().parent().parent().attr("id").split("event")[1];
     	Events.changeConfirmation($event_id);
     });

     $(".event .options .preview").on("click", function(e) {
     	e.preventDefault();
     	var $event_id = $(this).parent().parent().parent().parent().attr("id").split("event")[1];
     	Events.preview($event_id);
     });

     $(".event .options .delete").on("click", function(e) {
     	e.preventDefault();
     	var $event_id = $(this).parent().parent().parent().parent().attr("id").split("event")[1];
     	Events.deleteEvent($event_id);
     });
});


Events = {
	changeStatus: function($event_id) {
         if($event_id == null) throw "$event_id was not set in changeStatus()";
         var $url = "change_event_status=yes&event_id=" + $event_id;
         $.post("./ajax/ajax.save.php", $url, function(e) {
         	Global.showNotificationMessage(e);

         	var $text = (e.indexOf("shown") != -1) ? "Unpublish" : "Publish";
         	$("#event" + $event_id + " .options .publish").text($text);        	
         });
	},

	changeConfirmation: function($event_id) {
         if($event_id == null) throw "$event_id was not set in changeConfirmation()";
         var $url = "change_event_confirmation=yes&event_id=" + $event_id;
         $.post("./ajax/ajax.save.php", $url, function(e) {
         	Global.showNotificationMessage(e);

         	var $text = (e.indexOf("public") != -1) ? "Unconfirm" : "Confirm";
         	$("#event" + $event_id + " .options .confirm").text($text);	
         });
	},

	preview: function($event_id) {
        if ($event_id == null) throw "Post ID was not set in previewPost()";

         var $url, 
             $data, 
             $lightbox, 
             $container,
             $close, 
             $data, i, 
             $title, 
             $front_image,
             $img,

             $info_div, 
             $ul, 
             $li_author, 
             $li_comments,
             $li_readers,
             $li_date,

             $content_div, 
             $content_p, 

             $attachment_div,
             $attachment_ul, 
             $li_file1,
             $li_file2,
             $li_file3;

         $url = "fetch_event=yes&event_id="+ $event_id;
         $.get("./ajax/ajax.query.php", $url, function(e) {
             alert(e);
             if(e == 0) {
                Global.showNotificationMessage("Ooops!, error fetching post: " + $event_id);
                return;
             }
             
             $data = JSON.parse("["+ e +"]");
             
             $img = $("<img />", {"class":"image"}); //image tag to be cloned for all images
             $images_path = "../uploads/events";

             $close       = $("<p />", {"class":"close", "text":"x"}).bind("click", function() {
                  $lightbox.fadeOut("slow").replaceWith(" ");
             });

             $container   = $("<div />", {"class":"container"}).append($close);
             $lightbox    = $("<div />", {"class":"lightbox"});

             for(i in $data) {
                 $title = $("<h3 />", {"class":"title", "html":$data[i].title});

                 $front_image = $img.clone().attr({
                    src: $images_path + "/" + $data[i].image_one,
                    alt: "Image one",
                    title: "Front Image"
                 });

                 $li_author = $("<li />", {"html":"Owner: "+$data[i].owner_name});
                 $li_readers = $("<li />", {"html":"Readers: "+$data[i].num_readers});
                 $li_date = $("<li />", {"html":"Date: "+$data[i].date_added});
                 $ul = $("<ul />").append($li_author).append($li_readers).append($li_date);

                 $info_div = $("<div />", {"class":"event-info-div clearfix"}).append($front_image).append($ul);
                 $content_p = $("<p />", {"html":$data[i].content_formatted});
                 $content_div = $("<div />", {"class":"content-div clearfix"}).append($content_p);
 
                 $li_file1 = $("<li />", {"html":$data[i].file1});
                 $li_file2 = $("<li />", {"html":$data[i].file2});
                 $li_file3 = $("<li />", {"html":$data[i].file3});
                 $attachment_ul = $("<ul />").append($li_file1).append($li_file2).append($li_file3);
                 $attachment_div = $("<div />", {"class":"attachment-div"}).append($attachment_ul);
                       
                 $container.append($close).append($title).append($content_div).append($info_div);
             }

             $lightbox.append($container);
             $("body").append($lightbox);
             $lightbox.hide().fadeIn("slow");

         });
	},

	deleteEvent: function($event_id) {
        if($event_id == null) throw "$event_id was not set in changeConfirmation()";
        var $url = "delete_event=yes&event_id="+ $event_id;
        $.post("./ajax/ajax.delete.php", $url, function(e) {
         	Global.showNotificationMessage(e);

            if(e.indexOf("successfully") != -1) {
               $("#event" + $event_id).fadeOut("slow");
            }  
        });
	}
}