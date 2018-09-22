$(function() {
    $("h3.toggle").on("click", function() {
    	Account.toggleSection(this);
    });

    $(".activation-section .resend-btn").on("click", function(e) {
        e.preventDefault();
        Account.resendActivation();
    });

    $(".table tr th .edit").bind("click", function(e) {
        e.preventDefault();

        if (!Account.editingAllowed) {
           Account.allowedUpdate();
        } else {
           Account.disallowUpdates();
        }
    });

    $(".password-form .change-pass-btn").on("click", function(e) {
    	e.preventDefault();
    	Account.changePassword();
    });

    $(".upload-form .upload-btn").on("click", function(e) {
    	e.preventDefault();
    	Account.uploadContent();
    });

    $(".table .avatar-box .btn").on("click", function(e) {
        e.preventDefault();
        Account.selectAvatar();
    });
     
    $(".settings-form .save-settings").on("click", function(e) {
        e.preventDefault();
        Account.saveAccountSettings();
    });  
});

var Account = {
    editingAllowed: false,

	toggleSection: function($item) {
         if ($($item).parent().children(".form").is(".form:visible")) {
            return false;
         }
         
         $(".form-section .form").hide("slow");
         $($item).parent().children(".form").slideToggle("slow");
	},

    authenticate: function(callback) {
        var $label     = $("<label />", {"text":"Authenticate with your password."});
        var $input     = $("<input />", {"type":"password", "name":"password", "placeholder":"Enter your account password"});
        var $s         = $("<input />", {"type":"hidden", "name":"authenticate_action", "value":"yes"});
        var $s_btn     = $("<button />", {"class":"authenticate-btn button", "text":"Authenticate"});
        var $c_btn     = $("<button />", {"class":"cancel-btn button", "text":" Cancel "}).bind("click", function(e) {
           e.preventDefault();
           $(".lightbox").fadeOut("slow").replaceWith(" ");
        });
        
        var $form      = $("<form />", {"class":"authentication-form form"}).append($label).append($input).append($s).append($c_btn).append($s_btn);
        var $container = $("<div />", {"class":"container authentication-container"}).append($form);
        var $lightbox  = $("<div />", {"class":"lightbox"}).append($container);

        $s_btn.bind("click", function(e) {
            e.preventDefault();
            var $form = $(".authentication-form").serialize();
            $(".lightbox").fadeOut("slow").replaceWith(" ");

            $.post("/ajax/ajax.session.php", $form, function(e) {
                $result = (e.indexOf("granted") != -1);
                callback($result);
            });
        });     

        $("body").append($lightbox);
        $lightbox.hide().fadeIn("slow");
    },

    resendActivation: function() {
        Account.authenticate(function(e) {
            if (!e) {
                Global.showNotificationMessage("Access denied! Authentication failed.");
                return;
            }       
        

            var $url = "resend_activation=yes";
            $.post("/ajax/ajax.session.php", $url, function(e) {
                Global.showNotificationMessage(e);
            });
        });
    },

    allowedUpdate: function() {  
        Account.authenticate(function(e) {
            if (!e) {
                Global.showNotificationMessage("Access denied! Authentication failed.");
                return;
            }

            var $new_text = ( $(".table tr th .edit").text() === "Edit" ) ? "OK" : "Edit";
            $(".table tr th .edit").text($new_text);

            $(".table tr td.fname").attr("contenteditable", "true");
            $(".table tr td.lname").attr("contenteditable", "true");
            $(".table tr td.uname").attr("contenteditable", "true");
            $(".table tr td.contact_email").attr("contenteditable", "true");
            $(".table tr td.phone_number").attr("contenteditable", "true");

            $(".table tr td[contenteditable=true]").bind("blur", function() {
                var $value      = $(this).text().trim();
                var $field_name = $(this).attr("class");

                if($value === "") {
                    Global.showNotificationMessage("Field should not be empty");
                    return;
                }
                
                switch ($field_name) {
                    case "fname": 
                    case "lname":
                    case "uname":
                        if ($value.length < 3) {
                            var $name = "username";
                            if ($field_name == "fname") {
                                $name = "first name";
                            } else if ($field_name == "lname") {
                                $name = "last name";
                            }

                            Global.showNotificationMessage("Please enter a valid "+ $name);
                            return;
                        }
                        break;
                    case "contact_email":
                        if ($value.length < 6) {
                            Global.showNotificationMessage("Email address is invalid");
                            return;
                        }
                        break;
                    case "phone_number":
                        if ($value.length < 10 || $value.length > 10) {
                           Global.showNotificationMessage("Phone number is invalid");
                           return;
                        }  
                        break;
                    default:
                        throw "Field specified is unknown";
                        return;
                        break;
                }

                var $url = "update_profile=yes&field=" + $field_name + "&value=" + $value;
                $.post("/ajax/ajax.save.php", $url, function(e) {
                    Global.showNotificationMessage(e);
                });
            });
            Account.editingAllowed = true;
        });
    },

    disallowUpdates: function() {
        document.querySelector(".table tr td.fname").removeAttribute("contenteditable", false);
        document.querySelector(".table tr td.lname").removeAttribute("contenteditable", false);
        document.querySelector(".table tr td.uname").removeAttribute("contenteditable", false);
        document.querySelector(".table tr td.contact_email").removeAttribute("contenteditable", false);
        document.querySelector(".table tr td.phone_number").removeAttribute("contenteditable", false);

        var $new_text = ( $(".table tr th .edit").text() == "Edit" ) ? "OK" : "Edit";
        $(".table tr th .edit").text($new_text);
        Account.editingAllowed = false;
    },

    selectAvatar: function() {
        $(".table .avatar-form input[name=avatar]").bind("change", function() {
            Account.uploadAvatar();
        });
        $(".table .avatar-form input[name=avatar]").click();
    },

    uploadAvatar: function() {
        var $file_name = $(".table .avatar-form input[name=avatar]").val().trim();
        var $file      = $(".table .avatar-form input[name=avatar]")[0].files[0];

        if ($file_name === "") {
            Global.showNotificationMessage("Please select an image file to upload.");
            return;
        }

        if (!window.FormData) {
             Global.showNotificationMessage("Your browser does not support FormData()<br> We recommend a recent version of Mozilla Firefox or Google Chrome");
             return;
        }
        var $formdata = new FormData();
        $formdata.append("upload_avatar", "yes");
        $formdata.append("file", $file); 

        $.ajax({
            type: "POST",
            url: "/ajax/ajax.upload.php",
            data: $formdata,
            contentType: false,
            processData: false,
            success: function(e) {
                Global.showNotificationMessage(e);

                if (e.indexOf("successfully") != -1) {
                    Account.queryAvatar();
                }
            },
            error: function(e) {
                 Global.showNotificationMessage("Opps! error connecting to server");
            }
        });
    },

    queryAvatar: function() {
       var $url = "query_avatar=yes";
       $.get("/ajax/ajax.query.php", $url, function(e) {
           if(e == 0) return;
           
           $(".table .avatar-box img.avatar").attr("src", "/uploads/avatars/" + e );
       });
    },

    saveAccountSettings: function() {
        Account.authenticate(function(e) {
            if (!e) {
                Global.showNotificationMessage("Access denied! Authentication failed.");
                return;
            }

            $(".settings-form .save-settings").fadeOut("slow");

            var $data = $(".settings-form").serialize();
            $.post("/ajax/ajax.save.php", $data, function(e) {
                Global.showNotificationMessage(e);
                $(".settings-form .save-settings").fadeIn("slow");
            });
        });
    },

	changePassword: function() {
		var $cpass = $(".password-form input[name=current-pass]").val();
		var $pass1 = $(".password-form input[name=pass1]").val();
		var $pass2 = $(".password-form input[name=pass2]").val();


        if ($cpass.length < 8) {
        	Global.showNotificationMessage("Invalid current password");
			return;
        } else if ($pass1.length < 8) {
			Global.showNotificationMessage("New password must be at least 8 characters");
			return;
		} else if ($pass1 !== $pass2) {
			Global.showNotificationMessage("The two passwords donnot match");
			return;
		}
        $(".password-form .change-pass-btn").fadeOut("slow");

        Account.authenticate(function(e) {
            if (!e) {
                Global.showNotificationMessage("Access denied! Authentication failed.");
                return;
            }

    		var $url = "change_password=yes&cpass=" + $cpass + "&pass1=" + $pass1 + "&pass2=" + $pass2;
    		$.post("/ajax/ajax.session.php", $url, function(e) {
    			Global.showNotificationMessage(e);
    			if(e.indexOf("successfully") != -1) {
    				$(".password-form input[name=current-pass]").val("")
    				$(".password-form input[name=pass1]").val("");
    				$(".password-form input[name=pass2]").val("");
    			}
                $(".password-form .change-pass-btn").fadeIn("slow");
    		});
        });
	},

	uploadContent: function() {
		var $file_name, $exts, $ext, $formdata, $cat, $subject, $desc;

        $cat       = $(".upload-form select[name=category] option:selected").attr("value");
		$file_name = $(".upload-form input[name=file]").val();
		$file      = $(".upload-form input[name=file]")[0].files[0];
		$subject   = $(".upload-form input[name=subject]").val();
		$desc      = $(".upload-form textarea[name=description]").val();

        if($cat === undefined) {
			Global.showNotificationMessage("Please select upload category");
			return;
		}

	
		if($file_name === "") {
			Global.showNotificationMessage("Please select file to upload");
			return;
		}

		if($subject.length < 5) {
			Global.showNotificationMessage("Please enter a valid subject for the content");
			return;
		}

		if($desc.length < 5) {
			Global.showNotificationMessage("Please enter a valid description for content");
			return;
		}
      
        if (!window.FormData) {
        	Global.showNotificationMessage("Your browser does not support FormData()<br> We recommend a recent version of Mozilla Firefox or Google Chrome");
        }

        $formdata = new FormData();
        $formdata.append("category", $cat);
        $formdata.append("subject", $subject);
        $formdata.append("description", $desc);
        $formdata.append("file", $file);
        $formdata.append("submit_upload", "yes");
        

        Account.authenticate(function(e) {
            if (!e) {
                Global.showNotificationMessage("Access denied! Authentication failed.");
                return;
            }
            $(".upload-form .upload-btn").fadeOut("slow");

            $.ajax({
            	type: "POST",
            	url: "/ajax/ajax.upload.php",
            	data: $formdata,
            	contentType: false,
            	processData: false,
            	success: function(e) {
                    Global.showNotificationMessage(e);
                    if (e.indexOf("successfully") != -1) {
                        $(".upload-form select[name=category] option:selected").attr("selected", false); //reset select
                        $(".upload-form input[name=file]").val("");
                        $(".upload-form input[name=subject]").val("");
                        $(".upload-form textarea[name=description]").val("");
                    }
                    $(".upload-form .upload-btn").fadeIn("slow");
            	},
            	error: function(e) {
                     Global.showNotificationMessage("Opps! error connecting to server");
                     $(".upload-form .upload-btn").fadeIn("slow");
            	}
            });
        });
	}
}