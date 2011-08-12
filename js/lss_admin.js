var user_list;
var shown_stream = 10;
function lss_delete_stream(id)
{
	jQuery.post(ajaxurl, {action: 'lss_delete_stream', stream_id: id}, function(response){
		jQuery('.lss_response').html(response);
	});
}
function lss_add_stream()
{
	jQuery(".insert_loading").toggle();
	var channel_name = jQuery(".channel_name").val();
	var stream_name = jQuery(".stream_name").val();
	var stream_uid = jQuery("#stream_uid").val();
	var stream_site = jQuery("#stream_site").val();
	var description = jQuery("#description").val();
	//var featured = jQuery("#featured").val();
	//alert(description);
	jQuery.post(ajaxurl,{ 
			action: 'lss_add_stream',
			channel_name: channel_name,
			stream_name: stream_name,
			stream_uid: stream_uid,
			stream_site: stream_site,
			description: description,
			featured: 0},
			function(response) {
				var stuff = response.split('|');
				if (stuff[0] == 1)
					{
						jQuery(".insert_loading").toggle();
						jQuery(".channel_name").val('');
						jQuery(".stream_name").val('');
						jQuery("#stream_uid").val('');
						jQuery("#stream_site").val('');
						jQuery("#description").val('');
						jQuery(".lss_response").html(stuff[1]);
						jQuery("#lss_edit_table").append(stuff[2]);
					}
				else
					{
						jQuery('.lss_response').html(stuff[1]);
						jQuery('.lss_insert_error').html(stuff[2]);
						jQuery('.lss_show_error').toggle();
						jQuery(".insert_loading").toggle();
					}
	});
}
function lss_show_details(tag)
{
	var tag_div = "div." + tag;
	var tag_a = "a#" + tag;
	jQuery(tag_div).toggle("slow");
	jQuery(tag_a).toggle();
}
function lss_edit_streams(tag_id)
{
	var id_tag = '#sted_id-' + tag_id;
	var tag_channel_name = '#sted_channel_name-' + tag_id;
	var tag_stream_name = '#sted_stream_name-' + tag_id;
	var tag_stream_uid = '#sted_stream_owner-' + tag_id;
	var tag_stream_site = '#sted_stream_site-' + tag_id;
	var tag_description = '#sted_stream_desc-' + tag_id;
	var id = jQuery(id_tag).val();
	var channel_name = jQuery(tag_channel_name).val();
	var stream_name = jQuery(tag_stream_name).val();
	var stream_uid = jQuery(tag_stream_uid).val();
	var stream_site = jQuery(tag_stream_site).val();
	var description = jQuery(tag_description).val();
	jQuery.post(ajaxurl,{ 
		action: 'lss_edit_streams',
		stream_id: id,
		channel_name: channel_name,
		stream_name: stream_name,
		stream_uid: stream_uid,
		stream_site: stream_site,
		description: description,
		featured: 0},
		function(response) {
			jQuery(".lss_response").html(response);
		});	
}
function lss_edit_streams_table()
{
	jQuery.post(ajaxurl, {action: 'lss_edit_streams_table', start_id: shown_stream},
	function(response){
		document.getElementById("lss_edit_table").innerHTML = response;
	});
	shown_stream = shown_stream + 10;
}
function lss_delete_stream(id)
{
	var remove_tag = '#sted_tr_id-' + id;
	var stream_name = '#sted_stream_name-' + id;
	var delete_stream = confirm("Are you sure you want to delete " + jQuery(stream_name).val() + "?");
	 if (delete_stream == true)
	 {
		 jQuery.post(ajaxurl, {action: 'lss_delete_stream', stream_id: id}, function(response){
				jQuery(".lss_response").html(response);
				jQuery(remove_tag).remove();
			});
	 }
	 else
	 {
	 }
	
}
function make_json()
{
	jQuery.post(ajaxurl, {action: 'lss_make_json'}, function(response){
		jQuery('.lss_response').html(response);
	});
}
jQuery(function() {
	function log( message ) {
		jQuery( ".stream_uid" ).attr( "scrollTop", 0 );
	}
jQuery( ".stream_uid" ).autocomplete({
	source: function( request, response ) {
		jQuery.ajax({
			url: ajaxurl,
			dataType: "json",
			data: {
				action: 'lss_user_list',
				user_name: request.term
			},
			success: function( data ) {
				response( jQuery.map( data.user, function( item ) {
					return {
						label: item.name,
						value: item.name
					}
				}));
			}
		});
	},
	minLength: 2,
	select: function( event, ui ) {
		log( ui.item );
	},
	open: function() {
		jQuery( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
	},
	close: function() {
		jQuery( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
	}
});
});