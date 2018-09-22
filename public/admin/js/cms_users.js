$(function() {
    $(".users-table .freeze").on("click", function(e) {
    	e.preventDefault();
    	var $user_id = $(this).parent().parent().attr("id").split("user")[1];
    	Users.freezeAccount($user_id);
    })
});


var Users = {
    freezeAccount: function($user_id) {
    	if (!$user_id == null) throw "$user_id was notset in Users.freezeAccount()";

        Global.authenticate(function(e) {
	        if (!e) {
	        	Global.showNotificationMessage("Access denied! Authentication failed.");
	        	return;
	        } 

	        if (!window.confirm("Are you sure you want to freeze this user's account?")) return;

            var $url = "change_account_freeze=yes&user_id="+$user_id;
	        $.post("./ajax/ajax.session.php", $url, function(e) {
	        	Global.showNotificationMessage(e);
	        });
	    });
    }
}