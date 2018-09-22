$(function() {
    $(".comment-form .comment-btn").on("click", function(e) {
    	e.preventDefault();
        Post.addComment();
    });

    setTimeout(function() {
        Post.addReader();
    }, 10000);
});


var Post = {

    addReader: function() {
        var $post_id = $(".comment-form").attr("id").split("comment-form")[1];
        var $url = "add_reader=yes&type=post&id=" + $post_id;
        $.post("/ajax/ajax.save.php", $url, function(e) {
            //do nothing
        });
    },

    addComment: function() {

        var $post_id = $(".comment-form").attr("id").split("comment-form")[1];
        var $comment = $(".comment-form textarea[name=comment]").val().trim();

        if($comment == "") {
            Global.showNotificationMessage("Please type something to post");
            return false;
        }

        var $url = "add_comment=yes&post_id=" + $post_id + "&comment=" + $comment;
        $(".comment-form .comment-btn").fadeOut("slow");
            
        $.post("/ajax/ajax.save.php", $url, function(e) {
        	Global.showNotificationMessage(e);
            if (e.indexOf("successfully") != -1) {
               $(".comment-form textarea[name=comment]").val("");
            } 

            $(".comment-form .comment-btn").fadeIn("slow");
        });
    }

}