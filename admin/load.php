<?php 
/**
 * Load Plugin
 *
 * Displays the plugin file passed to it 
 *
 * @package GetSimple
 * @subpackage Plugins
 */


# Setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

global $plugin_info;

# verify a plugin was passed to this page
if (!array_key_exists('id', $_REQUEST) || !array_key_exists($_REQUEST['id'], $plugin_info)) {
	redirect('plugins.php');
}

# include the plugin
$plugin_id = $_GET['id'];

get_template('header', cl($SITENAME).' &raquo; '. $plugin_info[$plugin_id]['name']); 

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<div class="main">

		<?php 
			if(function_exists($plugin_info[$plugin_id]['load_data'])){
				call_user_func_array($plugin_info[$plugin_id]['load_data'],array()); 
			}	
		?>

		</div>
	</div>
	
	<div id="sidebar" >
    <?php 
      $res = (@include('template/sidebar-'.$plugin_info[$plugin_id]['page_type'].'.php'));
      if (!$res) { 
    ?>
      <ul class="snav">
        <?php exec_action($plugin_info[$plugin_id]['page_type']."-sidebar"); ?>
      </ul>
    <?php
	}
	// call sidebar extra hook for plugin page_type
	exec_action($plugin_info[$plugin_id]['page_type']."-sidebar-extra");     
    ?>
  </div>

</div>
<?php get_template('footer'); ?>
