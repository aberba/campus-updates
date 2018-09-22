$(function() {
    $(".form .welcome-btn").on("click", function(e) {
    	e.preventDefault();
    	PassRecovery.showForm();
    });
});


var PassRecovery =  {
	showForm: function() {
        $(".welcome-form").fadeOut("slow", function() {
        	$(".email-form").fadeIn("slow");
        	$(".email-form .email-btn").bind("click", function(e) {
        		e.preventDefault();
        		PassRecovery.sendRecovery();
        	});
        });
	},

	sendRecovery: function() {
        var $email = $(".email-form input[name=email]").val(), 
            $url   = "send_recovery=yes&email=" + $email;

        if ($email == "") {
        	Global.showNotificationMessage("Please enter your account email address");
        	return;
        }

        $.post("/ajax/ajax.session.php", $url, function(e) {

        	if (e.indexOf("successfully") != -1) {
        		$(".email-form input[name=email]").val("");
        		var $p   = $("<p />", { "class":"message" }).append(e), 
        		    $div = $("<div />", { "class": "message-box" }).append($p);

        	    $(".email-form").hide();
        	    $(".recovery-section").append($div);
        	    $div.hide().fadeIn("slow");

            } else {
            	Global.showNotificationMessage(e);
            }
        });
	}
}