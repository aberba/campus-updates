$(function() {
    $(".add-form .add-btn").on("click", function(e) {
        e.preventDefault();
        Advertisements.addNew();
    });

    $(".toggle-form-btn").on("click", function(e) {
        e.preventDefault();
        $(".add-form").fadeIn("slow");
        $(this).fadeOut("slow");
    });

    $(".add-form .cancel-btn").on("click", function(e) {
        e.preventDefault();
        $(".add-form").fadeOut("slow");
        $(".toggle-form-btn").fadeIn("slow");
    });

    $(".advertisement .publish").on("click", function(e) {
        e.preventDefault();
        var $ad_id = $(this).parent().parent().parent().attr("id").split("advertisement")[1];
        Advertisements.changeStatus($ad_id);
    });

    $(".advertisement .delete").on("click", function(e) {
        e.preventDefault();
        var $ad_id = $(this).parent().parent().parent().attr("id").split("advertisement")[1];
        Advertisements.deleteAd($ad_id);
    });
});

var Advertisements = {
	addNew: function() {
		var $placement = $(".form select[name=placement] option:selected").attr("value");
		var $file_name = $(".form input[name=file]").val();
		var $file_url  = $(".form input[name=file_url]").val();
        var $ad_url    = $(".form input[name=ad_url]").val();

		var $alt   = $(".form input[name=alt]").val(); 
        var $day   = $(".date-container select[name=day]").val();
        var $month = $(".date-container select[name=month]").val();
        var $year  = $(".date-container select[name=year]").val();

        
        if( $placement === undefined) {
             Global.showNotificationMessage("Please select placement for banner");
             return;
        }

        if ($file_url === "" && $file_name === "") {
            Global.showNotificationMessage("Please select file from local disk OR enter file source URL");
            return;
        }

         if ($file_url !== "" && $file_name !== "") {
            Global.showNotificationMessage("Please use either a local image file OR file source URL but not both");
            return;
        }

        if ($ad_url === "") {
             Global.showNotificationMessage("Please enter ad client URL.");
            return;
        }

        var $file = false;
        if($file_name !== "") {
            $file = $(".form input[name=file]")[0].files[0];
        }

        if( $alt.length <= 0 ) {
             Global.showNotificationMessage("Please enter alt text for banner");
             return;
        }
 
        if (!window.FormData) {
            Global.showNotificationMessage("Sorry, you browser does no support FormData()");
            return;
        }
        
        var $formdata = new FormData();
        $formdata.append("upload_advertisement", "yes");
        $formdata.append("placement", $placement);
        $formdata.append("alt", $alt);
        
        $formdata.append("day", $day);
        $formdata.append("month", $month);
        $formdata.append("year", $year);
        $formdata.append("file_url", $file_url);
        $formdata.append("ad_url", $ad_url);

        if ($file) {
            $formdata.append("file", $file);
        }


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
                    $(".add-form").fadeOut("slow");
                    $(".toggle-form-btn").fadeIn("slow");
                }
            },
            error: function(e) {
                Global.showNotificationMessage("Ooops!, error connecting to server");
            }
        });
	},

    changeStatus: function($ad_id) {
        if ($ad_id == null) throw "$ad_id was not set in Advertisements.addNew()";

        var $url = "changad_ad_status=yes&ad_id=" + $ad_id;
        $.post("./ajax/ajax.save.php", $url, function(e) {
            Global.showNotificationMessage(e);
            var $text = (e.indexOf("shown") != -1) ? "Unpublish" : "Publish";
            $("#advertisement" + $ad_id + " .publish").text($text);
        });
    },

    deleteAd: function($ad_id) {
        if ($ad_id == null) throw "$ad_id was not set in Advertisements.deleteAd()";

        var $url = "delete_ad=yes&ad_id=" + $ad_id;
        $.post("./ajax/ajax.delete.php", $url, function(e) {
            Global.showNotificationMessage(e);
            if (e.indexOf("successfully") != -1) {
                 $("#advertisement" + $ad_id).fadeOut("slow");
            }
        });
    }
}