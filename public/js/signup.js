$(function() {
    $("button.signup").on("click", function(e) {
        e.preventDefault();
        Signup.validateName($("input[name=fname]").val(), $("input[name=lname]").val());
        Signup.validateUsername($("input[name=uname]").val());
        Signup.validateEmail($("input[name=email]").val());
        Signup.validatePasswords($("input[name=pass1]").val(), $("input[name=pass2]").val());
        Signup.createAccount();
    });
});

var Signup = {
	allowSignup: false,
	minNameLength: 3,
	minUsernameLength: 3,
	minPasswordLength: 8,
	minEmailLength: 6,
	
	validateName: function($fname, $lname) {
		if ($fname == null) throw "$fname was not set in validateName()";
		if ($lname == null) throw "$lname was not set in validateName()";
		if ($fname.length < Signup.minNameLength) {
			Global.showNotificationMessage("First name be at least " + Signup.minNameLength+ " alphabets");
			Signup.allowSignup = false;
			return false;
		} else if ($lname.length < Signup.minNameLength) {
			Global.showNotificationMessage("Last name must be at least " + Signup.minNameLength+ " alphabets");
			Signup.allowSignup = false;
			return false;
		}
		Signup.allowSignup = true;
	},

	validateUsername: function($user_name) {
		if(!Signup.allowSignup) return false;

		if($user_name == null) throw "$user_name was not set in validateUsername()";

		if($user_name.length < Signup.minUsernameLength) {
			Global.showNotificationMessage("Username must be at least " + Signup.minUsernameLength + " alphanumeric characters");
            Signup.allowSignup = false;
            return false; 
		} 
		Signup.allowSignup = true;
	},

	validateEmail: function($email) {
		if(!Signup.allowSignup) return false;

        if($email == null) throw "$email was not set in validateEmail()";
        if ($email.length < Signup.minEmailLength) {
        	Global.showNotificationMessage("Email Address must be at least " + Signup.minEmailLength + " characters");
        	Signup.allowSignup = false;
        	return false;
        }
        Signup.allowSignup = true;
	},

	validatePasswords: function($pass1, $pass2) {
		if(!Signup.allowSignup) return false;

		if($pass1 == null) throw "$pass1 was not set in validatePasswords()";
		if($pass2 == null) throw "$pass2 was not set in validatePasswords()";

		if($pass1.length < Signup.minPasswordLength) {
			Global.showNotificationMessage("Password must be at least " + Signup.minPasswordLength + " characters");
			Signup.allowSignup = false;
			return false;
		} else if ($pass1 !== $pass2) {
			Global.showNotificationMessage("The two passwords donnot match");
			Signup.allowSignup = false;
			return false;
		}
		Signup.allowSignup = true;
	},

	createAccount: function() {
		if(!Signup.allowSignup) return false;
		$("button.signup").fadeOut("slow");

		var $data = $(".signup-form").serialize();
		$.post("/ajax/ajax.session.php", $data, function(e) {
			Global.showNotificationMessage(e);
			if (e.indexOf("successfully") != -1) {

				setTimeout(function() {
					window.location.replace("/signin/");
				}, 2000);
				
			}
			$("button.signup").fadeIn("slow");
		});
	}

}