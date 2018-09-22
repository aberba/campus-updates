$(function() {
     $(".upload .options .toggle").on("click", function(e) {
     	e.preventDefault();
     	var $id = $(this).parent().parent().parent().parent().attr("id").split("upload")[1];
     	UsersUploads.toggleContent($id);
     });

     $(".upload .options .delete").on("click", function(e) {
          e.preventDefault();
          var $id = $(this).parent().parent().parent().parent().attr("id").split("upload")[1];
          UsersUploads.removeUpload($id);
     });
});


var UsersUploads = {
	toggleContent: function($upload_id) {
		$("#upload" + $upload_id + " .content").slideToggle();
	},

     removeUpload: function($upload_id) {
          var $url = "remove_user_upload=yes&upload_id=" + $upload_id;
          $.post("./ajax/ajax.delete.php", $url, function(e) {
              Global.showNotificationMessage(e);

              if(e.indexOf("successfully") != -1) {
                  $("#upload" + $upload_id).fadeOut("slow");
              }
          });
     }
}