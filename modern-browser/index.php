<?php
/*
Plugin Name: Modern Browsing
Plugin URI: http://royalestudios.com/blog/labs/modern-browsing/
Description: The plugin detects the browser and if it's obsolte it shows an alternative non intrusive suggestion
Version: 0.1
Author: Royal Estudios
Author URI: http://royalestudios.com
License: GPL2
*/

$url = plugins_url();

class modern_browse {
	function javascript_init() {
		wp_register_style('mb_style', WP_PLUGIN_URL . '/modern-browser/style.css');
		
		if(!is_admin()) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('mb_style');
		}
	}
	function create_menu() {
		//create new top-level menu
		add_submenu_page( 'options-general.php', 'Modern Browsing', 'Modern Browsing', 'administrator', 'modern-browsing', array('modern_browse','options_page') );
	}
	function register_settings(){
		register_setting( 'mb_settings', 'enable_hide' );
		register_setting( 'mb_settings', 'shiv' );
		register_setting( 'mb_settings', 'browser' );
	}
	function options_page(){ ?>
<div class="wrap">
	<div class="icon32" id="icon-options-general"><br></div>
    <h2><?php _e('Modern Browsing Options','mb'); ?></h2>
    <form method="post" action="options.php">
    	<?php settings_fields( 'mb_settings' ); ?>
		<?php do_settings_fields('modern-browsing', 'mb_settings' ); ?>
        <?php
			$browser_list = array(
				'Internet Explorer' => array('6' => '6+', '7' => '7+', '8' => '8+','9'=> '9+'),
				'Safari' => array('3' => '3+', '4' => '4+', '5' => '5+', '5.1' => '5.1+'),
				'Firefox' => array('3' => '3+', '4' => '4+', '5' => '5+', '6' => '6+', '7' => '7+', '8' => '8+', '9' => '9+', '10' => '10+', '11' => '11+'),
				'Opera' => array('8' => '8+', '9' => '9+', '10' => '10+', '11' => '11+')
			);
		?>
        <table class="form-table">
            <tr valign="top">
            <th scope="row"><?php _e('Bar Hiding', 'mb'); ?></th>
            <td><label for="enable_hide"><input type="checkbox" id="enable_hide" name="enable_hide" <?php echo (get_option('enable_hide')) ? ' hecked' : ''; ?>/><?php _e('Enable user bar remove', 'mb'); ?></label></td>
            </tr>
            <th scope="row"><?php _e('Add HTML5 Shiv', 'mb'); ?></th>
            <td><label for="shiv"><input type="checkbox" id="shiv" name="shiv" <?php echo (get_option('shiv')) ? 'checked' : ''; ?>/><?php _e('HTML5 support for IE', 'mb'); ?></label></td>
            </tr>
        </table>     
        <h3><?php _e('Select browser compatility','mb'); ?></h3>
        <?php $browser_option = get_option('browser'); ?>
        <table class="form-table">
        	<?php foreach($browser_list as $name => $versions) { ?>
            <?php $browser_id = str_replace(' ', '-', strtolower($name)); ?>
        	<tr valign="top">
            <th scope="row"><?php echo $name; ?></th>
            <td>
                <select name="browser[<?php echo $browser_id;  ?>]">
                <?php foreach($versions as $k => $v) { ?>
					<option value="<?php echo $k; ?>"<?php echo ($k == $browser_option[$browser_id]) ? ' selected="selected"' : ''; ?>><?php echo $v; ?></option>
				<?php } ?>
                </select>
            </td>
            </tr>
            <?php } ?>
        </table>
        
        <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
</div>
<?php
	}
	function browser(){
		if(!is_admin()) {
			
			//Thanks to http://chrisschuld.com/projects/browser-php-detecting-a-users-browser-from-php/
			include_once(WP_PLUGIN_DIR . '/modern-browser/Browser.php');
		}
		load_plugin_textdomain( 'mb', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	
	function show_bar(){
		if(!is_admin()) { 
		$browser_option = get_option('browser');
		
		//browsers download
		$updated_browsers = array('safari' => 'http://www.apple.com/es/safari/download/', 'firefox' =>'http://www.mozilla.org/en-US/firefox/new/', 'internet-explorer' => 'http://windows.microsoft.com/es-ES/internet-explorer/products/ie/home', 'opera' => 'http://www.opera.com/download/');
		
		//creating a new Browser
		$browser = new Browser();
		$current_browser = $browser->getBrowser();
		$current_browser_item = str_replace(' ', '-', strtolower($current_browser));
		
		//box content message
		$content = __('<div class="mb_title"><h3>Your browser is obsolete!</h3></div><div class="mb_message"><p>This site was developed under the lastest web standards, and to check all cool features made for you, it is important to update your browser</p><p>With an updated browser, you can:</p></div>','mb');
		
		//benefits array for display in the site
		$benefits = array('speed' => __('More speed and performance','mb'), 'security' => __('Security','mb'), 'support' => __('Support for the latest technologies','mb'), 'more' => __('because this and much more benefits...','mb'), 'version' => __('Your version','mb'));
		
		$benefits_icons = '';
		foreach($benefits as $k => $v){
			$benefits_icons .= '<span class="' . $k . ' mb_icon">' . $v . '</span>';
		}
		
		$message = __("You are using an obsolete version of ","mb") . $current_browser . '.' . __(" For a better experience in this web site ",'mb');
		$message .= '<a href="' . $updated_browsers[$current_browser_item] . '" class="mb_button">' . __('Update your browser','mb') . '</a>';
		$message .= '<a href="#" target="_blank" class="mb_mi">' . __('More information','mb') . '</a>';
		
		
		
?>
<?php if(get_option('shiv') == 'on') { ?>
<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<?php } ?>
<script type="text/javascript">
jQuery(function($){
	$('.mb_mi').live('click', function(){
		if($(this).hasClass('opened')){
			$('.mb_toolbar').stop(true, true).animate({ top : -170}, 300);
		} else{
			$('.mb_toolbar').stop(true, true).animate({ top : 0}, 300);
		}
		$(this).toggleClass('opened');
		return false;
	});
	var browser;
	
	var text = '<?php echo $message; ?>';
	var content = '<?php echo $content; ?>';
	var currentBrowser = '<?php echo $current_browser_item; ?>';
	var icons = '<?php echo $benefits_icons; ?>';
	
	function constructor(browserName, version){
		$('body').prepend('<div class="mb_toolbar ' + browserName + '"><div class="mb_wrap"><div class="mb_content">' + content  + icons + '</div><div class="mb_handle">' + text + '</div></div></div>');
	}
	<?php if(intval($browser_option[$current_browser_item]) > $browser->getVersion() ) { ?>
	constructor(currentBrowser, '<?php $browser->getVersion() ?>');
	<?php } ?>
	
});
</script>	
	<?php 
		} //admin
	} //show_bar
}
add_action('wp_enqueue_scripts', array('modern_browse', 'javascript_init'));
add_action('admin_menu', array('modern_browse', 'create_menu'));
add_action( 'admin_init', array('modern_browse', 'register_settings') );
add_action( 'wp_head', array('modern_browse', 'show_bar') );
add_action( 'init', array('modern_browse', 'browser') );
?>