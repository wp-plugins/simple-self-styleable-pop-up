var preset1html = '<div id="popup">This website uses cookies to store personal information. Click here to <a href="#">learn more</a> about cookies and what they are used for.<div class="ssspu-close">Close</div></div>';
var preset1css = '#popup{position:fixed; width:100%; line-height:20px; padding:10px; background-color:#7d7d7d; color:#FFFFFF; text-align:center;} #popup a{color:#FFFFFF;} .ssspu-close{padding:5px; background-color:#000000; color:#FFFFFF; display:inline-block; margin-left:10px;}';
var preset2html = "<div id=\"popup\"><h3>Discover the future of egg-laying dinosaurs!</h3><p>You might add your own links here. Or insert a image. Or do whatever you wish.</p><!-- If you wrap your elements in the div class ssspu-close, a click on them will automatically deactivate the window --><div class=\"ssspu-close\">Close this ad!</div></div>";
var preset2css = "#popup {position: fixed; width:450px; height:250px; margin-top: -125px; top: 50%; left: 50%; margin-left: -250px; background-color: #eeeeee; border: 1px solid #cecece; font-family: Verdana, Geneva, sans-serif; font-size: 14px; color: #000; padding: 10px;} .ssspu-close{ background-color:#ff0000; color: #ffffff; padding: 10px; display: block;}";

jQuery(document).ready(function(){
	jQuery("#ssspu_preset1").click(function(){
		jQuery("#ssspu_html").val(preset1html);
		jQuery("#ssspu_css").val(preset1css);
	});
	jQuery("#ssspu_preset2").click(function(){
		jQuery("#ssspu_html").val(preset2html);
		jQuery("#ssspu_css").val(preset2css);
	});
});