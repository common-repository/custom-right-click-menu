<?php
/*
Plugin Name:Custom right click menu
Plugin URI: http://profiles.wordpress.org/ezhil/
Description: Custom right click menu plugin enables you to create a contect menu instead of blocking right click for copyright and other issues.
Author: ezhil
Version: 1.1.1
Author URI: http://profiles.wordpress.org/ezhil/
License: GPLv2 or later
*/
$site_url = get_option('siteurl');
   $temp_url = get_template_directory_uri();
   define('SITE_URL', $site_url );
   define('TEMP_URL', $temp_url );
   $site_name = parse_url(SITE_URL, PHP_URL_HOST);
   define('SITE_NAME', $site_name );
function tock_arr($val)
{
	$dataPoints = array();  
    $dataPoint = array();  
	for($i=1;$i<=$val;$i++)
	{
	$dataPoint = array(	$tock['atr'.$i] = array('link'.$i,'name'.$i));
	$dataPoints = array_merge($dataPoints,$dataPoint);	
	}
	return $dataPoints;
}
//  collected through value  

global $sa_header;
$thevalue = get_option( 'sa_header', $sa_header );
if(empty($thevalue['thevalue']))
{
	$exval ='5';
	
}else {$exval = $thevalue['thevalue'];}
$sa_header =  tock_arr($exval);


if ( is_admin() ) : // Load only if we are viewing an admin page

function sa_register_settings() {
   // Register settings and call sanitation functions
   register_setting( 'sa_theme_header', 'sa_header', 'sa_validate_header' );
}

add_action( 'admin_init', 'sa_register_settings' );

add_action('admin_menu', 'rc_admin_menus');

    function rc_admin_menus() {  
        add_submenu_page('options-general.php','RC Elements', 'Right click', 'manage_options',   
            'rcoptions', 'sa_theme_home_page');   
    }


// Function to generate options page
function sa_theme_home_page() {
   global $pagenow;

           theme_click_options();
}
// Function to generate options page
function theme_click_options() {
   global $sa_header;
   if ( ! isset( $_REQUEST['updated'] ) )
       $_REQUEST['updated'] = false; // This checks whether the form has just been submitted. ?>
<div class="wrap">
<?php screen_icon(); echo "<h2>Custom Right Click options</h2>"; ?>
<?php if ( false !== $_REQUEST['updated'] ) : ?>
<div class="updated fade"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
<?php endif; // If the form has just been submitted, this shows the notification ?>
<form method="post" action="options.php">
<?php $settings = get_option( 'sa_header', $sa_header )?>
<?php settings_fields( 'sa_theme_header' );?>
<style>
.rctable input[text]{
width:200px;float:left;margin-right:25px;
}
</style>
<table class="form-table rctable"><!-- Grab a hot cup of coffee, yes we're using tables! -->
<tr valign="top"><th scope="row"><label for="thevalue"><h3 style="margin: 0px;">No of fields</h3></label></th></tr>
<tr valign="top">
<td>
<input placeholder="enter only numbers" title="enter only numbers" pattern="[0-9]*" id="sa_header[thevalue]" name="sa_header[thevalue]" type="text" value="<?php  esc_attr_e($settings['thevalue']); ?>" /></td>
</tr>
<tr valign="top"><th scope="row"><label for="thevalue"><h3 style="margin: 0px;">Menu background color</h3></label></th></tr>
<tr valign="top">
<td>
<input placeholder="enter html color code" title="background color" id="sa_header[color]" name="sa_header[color]" type="text" value="<?php  esc_attr_e($settings['color']); ?>" /></td>
</tr>
<tr valign="top"><th scope="row"><label for="site_logo_url"><h3 style="margin: 0px;">Enter links and names</h3></label></th>
</tr>
<?php foreach ($sa_header as $i=> $lnval) { ?>
<tr> <td>
<input placeholder="enter the link" id="<?php echo  $lnval[0]?>" name="sa_header[<?php echo  $lnval[0]?>]" type="text" value="<?php  esc_attr_e($settings[$lnval[0]]); ?>" />
</td>
<td>
<input placeholder="enter the name" id="<?php echo  $lnval[1]?>" name="sa_header[<?php echo  $lnval[1]?>]" type="text" value="<?php  esc_attr_e($settings[$lnval[1]]); ?>" />
</td>
</tr>
<?php } ?>
</table>
<p class="submit"><input type="submit" class="button-primary" value="Save Options" /></p>
</form>
</div>
<?php
} 
function sa_validate_header( $input ) {
   global $sa_header;

   $settings = get_option( 'sa_header', $sa_header );
   
          //$input['atr1'] = wp_filter_nohtml_kses( $input['atr1'] );
      return $input;
}

endif;  // EndIf is_admin()
//for jquery load
function jq_rc_load()
{
wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts','jq_rc_load'); 
//for right click
function rc_main_sc()
{
global $sa_header;
   $settings = get_option( 'sa_header', $sa_header );
	?>
<script type="text/javascript" >
(function($){
    jQuery.fn.vscontext = function(options){
        var defaults = {
            menuBlock: null,
            offsetX : 8,
            offsetY : 8,
            speed : 'slow'
        };
        var options = $.extend(defaults, options);
        var menu_item = '.' + options.menuBlock;
        return this.each(function(){
            	$(this).bind("contextmenu",function(e){
				return false;
		});
            	$(this).mousedown(function(e){
                        var offsetX = e.pageX  + options.offsetX;
                        var offsetY = e.pageY + options.offsetY;
			if(e.button == "2"){
                            $(menu_item).show(options.speed);
                            $(menu_item).css('display','block');
                            $(menu_item).css('top',offsetY);
                            $(menu_item).css('left',offsetX);
			}else {
                            $(menu_item).hide(options.speed);
                        }
		});
                $(menu_item).hover(function(){}, function(){$(menu_item).hide(options.speed);})
                
        });
    };
})(jQuery);
</script>
<style type="text/css" >
           .vs-context-menu{
    display:none;
    position: absolute;
}

.vs-context-menu ul{
       list-style: none;
        padding: 0;
        margin: 0;position:relative;
        background:<?php if(strip_tags($settings['color'])){echo strip_tags($settings['color']).'!important';}else {echo '#eee';}?>;
    border: 1px solid #DDDDDD;
    width:auto;min-width: 220px;
    -moz-border-radius-topleft: 5px;
    -moz-border-radius-topright: 5px;
    -webkit-border-top-left-radius: 5px;
    -webkit-border-top-right-radius: 5px;
    font-size: 13px;
}

.vs-context-menu li{
    margin: 0px;
    padding: 3px;
}
.vs-context-menu ul a{
        display: block;
	padding: 1px 5px;
	padding-left: 28px;
        text-decoration: none;
        color: #000;
        background-repeat: no-repeat;padding: 10px;
}

.vs-context-menu ul li a:hover, .vs-context-menu ul li a:focus {
opacity:.8;background:#fff;
}

.seprator{
    border-bottom: 1px solid  #DDDDDD;
}
</style>
<?php }
add_action('wp_head', 'rc_main_sc');
add_action('wp_footer', 'rc_foot_sc');
function rc_foot_sc()
{
global $sa_header;
$settings = get_option( 'sa_header', $sa_header );
?>
<div class="vs-context-menu">
<ul>
<?php for($i=1;$i<=$settings['thevalue'];$i++){?>
<li><a href="<?php echo strip_tags($settings['link'.$i]); ?>" alt="<?php echo htmlentities($settings['name'.$i]); ?>"><?php echo htmlentities($settings['name'.$i]); ?></a></li>
<?php }?>
</ul>
</div>
<script type="text/javascript">
   jQuery(document).ready(function(){
      jQuery('html').vscontext({menuBlock: 'vs-context-menu'});
   });
</script>
<?php }?>