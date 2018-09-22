$(function() {

	 $(".post-options .publish").on("click", function(e) {
	 	 e.preventDefault();
         var $post_id = $(this).parent().parent().parent().parent().attr("id").split("post")[1];
	 	 Posts.changePostState($post_id);
	 });

	 $(".post-options .block-comments").on("click", function(e) {
	 	 e.preventDefault();
         var $post_id = $(this).parent().parent().parent().parent().attr("id").split("post")[1];
	 	 Posts.changePostCommentingStatus($post_id);
	 });

	 $(".post-options .delete").on("click", function(e) {
	 	 e.preventDefault();
         var $post_id = $(this).parent().parent().parent().parent().attr("id").split("post")[1];
	 	 Posts.deletePost($post_id);
	 });

	 $(".post-options .preview").on("click", function(e) {
	 	 e.preventDefault();
         var $post_id = $(this).parent().parent().parent().parent().attr("id").split("post")[1];
	 	 Posts.previewPost($post_id);
	 });

	 $(".post-options .view-comments").on("click", function(e) {
	 	 e.preventDefault();
         var $post_id = $(this).parent().parent().parent().parent().attr("id").split("post")[1];
	 	 Posts.viewComments($post_id);
	 });
      
});

Posts = {

    changePostState: function($post_id) {
    	var $url,
     	    $publish_status;

     	 if ($post_id == null) throw "Post ID was not set in changePostState()";

     	 $url = "change_post_state=yes&post_id="+ $post_id;
     	 $.post("./ajax/ajax.save.php", $url, function(e) {
     	 	 Global.showNotificationMessage(e);

     	 	 $publish_status = (e.indexOf("shown") != -1) ? "Unpublish" : "Publish";
     	 	 $("#post"+ $post_id +" .post-options .publish").text($publish_status);
     	 });
    },

    changePostCommentingStatus: function($post_id) {
     	var $url, 
     	    $comments_status;

     	if ($post_id == null) throw "Post ID was not set in changePostCommentingStatus()";

     	$url = "change_commenting_status=yes&post_id="+ $post_id;
     	$.post("./ajax/ajax.save.php", $url, function(e) {
     	 	 Global.showNotificationMessage(e);

     	 	 $comments_status = (e.indexOf("blocked") != -1) ? "Unblock Comments" : "Block Comments";
     	 	 $("#post"+ $post_id +" .post-options .block-comments").text($comments_status);
     	});
    },

    previewPost: function($post_id) {
     	if ($post_id == null) throw "Post ID was not set in previewPost()";

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

         $url = "fetch_post=yes&post_id="+ $post_id;
     	 $.get("./ajax/ajax.query.php", $url, function(e) {
     	 	 
     	 	 if(e == 0) {
     	 	 	Global.showNotificationMessage("Ooops!, error fetching post: " + $post_id);
     	 	 	return;
     	 	 }
     	 	 
     	 	 $data = JSON.parse("["+ e +"]");
             
             $img = $("<img />", {"class":"image"}); //image tag to be cloned for all images
             $images_path = "../uploads/posts";

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
                 
                 $url_author = $("<a />", {"href": $data[i].owner_url_address, "html":$data[i].owner_name, "target":"_blank"});
                 $li_author = $("<li />", {"html":"Author: "}).append($url_author);
                 $li_readers = $("<li />", {"html":"Readers: "+$data[i].num_readers});
                 $li_comments = $("<li />", {"html":"Comments: "+$data[i].num_comments});
                 $li_date = $("<li />", {"html":"Date: "+$data[i].date_added});
                 $ul = $("<ul />").append($li_author).append($li_readers).append($li_comments).append($li_date);

                 $info_div = $("<div />", {"class":"post-info-div clearfix"}).append($front_image).append($ul);
                 $content_p = $("<p />", {"html":$data[i].content_formatted});
                 $content_div = $("<div />", {"class":"content-div clearfix"}).append($content_p);
 
                 $li_file1 = $("<li />", {"html":$data[i].file1});
                 $li_file2 = $("<li />", {"html":$data[i].file2});
                 $li_file3 = $("<li />", {"html":$data[i].file3});
                 $attachment_ul = $("<ul />").append($li_file1).append($li_file2).append($li_file3);
                 $attachment_div = $("<div />", {"class":"attachment-div"}).append($attachment_ul);
                       
                 $container.append($close).append($title).append($info_div).append($content_div).append($attachment_div);
     	 	 }

             $lightbox.append($container);
             $("body").append($lightbox);
             $lightbox.hide().fadeIn("slow");

     	 });
    },

    deletePost: function($post_id) {
         if($post_id == null) throw "post_id was not set in deletePost()";

         var $url = "delete_post=yes&post_id=" + $post_id;
         $.post("./ajax/ajax.delete.php", $url, function(e) {
         	 Global.showNotificationMessage(e);

         	 if(e.indexOf("successfully") != -1) {
         	 	  $("#post"+$post_id).fadeOut("slow");
         	 }
         })
    },

    viewComments: function($post_id) {
     	 if ($post_id == null) throw "Post ID was not set in ViewComments()";

     	 var $data, 
     	     $url, 
     	     $comment_div,
     	     $figure,
     	     $img,
     	     $comment_p,
     	     $info_div,
             $info_p,
             $user_name,
             $date,
             $edit_link,
             $delete_link,
             $publish_link,
             $publish_status;

     	 $url = "fetch_comments=yes&post_id="+ $post_id;
     	 $.get("./ajax/ajax.query.php", $url, function(e) {
     	 	 if(e == 0) {
     	 	 	Global.showNotificationMessage("No comment is posted on post: " + $post_id);
     	 	 	return;
     	 	 }
     	 	 
     	 	 $data = JSON.parse(e);        
             $avatars_path = "../uploads/avatars";

             $close       = $("<p />", {"class":"close", "text":"x"}).bind("click", function() {
                  $lightbox.fadeOut("slow").replaceWith(" ");
             });

             $container    = $("<div />", {"class":"container"}).append($close);

     	 	 for(i in $data) {
                 $img    = $("<img />", {"src":$avatars_path + "/" +$data[i].avatar});
                 $figure = $("<figure />", {"class":"avatar"}).append($img);

                 $comment_p = $("<p />", {"html":$data[i].comment, "class":"comment-text"});
                 $user_name = "<strong>"+ $data[i].user_name+ "</strong> ";
                 $date      = $("<span />", { "text": $data[i].date });

                 $edit_link = $("<a />", { "href":"#", "text":"Edit", "class":"edit", "id":$data[i].comment_id }).bind("click", function(e) {
                 	e.preventDefault();
                 	var $id = $(this).attr("id");
                 	Posts.editComment($id);
                 });

                 $delete_link = $("<a />", {"href":"#", "text":"Delete", "class":"delete", "id":$data[i].comment_id }).bind("click", function(e) {
                 	e.preventDefault();
                 	var $id = $(this).attr("id");
                 	Posts.deleteComment($id);
                 });
                 
                 $publish_status = (parseInt($data[i].publish, 10) == 1) ? "Unpublish" : "Publish";
                 $publish_link = $("<a />", {"href":"#", "text":$publish_status, "class":"publish", "id":"publish" + $data[i].comment_id }).bind("click", function(e) {
                 	e.preventDefault();
                 	var $id = $(this).attr("id").split("publish")[1];
                 	Posts.changeCommentStatus($id);
                 });

                 $info_p    = $("<p />", { "class":"comment-info" }).append($user_name).append($date).append(" &nbsp;&nbsp;&nbsp;&nbsp; ").append($edit_link).append(" &nbsp; ").append($delete_link).append(" &nbsp; ").append($publish_link);
                 $info_div  = $("<div />", { "class":"info-div" }).append($info_p);

                 $comment_div = $("<section />", {"id":"comment"+$data[i].comment_id, "data-post_id":$data[i].post_id, "class":"comment"}).append($figure).append($comment_p).append($info_div);
                 $container.append($comment_div);
     	 	 }
             
             $lightbox = $("<div />", {"class":"lightbox"}).append($container);
     	 	 $("body").append($lightbox);
             $lightbox.hide().fadeIn("slow");
     	 	 
     	 });
    },

    editComment: function($comment_id) {
     	 var $url, 
     	     $data, 
     	     i,
     	     $form, 
     	     $label,
     	     $textarea, 
     	     $input_comment_id;

     	 if ($comment_id == null) throw "Comment ID was not set in editComment()";

     	 $url = "query_comment=yes&comment_id="+$comment_id;
     	 $.get("./ajax/ajax.query.php", $url, function(e) {
             //alert(e);
     	 	 if (e == 0) {
     	 	 	 Global.showNotificationMessage("Ooops!, error querying comment: "+$comment_id);
     	 	 	 return;
     	 	 }

     	 	 $data = JSON.parse("["+e+"]");             
             $form = $("<form />", { "class":"comment-form form" });

     	 	 for(i in $data) {
	     	 	 $label = $("<label />",{ "for":"comment_content", "text":"Edit Comment & Save" });
	     	 	 $textarea =$("<textarea />", {"name":"comment_content" ,"html":$data[i].comment, "placeholder":"Edit Comment"});
	     	 	 $form.append($label).append($textarea);
     	 	 }

     	 	 $input_comment_id = $("<input />", { "type":"hidden", "name":"comment_id", "value":$data[i].comment_id });

     	 	 $save_btn = $("<button />", { "type":"button", "class":"save button", "text":" Save " }).bind("click", function(e) {
     	 	 	e.preventDefault();
     	 	 	Posts.saveComment($comment_id, $textarea.val());
     	 	 });

     	 	 $cancel_btn = $("<button />", { "type":"button", "class":"cancel button", "text":" Cancel " }).bind("click", function(e) {
     	 	 	 $form.fadeOut("slow").replaceWith(" ");
     	 	 });

     	 	 $form.append($input_comment_id).append($save_btn).append($cancel_btn);	 

     	 	 $(".lightbox .container").append($form);
     	 	 $form.hide().fadeIn("slow");
     	 }); // for showing editting form
    },

    saveComment: function($comment_id, $comment_text) {
    	var $url;

        if ($comment_id == null) throw "Comment ID was not set in saveComment()";
        if ($comment_text == null) throw "comment_text was not set in saveComment()";

          $url = "save_comment=yes&comment_id=" + $comment_id + "&comment=" + $comment_text;
          $.post("./ajax/ajax.save.php", $url, function(e) {
          	  Global.showNotificationMessage(e);

          	  if(e.indexOf("successfully") != -1) {
          	  	  $(".lightbox .comment-form").fadeOut("slow").replaceWith(" ");
          	  }
          }); // for saving edited comment
    },

    deleteComment: function($comment_id) {
        if($comment_id == null) throw "comment_id was not set in deleteComment()";

        var $url = "delete_comment=yes&comment_id="+$comment_id;
        var $conform = window.confirm("Are you sure you want to delet this comment?");

        if($conform) {
	        $.post("./ajax/ajax.delete.php", $url, function(e) {
	            Global.showNotificationMessage(e);

	            if(e.indexOf("successfully") != -1) {
	            	$(".lightbox .container #comment" + $comment_id).fadeOut("slow");
	            }
	        });
        }
    },

    changeCommentStatus: function($comment_id) {
    	var $url, 
    	    $comment_status;

        if($comment_id == null) throw "comment_id was not set in changeCommentStatus()";

        $url = "change_comment_status=yes&comment_id=" + $comment_id;
        $.post("./ajax/ajax.save.php", $url , function(e) {
            Global.showNotificationMessage(e);
            
            $comment_status = (e.indexOf("shown") != -1) ? "Unpublish": "Publish";
            $(".comment #publish" + $comment_id).text($comment_status);
        });    
    }
}