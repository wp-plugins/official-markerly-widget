<?php
/*
Plugin Name: Markerly Wordpress Plugin
Plugin URI: http://www.markerly.com
Description: A plugin to give you all the options of the Markerly sharing/analytics suite

Features
--------
* All of the basic Markerly options configurable via Wordpress settings
* Simple, asynchronous code loaded alongside each post/page
* For more options/settings, please visit http://markerly.com/docs

Configuring / Installing
------------------------
* Please folder inside of /wp-content/plugins/
* All settings live under SETTINGS > Markerly Settings
* Publisher ID required to receive analytics

Version: 1.1.0
Author: Justin Kline
Author URI: justin@markerly.com
*/


add_action('admin_init', 'markerlyoptions_init' );
add_action('admin_menu', 'markerlyoptions_add_page');
add_action('wp_head', 'markerly_script');

// Init plugin options to white list our options
function markerlyoptions_init(){
	register_setting( 'markerlyoptions_options', 'markerly', 'markerlyoptions_validate' );
	register_setting('markerlyoptions_options', 'pub_id');
}

// Add menu page
function markerlyoptions_add_page() {
	add_options_page('Markerly Options', 'Markerly Options', 'manage_options', 'markerlyoptions', 'markerlyoptions_do_page');
}

function markerly_script() {
    $options = get_option('markerly');
    if ($options["text_sharing"] == 1) unset($options["text_sharing"]); else $options["text_sharing"] = 0;
    if ($options["image_sharing"] == 1) unset($options["image_sharing"]); else $options["image_sharing"] = 0;
    $pub_id = ($options["pub_id"] == "") ? "WP_ANONYMOUS" : $options["pub_id"];
        
    
    $out_options = '<script type="text/javascript">var markerly_settings = '.json_encode($options).';</script>';
    $out = "";
    
    $out = $out_options.'<script type="text/javascript" src="http://www.markerly.com/toolbar/markerly-pub.js#pub_id='.$pub_id.'"></script>';
    
    echo $out;
}

// Draw the menu page itself
function markerlyoptions_do_page() {
	?>
	<script type="text/javascript" src="http://www.markerly.com/assets/js/lib/colorpicker/jquery.miniColors.min.js"></script>
	<link rel="stylesheet" type="text/css" href="http://www.markerly.com/assets/js/lib/colorpicker/jquery.miniColors.css" />
    <style type="text/css">
    #done-btn {
        background: none repeat scroll 0 0 yellow;
        border: 1px solid lightgrey;
        border-radius: 3px 3px 3px 3px;
        bottom: 0;
        color: #444444;
        cursor: pointer;
        display: none;
        font-weight: bold;
        height: 0;
        left: 17px;
        padding: 5px 30px 24px;
        position: absolute;
        z-index: 12;
    }
    </style>
	
	<div class="wrap">
		<h2>Markerly Options</h2>
		<form method="post" action="options.php">
			<?php settings_fields('markerlyoptions_options'); ?>
			<?php $options = get_option('markerly'); 
			    
			    if ($options['text_sharing'] == "") $options['text_sharing'] = 1;
			    if ($options['image_sharing'] == "") $options['image_sharing'] = 1;
			    if ($options['tip_style'] == "") $options['tip_style'] = "light";
			    if ($options['services'] == "") $options['services'] = "facebook,twitter,email";
			    if ($options['image_services'] == "") $options['services'] = "facebook,twitter,pinterest,email";
			    if ($options['service_color'] == "") $options['service_color'] = "000000";
			    
			    
			?>
			
			
			<script type="text/javascript">
			if (typeof $ == "undefined") $ = jQuery;
			$(function() {
			    $('#colorselector').miniColors({
					close: function(hex, rgb) {
					    $('input[name="markerly[service_color]"]').val(hex.replace('#', ''));
					    $('.sharing-options').css('background-color', hex);
					    $('.color-code').text(hex.toUpperCase());
						$('#done-btn').hide();
					},
					open: function() {
						// mixpanel.track('color-picker', mixpanel_array);
					}
				});
				
				$('.color-code').text('#<?php echo $options['service_color']; ?>');
			});	
			</script>
			
			<table class="form-table">
				<tr valign="top"><th scope="row">Publisher ID</th>
					<td>
					    <input type="text" name="markerly[pub_id]" value="<?php echo $options['pub_id']; ?>" />
					    <p>No publisher ID? You'll need one for analytics. <a href="http://www.markerly.com/social#gr8" target="_blank">Get it here</a></p>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Text Sharing</th>
					<td>
					    <input name="markerly[text_sharing]" type="radio" value="1" <?php checked(1, $options['text_sharing']); ?> /> Yes<br />
					    <input name="markerly[text_sharing]" type="radio" value="0" <?php checked(0, $options['text_sharing']); ?> /> No
					</td>
				</tr>
				<tr valign="top"><th scope="row">Image Sharing</th>
					<td>
					    <input name="markerly[image_sharing]" type="radio" value="1" <?php checked(1, $options['image_sharing']); ?> /> Yes<br />
					    <input name="markerly[image_sharing]" type="radio" value="0" <?php checked(0, $options['image_sharing']); ?> /> No
					</td>
				</tr>
				<tr valign="top"><th scope="row">Text Sharing Services</th>
					<td>
					    <input name="markerly[services]" type="text" value="<?php echo $options['services']; ?>" size="50" />
					    <br />
					    <em><strong>Available options:</strong> facebook,twitter,email,linkedin,evernote</em>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Image Sharing Services</th>
					<td>
					    <input name="markerly[image_services]" type="text" value="<?php echo $options['image_services']; ?>" size="50" />
					    <br />
					    <em><strong>Available options:</strong> facebook,twitter,email,pinterest,comments</em>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Tip Style</th>
					<td>
					    <input name="markerly[tip_style]" type="radio" value="light" <?php checked("light", $options['tip_style']); ?> /> Light <br />
					    <input name="markerly[tip_style]" type="radio" value="black" <?php checked("black", $options['tip_style']); ?> /> Dark<br />
					    <input name="markerly[tip_style]" type="radio" value="transparent" <?php checked("transparent", $options['tip_style']); ?> /> Transparent
					</td>
				</tr>
				<tr valign="top"><th scope="row">Service Icon Color</th>
					<td>
					    <div id="interface-options-wrap" style="position: relative">
						    <input type="hidden" id="colorselector" value="<?php echo $options['service_color']; ?>" class="color-picker black" />
						    <input name="markerly[service_color]" type="hidden" value="<?php echo $options['service_color']; ?>" />
						    <span class="color-code"></span>
							<div id="done-btn">Done</div>
						</div>
					    
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function markerlyoptions_validate($input) {
	// Our first value is either 0 or 1
	// $input['text_sharing'] = ($input['text_sharing'] == "1") ? "0" : "1";
	
	// Say our second option must be safe text with no HTML tags
	// $input['sometext'] =  wp_filter_nohtml_kses($input['sometext']);
	
	return $input;
}

