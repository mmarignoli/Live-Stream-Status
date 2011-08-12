<?php
/*
 Plugin Name: Live Stream Status
 Plugin URI: http://code.google.com/p/livestreamstatus/
 Description: Check live streams and displays them on the website
 Author: Marco Marignoli
 Version: 0.1
 Author URI: http://www.heatgaming.com/
 */ 


global $wp_version;

if (!version_compare($wp_version, "3.2", ">="))
{
	die("you need at least version 3.2 to use this plugin");
}
function single_table($stream_id)
{
	global $wpdb;
	$table = $wpdb->prefix . 'lss_streams';
	$streams = $wpdb->get_results($wpdb->prepare("SELECT * from ".$table." WHERE stream_id ='".$stream_id."';"), ARRAY_N);
	$response;
	$response .= '<tr id="sted_tr_id-'.$streams[0][0].'">';
		$response .= '	<td><input name="id" id="sted_id-'.$streams[0][0].'" type="hidden" value="'.$streams[0][0].'"/><input type="text" name="test" id="sted_channel_name-'.$streams[0][0].'" value="'.$streams[0][1].'"/></td>';
		$response .= '   <td><input type="text" name="test" id="sted_stream_name-'.$streams[0][0].'" value="'.$streams[0][2].'"/></td>';
		$response .= '   <td><input type="text" name="test" id="sted_stream_owner-'.$streams[0][0].'" class="stream_uid" onclick="javascript:lss_users_show" value="'.lss_get_user($streams[0][3]).'"/></td>';
		switch ($streams[0][4]) {
		case 0:
			$response .= '<td>
			<select name="site" id="sted_stream_site-'.$streams[0][0].'">
			  <option value="0" selected>justin.tv</option>
			  <option value="1">own3d.tv</option>
			  <option value="2">livestream.tv</option>
			  <option value="3">ustream.tv</option>
			</select></td>';
			break;
		case 1:
			$response .= '<td>
			<select name="site" id="sted_stream_site-'.$streams[0][0].'">
			  <option value="0">justin.tv</option>
			  <option value="1" selected>own3d.tv</option>
			  <option value="2">livestream.tv</option>
			  <option value="3">ustream.tv</option>
			</select></td>';
			break;
		case 2:
			$response .= '<td>
			<select name="site" id="sted_stream_site-'.$streams[0][0].'">
			  <option value="0">justin.tv</option>
			  <option value="1">own3d.tv</option>
			  <option value="2" selected>livestream.tv</option>
			  <option value="3">ustream.tv</option>
			</select></td>';
			break;
		case 3:
			$response .= '<td>
			<select name="site" id="sted_stream_site-'.$streams[0][0].'">
			  <option value="0">justin.tv</option>
			  <option value="1">own3d.tv</option>
			  <option value="2">livestream.tv</option>
			  <option value="3" selected>ustream.tv</option>
			</select></td>';
			break;
		default:
			break;
		}
		
		
		$response .= '   <td><input type="text" name="test" id="test" /></td>';
		$response .= '   <td><input type="text" name="test" id="sted_stream_desc-'.$streams[0][0].'" value="'.htmlspecialchars(stripslashes($streams[0][6])).'"/></td>
			    <td><input type="text" name="test" id="test" /></td>
			    <td><button type="button" style="border-style:solid; border-width:1px; border-color:#000000" onclick="javascript:lss_edit_streams('.$streams[0][0].')">Edit</button></td>
		    	<td><button type="button" style="border-style:solid; border-color:#FF0000; border-width:1px;" onclick="javascript:lss_delete_stream('.$streams[0][0].')">Delete</button></td>';
		$response .=  '</tr>';
		return $response;
	
}
function lss_activate()
{
	//add db create code here!!
	global $wpdb;
	
	$table_name = $wpdb->prefix . "lss_streams";
	
	if ( $wpdb->get_var('SHOW TABLES LIKE' . $table_name)!= $table_name)
	{
		$sql = 'CREATE TABLE IF NOT EXISTS '.$table_name.' (
			  `stream_id` int(11) NOT NULL AUTO_INCREMENT,
			  `channel_name` text NOT NULL,
			  `stream_name` text NOT NULL,
			  `stream_user_id` int(11) NOT NULL,
			  `stream_site` int(11) NOT NULL,
			  `stream_type` int(11) DEFAULT NULL,
			  `description` longtext NOT NULL,
			  `embed_code` longtext,
			  `stream_score` float DEFAULT NULL,
			  `stream_vote` int(11) DEFAULT NULL,
			  `featured` int(11) NOT NULL,
			  `page_link` text,
			  `page_id` int(11) DEFAULT NULL,
			  PRIMARY KEY (`stream_id`)
			)';
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		add_option('lss_dbversion' , '0.1');
	}
}

register_activation_hook(__FILE__, "lss_activate");

function lss_admin()
{
	?>
	<div class="wrap">
	<div class="lss_response"></div>
	<a href="javascript:lss_show_details('lss_insert_error')" id="lss_insert_error" class="lss_show_error" style="display:none">Show Details</a><a href="javascript:lss_show_details('lss_insert_error')" id="lss_insert_error" style="display:none">Hide Details</a>
	<div class="lss_insert_error" style="display:none"></div>
	<div class="lss_edit_streams">
		<table border="1">
		  <tr>
		    <td>Channel Name</td>
		    <td>Stream Name</td>
		    <td>Stream Owner</td>
		    <td>Streaming Site</td>
		    <td>Stream Game</td>
		    <td>Description</td>
		    <td>Rating</td>
		    <td>Edit</td>
		    <td style="color:#FF0000">Delete</td>
		  </tr>
		 <div id="lss_edit_table">
			<?php lss_edit_streams_table_callback()?>
		 </div>
		</table>
	<div id="lss_more"><a href="javascript:lss_edit_streams_table()">Show More</a></div>
	</div>
	<div class="insert_loading" style="position:absolute; top:0px; left:0px; width:100%; height:100%; background:#CCC; opacity:0.3; display:none"><img src="wp-content/plugins/live-stream-status/ajax-loader.gif" width="66" height="66" style="position:absolute; top:50%; left:50%; margin-top:-33px; margin-left:-33px;"/></div>
	<h2>Add Stream:</h2>
	<table width="500" border="1">
	  <tr>
	    <td><label for="chanel_name2">Channel Name</label></td>
	    <td><input type="text" name="channel_name" class="channel_name" /></td>
	  </tr>
	  <tr>
	    <td><label for="stream_name">Stream Name</label></td>
	    <td><input type="text" name="stream_name" class="stream_name" /></td>
	  </tr>
	  <tr>
	    <td><label for="stream_uid">Stream Owner</label></td>
	    <td><input type="text" name="stream_uid" id="stream_uid" class="stream_uid" /></td>
	  </tr>
	  <tr>
	    <td><label for="stream_site">Streaming Site</label></td>
	    <td><select name="stream_site" id="stream_site">
	      <option value="0">Justin.tv</option>
	      <option value="1">own3d.tv</option>
	      <option value="2">livestream.tv</option>
	      <option value="3">ustream.tv</option>
	    </select></td>
	  </tr>
	  <tr>
	    <td><label for="description">Description</label></td>
	    <td><textarea name="description" id="description" cols="45" rows="5"></textarea></td>
	  </tr>
	  <tr>
	    <td><label for="featured">Featured</label></td>
	    <td><input type="checkbox" name="featured" id="featured" /></td>
	  </tr>
	  <tr>
	    <td>Submit </td>
	    <td><button type="button" style="border-style:solid; border-width:1px; border-color:#000000"" onclick="javascript:lss_add_stream()">Add Stream</button></td>
	  </tr>
	</table>
	</div>
	<?php
}

function lss_plugin_menu()
{
	add_options_page('Live streams status', 'Live streams status', 'manage_options', 'lss-plugin','lss_admin');
}

add_action('admin_menu', 'lss_plugin_menu');

add_action('wp_ajax_lss_add_stream', 'lss_add_stream_callback');

function lss_add_stream_callback() 
{
	global $wpdb; // this is how you get access to the database
	$table = $wpdb->prefix . 'lss_streams';
	$user_table = $wpdb->prefix . 'users';
	$user_id = $wpdb->get_results("SELECT `ID` FROM `".$user_table."` WHERE `user_login` = '".$_POST['stream_uid']."'", ARRAY_N);
	if ($user_id[0][0] == '')
	{
		echo "0|User ".$_POST['stream_uid']." does not exist";
		die();
	}
	else 
	{
		$lss_values = array(
				'channel_name'=> $_POST['channel_name'],
				'stream_name' => $_POST['stream_name'],
				'stream_user_id' => $user_id[0][0],
				'stream_site' =>$_POST['stream_site'],
				'description' =>$_POST['description'],
				'featured' => $_POST['featured']
		);
		$wpdb->insert( $table, $lss_values);
		
		
		if ($wpdb->insert_id)
		{
			$last_stream = $wpdb->insert_id;
			$new_page = array(    
			     'post_title' => 'Stream - ' . $_POST['stream_name'],
			     'post_content' => '[stream stream_id="'.$wpdb->insert_id.'"]',
			     'post_status' => 'publish',
			     'post_author' => $user_id[0][0],
				 'post_category' => array(0),
				 'post_type' => 'page'
			  );
			
			wp_insert_post( $new_page );
			
			$table_post = $wpdb->prefix . 'posts';
			
			$lss_stream_url = $wpdb->get_results("SELECT * FROM  ".$table_post." WHERE  `post_content` LIKE  '%stream stream_id%".$last_stream."%'", ARRAY_N);
			$lss_values = array(
				'page_link' => $lss_stream_url[0][18],
				'page_id' => $lss_stream_url[0][0]
			);
			$wpdb->update( $table, $lss_values, array( 'stream_id' => $last_stream ));
			$response = '1|The stream '.$_POST['stream_name'].' has been added to the databse <button type="button" onclick="javascript:lss_delete_stream('.$last_stream.')">Undo</button>|'.single_table($last_stream).'';
			echo $response;
		}
		else
		{
			$response = '0|There was an error nothing has been added to the database|'.$wpdb->last_error.'<br>'.$wpdb->last_query.'';
			echo $response;
		}
	}
	die();
}

add_action('wp_ajax_lss_delete_stream', 'lss_delete_stream_callback');

function lss_delete_stream_callback()
{
	global $wpdb;
	$table = $wpdb->prefix . 'lss_streams';
	$stream_name = $wpdb->get_results("SELECT stream_name FROM ".$table." WHERE stream_id = ".$_POST['stream_id'].";", ARRAY_N);
	if ($wpdb->query("DELETE FROM ".$table." WHERE stream_id = ".$_POST['stream_id']."") == 0)
	{
		echo "There was an error the stream wasnt deleted|".$wpdb->last_error."";
	}
	else 
	{
		echo $stream_name[0][0] . " was deleted successfully";
	}
	die();
}

add_action('wp_ajax_lss_user_list', 'lss_user_list_callback');

function lss_user_list_callback()
{
	global $wpdb;
	$table = $wpdb->prefix . 'users';
	$user_list = $wpdb->get_results("SELECT user_login FROM ".$table." WHERE user_login LIKE '%".$_REQUEST['user_name']."%'", ARRAY_N);
	$response_string = '{"user": [';
	$comma = '';
	for ($i = 0; $i <sizeof($user_list); $i++)
	{
		$response_string .= $comma .'{"name":"'. $user_list[$i][0] . '"}';
		$comma = ',';
	}
	$response_string .= ']}';
	echo $response_string;
	die();
}
add_action('wp_ajax_lss_edit_streams', 'lss_edit_streams_callback');

function lss_edit_streams_callback()
{
	global $wpdb;
	$table = $wpdb->prefix . 'lss_streams';
	$user_table = $wpdb->prefix . 'users';
	$user_id = $wpdb->get_results("SELECT `ID` FROM `".$user_table."` WHERE `user_login` = '".$_POST['stream_uid']."'", ARRAY_N);
	if ($user_id[0][0] == '')
	{
		echo "0|User ".$_POST['stream_uid']." does not exist";
		die();
	}
	$lss_values = array(
			'channel_name'=> $_POST['channel_name'],
			'stream_name' => $_POST['stream_name'],
			'stream_user_id' => $user_id[0][0],
			'stream_site' =>$_POST['stream_site'],
			'description' =>$_POST['description'],
			'featured' => $_POST['featured']
	);
	$wpdb->update ($table, $lss_values, array( 'stream_id' => $_POST['stream_id']));
	echo 'stream Edited';
	die();
}

add_action('wp_ajax_lss_edit_streams_table', 'lss_edit_streams_table_callback');
function lss_get_user($id)
{
	global $wpdb;
	$table = $wpdb->prefix . 'users';
	$user = $wpdb->get_results("SELECT user_login FROM ".$table." WHERE ID = ".$id.";", ARRAY_N);
	return $user[0][0];
}

function lss_edit_streams_table_callback()
{
	global $wpdb;
	$table = $wpdb->prefix . 'lss_streams';
	$start_lss = (isset($_REQUEST['start_id']))? $_REQUEST['start_id'] : 0;
	$end_lss = $start_lss + 10;
	$streams = $wpdb->get_results($wpdb->prepare("SELECT * from ".$table." LIMIT ".$start_lss.",".$end_lss.";"), ARRAY_N);
	//Table Header
	for ($i = 0; $i < sizeof($streams); $i++)
	{
		echo'<tr id="sted_tr_id-'.$streams[$i][0].'">';
		echo'	<td><input name="id" id="sted_id-'.$streams[$i][0].'" type="hidden" value="'.$streams[$i][0].'"/><input type="text" name="test" id="sted_channel_name-'.$streams[$i][0].'" value="'.$streams[$i][1].'"/></td>';
		echo'   <td><input type="text" name="test" id="sted_stream_name-'.$streams[$i][0].'" value="'.$streams[$i][2].'"/></td>';
		echo'   <td><input type="text" name="test" id="sted_stream_owner-'.$streams[$i][0].'" class="stream_uid" onclick="javascript:lss_users_show" value="'.lss_get_user($streams[$i][3]).'"/></td>';
		switch ($streams[$i][4]) {
		case 0:
			echo'<td>
			<select name="site" id="sted_stream_site-'.$streams[$i][0].'">
			  <option value="0" selected>justin.tv</option>
			  <option value="1">own3d.tv</option>
			  <option value="2">livestream.tv</option>
			  <option value="3">ustream.tv</option>
			</select></td>';
			break;
		case 1:
			echo'<td>
			<select name="site" id="sted_stream_site-'.$streams[$i][0].'">
			  <option value="0">justin.tv</option>
			  <option value="1" selected>own3d.tv</option>
			  <option value="2">livestream.tv</option>
			  <option value="3">ustream.tv</option>
			</select></td>';
			break;
		case 2:
			echo'<td>
			<select name="site" id="sted_stream_site-'.$streams[$i][0].'">
			  <option value="0">justin.tv</option>
			  <option value="1">own3d.tv</option>
			  <option value="2" selected>livestream.tv</option>
			  <option value="3">ustream.tv</option>
			</select></td>';
			break;
		case 3:
			echo'<td>
			<select name="site" id="sted_stream_site-'.$streams[$i][0].'">
			  <option value="0">justin.tv</option>
			  <option value="1">own3d.tv</option>
			  <option value="2">livestream.tv</option>
			  <option value="3" selected>ustream.tv</option>
			</select></td>';
			break;
		default:
			break;
		}
		
		
		echo'   <td><input type="text" name="test" id="test" /></td>';
		echo'   <td><input type="text" name="test" id="sted_stream_desc-'.$streams[$i][0].'" value="'.htmlspecialchars(stripslashes($streams[$i][6])).'"/></td>
			    <td><input type="text" name="test" id="test" /></td>
			    <td><button type="button" style="border-style:solid; border-width:1px; border-color:#000000" onclick="javascript:lss_edit_streams('.$streams[$i][0].')">Edit</button></td>
		    	<td><button type="button" style="border-style:solid; border-color:#FF0000; border-width:1px;" onclick="javascript:lss_delete_stream('.$streams[$i][0].')">Delete</button></td>';
		echo '</tr>';
	}
	if (isset ($_REQUEST['start_id']))
	{
		die();
	}
}
add_action ('wp_ajax_lss_delete_stream', 'lss_delete_stream');

function lss_delete_stream()
{
	global $wpdb;
	$table = $wpdb->prefix . 'lss_streams';
	$wpdb->query("DELETE FROM ".$table." WHERE 'stream_id' = ".$_POST['stream_id'].";");
}

function make_json()
{
	global $wpdb;
	$table = $wpdb->prefix . 'lss_streams';
	$stream_list = $wpdb->get_results("SELECT * FROM ".$table.";", ARRAY_N);
	$json_string = '{"streams":[';
	$comma = '';
	for ($i=0; $i<sizeof($stream_list); $i++)
	{
		$name = $stream_list[$i][1];
		$site = $stream_list[$i][4];
		$tag = $stream_list[$i][0];
		$stream_info = array('name' => $name, 'site' => $site, 'tag' => $tag);
		$json_string .= $comma . json_encode($stream_info);
		$comma = ',';
	}
	$json_string .= ']}';
	echo $json_string;
	$jsonFile = 'json_streams.txt';
	$fh = fopen($jsonFile, 'w') or die("can't open file");
	fwrite($fh, $json_string);
	fclose($fh);
	die();
}

add_action ('wp_ajax_lss_make_json', 'make_json');
add_action ('wp_ajax_nopriv_lss_make_json', 'make_json');

function lss_frontend()
{
	include 'icons.php';
	global $wpdb;
	$table = $wpdb->prefix . 'lss_streams';
	$streams = $wpdb->get_results("SELECT * FROM ".$table.";", ARRAY_N);
	$test1 = plugins_url('/img/live_user_hackprotech-320x240.jpg', __FILE__);
	$lss_output;
	for ($i = 0; $i < sizeof($streams); $i++)
	{
		$st_site = '';
		$st_image = '';
		switch($streams[$i][4])
		{
			case 0:
				$st_site = $lss_icons['jtv'];
				$st_image = 'http://static-cdn.justin.tv/previews/live_user_'.$streams[$i][1].'-320x240.jpg';
				break;
			case 1:
				$st_site = $lss_icons['o3d'];
				$st_image = 'http://img.hw.own3d.tv/live/live_tn_'.$streams[$i][1].'_.jpg';
				break;
			case 2:
				$st_site = $lss_icons['lst'];
				$st_image = "http://thumbnail.api.livestream.com/thumbnail?name='.$streams[$i][1].'";
				break;
			case 3:
				$st_site = $lss_icons['ust'];
				$st_image = '';
				break;
		}
		$lss_output .= '<a id="st-link" href="'.$streams[$i][11].'">
		<div id="container" class="st-'.$streams[$i][0].' offline_stream" style="position:relative; width:700px; height:90px margin-top:0px; padding-bottom:0px; background:white; display:none;" >
		<div style="position:absolute left:0px; top:0px; float:left;"><img src="'.$st_image.'" width="107" height="80" /></div>
		<div style="position:absolute left:85px, top:0px; width:305px; float:left; text-align:center; line-height:100%;">'.htmlspecialchars(stripslashes($streams[$i][2])).'</div>
		<div style="float:left; position:absolute; top:20px; left: 113px; width: 300px; font-size:12px; line-height:100%;">'.htmlspecialchars(stripslashes($streams[$i][6])).'</div>
		<div style="position:absolute; top:0px; left:419px;"><img src="'.$st_site.'" width="32" height="31" /></div>
		<div style="position:absolute; top:0px; left:469px;"><img src="'.$lss_icons["sc2"].'" width="32" height="32" /></div>
		<div id="viewers-'.$streams[$i][0].'" style="position:absolute; top:35px; left:419px;">Offline</div>
		<div style="position:absolute; top:60px; left:420px"><img src="'.$lss_icons["5st"].'" width="80" height="16" /></div>
		</div></a>';
	}
	$lss_output .= '<script type="text/javascript">update_streams()</script>';
	return $lss_output;
}

add_shortcode ('streams', 'lss_frontend');

function lss_embed_stream($atts, $content=null)
{
	global $wpdb;

	$table_name = $wpdb->prefix . "lss_streams";//

	$stream = $wpdb->get_results("SELECT * FROM ".$table_name." WHERE `stream_id` = ".$atts['stream_id']."", ARRAY_N);
	
	$output;

	switch ($stream[0][4])
	{
		case 0:
			$output .= '<object type="application/x-shockwave-flash" height="500" width="100%" id="live_embed_player_flash" data="http://www.justin.tv/widgets/live_embed_player.swf?channel='.$stream[0][1].'" bgcolor="#000000">
					<param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" />
					<param name="movie" value="http://www.justin.tv/widgets/live_embed_player.swf" />
					<param name="flashvars" value="hostname=www.justin.tv&channel='.$stream[0][1].'&auto_play=false&start_volume=25" /></object>';
			break;
		case 1:
			$output .='
			<iframe height="360" width="640" frameborder="0" src="http://www.own3d.tv/liveembed/'.$stream[0][1].'">';
			break;
		case 2:
			$output .='
			<iframe width="560" height="340" src="http://cdn.livestream.com/embed/'.$stream[0][1].'?layout=4&amp;autoplay=false" style="border:0;outline:0" frameborder="0" scrolling="no">
			</iframe><div style="font-size: 11px;padding-top:10px;text-align:center;width:560px">';
			break;
		case 3:
			//this is gonna need to be fixed, it's a quick fix for the time being
			$url = 'http://api.ustream.tv/php/channel/all/search/username:eq:'.$stream[0][1].'?key=A33D8E6A9BA8CC83C6E61AAAE8E12DBF';
			$contents = array();
			$contents = unserialize(file_get_contents($url));
			
			$url2 = 'http://api.ustream.tv/php/channel/'.$contents['results']['0']['id'].'/getCustomEmbedTag?key=A33D8E6A9BA8CC83C6E61AAAE8E12DBF&params=autoplay:false;mute:false;height:480;width:640';
			$contents2 = array();
			$contents2 = unserialize(file_get_contents($url2));
			
			$output .= $contents2['results'];
			break;
	}
	return $output;
}

add_shortcode('stream', 'lss_embed_stream');

function lss_scripts()
{
	$pluginfolder = get_bloginfo('url') . '/' . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));
	wp_register_script( 'jqueryui_autocomplete', plugins_url('/js/jquery-ui-1.8.14.autocomplete.js', __FILE__));
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jqueryui_autocomplete');
	wp_register_script( 'lss_scripts_admin', plugins_url('/js/lss_admin.js', __FILE__));
	wp_enqueue_script( 'lss_scripts_admin' );
	wp_register_script( 'lss_scripts_front', plugins_url('/js/lss_scripts.js', __FILE__));
	wp_enqueue_script( 'lss_scripts_front' );
	wp_enqueue_style('jquery.ui.theme.lightness', $pluginfolder . '/css/ui-lightness/jquery-ui-1.8.14.custom.css');
	wp_enqueue_style('lss_style', $pluginfolder . '/css/lss.css');
}

add_action('init', 'lss_scripts');