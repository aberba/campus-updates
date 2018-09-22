$(function() {
    setTimeout(function() {
       var $event_id = $("form input[name=event_id]").val();
       Event.addReader($event_id);
    }, 10000);
});


var Event = {
   addReader: function($event_id) {
        if($event_id === undefined) throw new Error("post_id is was not set in Post.addReader()");
        var $url = "add_reader=yes&type=event&id=" + $event_id;
        $.post("/ajax/ajax.save.php", $url, function(e) {
            //do nothing
        });
    }
}