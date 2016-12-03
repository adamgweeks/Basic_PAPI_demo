$("document").ready(function() {

if ('undefined' == typeof window.jQuery) {
    var script = document.createElement("SCRIPT");
    script.src = '/js/jquery-1.12.4.min.js';
    script.type = 'text/javascript';
    document.getElementsByTagName("head")[0].appendChild(script);

    // Poll for jQuery to come into existance
    var checkReady = function(callback) {
        if (window.jQuery) {
            callback(jQuery);
        }
        else {
            window.setTimeout(function() { checkReady(callback); }, 100);
        }
        }
    };
    
    $("#detail").contents().find("body").html("Detailed info will appear here when you click 'View Detail' in menu above.");

$("#button").click(function() {  
     $.ajax({
        type: "POST",
        dataType: "json",
        url: "run_command.php?cluster=" + $("#cluster").val() + "&user=" + $("#user").val() + "&password=" + $("#password").val() + "&type=" + $("#cmd_type").val(),
        success: function(t) {
                //console.log('type:' + $("#cmd_type").val());
            $("#commands").html("<tr><th>Description</th><th>Command</th><th>Result</th><th>Raw</th></tr>"), $.each(t, function(t, a) {
                var e = "<tr><td>" + a.desc + "</td><td>" + a.command + "</td><td align='center'>" + a.out + "</td><td><a href='./detail.php?command=" + a.fullcmd + "&user=" + $("#user").val() + "&password=" + $("#password").val() + "' target='detail'>View detail</a></td></tr>";
                $("#commands").append(e)
            })
            },
           error: function(xhr, ajaxOptions, thrownError) {
            alert(xhr.status + " "+ thrownError);
        }
    }), !1
});

});