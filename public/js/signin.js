$(function() {
    $(".login-btn").on("click", function(e) {
         e.preventDefault();
         Session.login();
    });
});


var Session = {

    login: function() {
    	var $data = $(".login-form").serialize();

    	$.post("/ajax/ajax.session.php", $data, function(e) {
            if(e.indexOf("successful") != -1) {
                window.location.href = "/home/";
                return false;
            } else {
                Global.showNotificationMessage(e);
            }	
    	});
    }
}