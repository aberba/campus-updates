$(function() {
    $(".comment-form .comment-btn").on("click", function(e) {
    	e.preventDefault();

    	var $post_id = $(".comment-form").attr("id").split("comment-form")[1];
    	var $comment = $(".comment-form textarea[name=comment]").val().trim();
        $Post.addComment($post_id, $comment);
    });
});

__Post__ = function() {}
$Post = new __Post__(); 

__Post__.prototype.addComment = function(post_id, comment) {

    if(post_id === undefined) throw new Error("post_id is undefined in addComment()");
    if(comment === undefined) throw new Error("comment is undefined in addComment()");
    if(comment == "") {
        $Global.showNotificationMessage("Please type something to post");
        return false;
    }

    var $url = "add_comment=yes&post_id="+post_id+"&comment="+comment;
        
    $.post("./ajax/ajax.save.php", $url, function(e) {
    	$Global.showNotificationMessage(e);
    });
}
