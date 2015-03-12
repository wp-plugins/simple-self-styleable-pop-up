<?php
/*
Plugin Name: Simple Self Stylable Popup
Plugin URI: http://askella.de/
Description: SSSPU allows users to easily add pop-ups to their websites. Intermediate HTML and CSS knowledge is required for this plugin.
Version: 0.6
Author: Michael Nissen
Author URI: http://michaelnissen.de/
*/

add_action('wp_head', 'ssspu_popup_js');
add_action('wp_footer', 'ssspu_popup_html');
add_action('admin_menu', 'ssspu_add_menu');
add_action('wp_enqueue_scripts', 'ssspu_frontend_scripts');
add_action('admin_enqueue_scripts', 'ssspu_backend_scripts');
add_action('wp_ajax_ssspu_frontend_css_hook', 'ssspu_frontend_css_hook');
add_action('wp_ajax_nopriv_ssspu_frontend_css_hook', 'ssspu_frontend_css_hook');
add_action('wp_ajax_ssspu_backend_css_hook', 'ssspu_backend_css_hook');
add_action('wp_ajax_nopriv_ssspu_backend_css_hook', 'ssspu_backend_css_hook');

function ssspu_popup_js(){
	/* 
	 * Pop-Up will only load if plugin is activated;
	 * ssspu_run() calls ssspu_js.php
	 * ssspu_html returns code defined in backend
     */
	if(get_option('ssspu_activated') == "true"){
	?>
		<script type="text/javascript">
		var ssspu_active = "<? echo(get_option('ssspu_activated'));?>";
		var ssspu_outsideclick = "<? echo(get_option('ssspu_outsideclick'));?>";
		var ssspu_firstvisit = "<? echo(get_option('ssspu_firstvisit'));?>";
		var ssspu_fadein = "<? echo(get_option('ssspu_fadein'));?>";
		var ssspu_fadeout = "<? echo(get_option('ssspu_fadeout'));?>";
		var ssspu_delay = "<? echo(get_option('ssspu_delay'));?>";
		var cookiename = ("<? echo(get_bloginfo('wpurl'));?>".replace(/[^A-Z0-9]/ig, '') + "popup");
			
		function ssspu_run(){	
			var visited = getCookie(cookiename);
			/* set a cookie if the option is enabled and if the cookie 'visited' has not been set to true */
			if((ssspu_firstvisit == "true") && (visited != "true")){
				var date = new Date();
				date.setTime(date.getTime() + 14*24*60*60*1000);
				document.cookie = cookiename + "=true; expires=" + date.toUTCString();
			}
			
			/* display the window if the page has not been visited or the option has been deactivated*/
			if(visited != "true" || ssspu_firstvisit == "false"){
				window.setTimeout(function(){
					jQuery("#ssspu-wrapper").fadeIn(parseInt(ssspu_fadein)).css('display', 'block');
				}, ssspu_delay);
				jQuery(document).ready(function(){
					if(ssspu_outsideclick == "true"){
						jQuery("#ssspu-wrapper").click(function(event){
							event.stopPropagation();
						});
						jQuery("html").click(function(){
							jQuery("#ssspu-wrapper").hide(parseInt(ssspu_fadeout));
						});
					}
					
					jQuery(".ssspu-close, #test").click(function(){
						jQuery("#ssspu-wrapper").hide(parseInt(ssspu_fadeout));
					});
				});
			}
		}
		
		function getCookie(cookiename){
			var name = cookiename + "=";
			var cookies = document.cookie.split(';');
			for(var i = 0; i < cookies.length; i++){
				var x = cookies[i];
				while (x.charAt(0) == ' ') x = x.substring(1);
				if (x.indexOf(name) != -1) return x.substring(name.length, x.length);
			}
			return "false";
		}
		ssspu_run();
        </script>
        <?
	}
}

/*function ssspu_popup_html($content){
	if(get_option('ssspu_activated') == "true"){
		$content = '<div id="ssspu-wrapper">'.
        get_option("ssspu_html").'
      	</div>'.$content;
	}
	
	return $content;
}*/

function ssspu_popup_html(){
	if(get_option('ssspu_activated') == "true"){
		echo('<div id="ssspu-wrapper">'.get_option("ssspu_html").'</div>');
	}
}

function ssspu_frontend_scripts(){
	/* Only load scripts if popup is activated */
	if(get_option('ssspu_activated') == "true"){
		wp_enqueue_script('jquery');
		/*wp_register_script('ssspu_js', admin_url('admin-ajax.php?action=ssspu_frontend_javascript'));
		wp_enqueue_script('ssspu_js'); */
		wp_register_style('ssspu_css', admin_url('admin-ajax.php?action=ssspu_frontend_css_hook'));
		wp_enqueue_style('ssspu_css'); 
	}
}

function ssspu_frontend_css_hook(){
		/* This will load the required frontend styles through admin-post.php */ 
		header('Content-type: text/css');
		
		echo('#ssspu-wrapper{
			position:fixed; z-index: 9999999; display:none; left:0; top:0;
		}');
		echo(get_option('ssspu_css'));
		return;
		
}

function ssspu_backend_scripts(){
	if(is_admin()){
		wp_register_script('ssspu_admin_js', plugins_url('includes/ssspu_admin_js.js', __FILE__));
		wp_enqueue_script('ssspu_admin_js');
		wp_register_style('ssspu_admin_css', admin_url('admin-ajax.php?action=ssspu_backend_css_hook'));
		wp_enqueue_style('ssspu_admin_css');
	}
}

function ssspu_backend_css_hook(){
		/* This will load the required backend styles through admin-post.php */ 
		header('Content-type: text/css');
		echo('
		h1{
			padding: 0 0 5px 0;
			margin: 0;
			font-size:20px;
			font-weight:400;
		}
		
		#ssspu_admin{
			padding:10px; 
			background-color:#eaeaea; 
			margin-top:25px; 
			width:95%;
		}
		
		#ssspu_saved{
			padding: 10px 10px 10px 40px;
			margin:10px;
			background-color:#FFF;
			line-height:22px;
		}
		
		#ssspu_saved:before{
			position:absolute;
			content:url('.plugins_url("includes/yes.png", __FILE__).');
			width:22px;
			height:22px;
			margin-left:-30px;
		}');
}

function ssspu_add_menu(){
	add_options_page('Simple Self-Stylable Pop-Up', 'Simple Self-Stylable Popup', 'manage_options', __FILE__, 'ssspu_admin_menu'); 
	/* Load required database entries and initialize them */
	add_option('ssspu_activated', 'false');
	add_option('ssspu_outsideclick', 'false');
	add_option('ssspu_firstvisit', 'false');
	add_option('ssspu_fadein', '0');
	add_option('ssspu_fadeout', '0');
	add_option('ssspu_delay', '0');
	add_option('ssspu_html', 'Enter your HTML code here');
	add_option('ssspu_css', 'Enter your CSS code here');
}

function ssspu_admin_menu(){?>
    <div id="ssspu_admin">
    <h1>Simple Self-Stylable Popup</h1>
    <? /* Update the database if any options are set (if POST-variables are set) */
        if(isset($_POST['activated']) || isset($_POST['outsideclick']) || isset($_POST['firstvisit']) || isset($_POST['delay']) || isset($_POST['html']) || isset($_POST['css']) ){
            ?> <div id="ssspu_saved"> <?
            if(isset($_POST['activated'])) update_option('ssspu_activated', 'true'); else update_option('ssspu_activated', 'false');
            if(isset($_POST['outsideclick'])) update_option('ssspu_outsideclick', 'true'); else update_option('ssspu_outsideclick', 'false');
            if(isset($_POST['firstvisit'])) update_option('ssspu_firstvisit', 'true'); else update_option('ssspu_firstvisit', 'false');
            update_option('ssspu_fadein', $_POST['fadein']);
            update_option('ssspu_fadeout', $_POST['fadeout']);
            update_option('ssspu_delay', $_POST['delay']);
            update_option('ssspu_html', stripslashes($_POST['html']));
            update_option('ssspu_css', stripslashes($_POST['css']));
            echo("Your settings have been saved.");
            ?> </div> <?
        }
        ?>
        
        <!-- start of plugin admin menu; load previously stored options from database -->
        <form name="testform" method="post">
        <input name="activated" type="checkbox" <? if(get_option('ssspu_activated') == 'true') echo('checked="true"'); ?>/> Show Pop-Up<br />
        <input name="outsideclick" type="checkbox" <? if(get_option('ssspu_outsideclick') == 'true') echo('checked="true"'); ?>/> Pop-Up closes on click outside of box<br />
        <input name="firstvisit" type="checkbox" <? if(get_option('ssspu_firstvisit') == 'true') echo('checked="true"'); ?>/> Pop-Up only appears on the first visit of user (will set a cookie)<br />
        <input name="fadein" type="text" style="width:50px;" value="<? echo(get_option('ssspu_fadein')); ?>" /> Duration of the fade-in effect (in Milliseconds)<br />
        <input name="fadeout" type="text" style="width:50px;" value="<? echo(get_option('ssspu_fadeout')); ?>" /> Duration of the fade-out effect (in Milliseconds); "0" is suggested for best user experience<br />
        <input name="delay" type="text" style="width:50px;" value="<? echo(get_option('ssspu_delay')); ?>" /> Wait for the given amount of Milliseconds until Pop-Up is shown<br />
        <div style="float:left; width:48%;">
        HTML:<br />
        <textarea name="html" id="ssspu_html" type="text" style="width:100%; height:400px;"><? echo(get_option('ssspu_html'));?></textarea><br />
        </div>
        <div style="float:right; width:48%;">
        CSS:<br />
        <textarea name="css" id="ssspu_css" type="text" style="width:100%; height:400px;"><? echo(get_option('ssspu_css'));?></textarea></div>
        <div style="clear:both;"></div>
        <input type="submit" value="Save" /> Presets: <input type="button" id="ssspu_preset1" value="Cookie-Dislcaimer" /> <input id="ssspu_preset2" type="button" value="Sample Advertisment" />
        </form></div>
        <!-- end of plugin admin menu -->
        
        <?php
}
?>