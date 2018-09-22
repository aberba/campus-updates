$(function() {
    $(".log .delete").on("click", function(e) {
    	e.preventDefault();
    	var $log_id = $(this).attr("id");
    	Logs.delete($log_id);
    });
});

var Logs = {
	delete: function($log_id) {
		if($log_id == null) throw "$log_id was not set in Logs.delete()";

		$url = "delete_log=yes&log_id=" + $log_id;
		$.post("./ajax/ajax.delete.php", $url, function(e) {
             Global.showNotificationMessage(e);

             if(e.indexOf("successfully") != -1) {
             	 $("#log" + $log_id).fadeOut("slow");
             }
		});
	}
}