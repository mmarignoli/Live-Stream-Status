var show_offline = 0;
var json_stuff;
var lss_t;
function update_streams()
{
	jQuery.ajax({
	async: true,
   	url: "",
   	contentType: "application/json; charset=utf-8",
   	dataType: "json",
	cache: false,
	success: function(data){
   	json_stuff = data;
   	table_update();
   	},
   	error: function(XHR,textStatus,errorThrown) {
   	alert("XHR="+XHR.responseText+"\nStatus="+textStatus+"\nerror="+errorThrown);
   	}
   	});
   	lss_t=setTimeout("update_streams()",180000);
   	console.log("it ran");
}

function table_update()
{
	var i = 0;
	for (i=0; i<json_stuff.streams.length;i++)
	{
		if (show_offline == 0)
		{
			if(json_stuff.streams[i].live == 1)
			{
				jQuery(".st-" + json_stuff.streams[i].name).show("slow");
				jQuery("#viewers-" + json_stuff.streams[i].name).html("Viewers: " + json_stuff.streams[i].viewer_count);
			}
			else
			{
				jQuery(".st-" + json_stuff.streams[i].name).hide("slow");
				jQuery("#viewers-" + json_stuff.streams[i].name).html("Offline");
			}
		}
		else
		{
			if(json_stuff.streams[i].live == 1)
			{
				jQuery(".st-" + json_stuff.streams[i].name).show("slow");
				jQuery("#viewers-" + json_stuff.streams[i].name).html("Viewers: " + json_stuff.streams[i].viewer_count);
			}
			else
			{
				jQuery(".st-" + json_stuff.streams[i].name).show("slow");
				jQuery("#viewers-" + json_stuff.streams[i].name).html("Offline");
			}
		}
	}
}
