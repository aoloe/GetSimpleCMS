<?php

/**
 * Admin Stylesheet
 * 
 * @package GetSimple
 * @subpackage init
 */

header('Content-type: text/css');

include('../inc/common.php');

$offset = 30000;
#header ('Cache-Control: max-age=' . $offset . ', must-revalidate');
#header ('Expires: ' . gmdate ("D, d M Y H:i:s", time() + $offset) . ' GMT');
$nocache = true;
# check to see if cache is available for this
$cachefile = GSDATACACHE.'stylesheet.txt';
if (file_exists($cachefile) && time() - 600 < filemtime($cachefile) && !$nocache) {
	echo "/* Cached copy, generated ".date('H:i', filemtime($cachefile))." '".$cachefile."' */\n";
	echo file_get_contents($cachefile);
	exit;
} 

ob_start();

function compress($buffer) {
  $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer); /* remove comments */
  $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer); /* remove tabs, spaces, newlines, etc. */
  return $buffer;
}

if (file_exists(GSTHEMESPATH.'admin.xml')) {
	#load admin theme xml file
	$theme = getXML(GSTHEMESPATH.'admin.xml');
	
	$primary_0 = trim($theme->primary->darkest);
	$primary_1 = trim($theme->primary->darker);
	$primary_2 = trim($theme->primary->dark);
	$primary_3 = trim($theme->primary->middle);
	$primary_4 = trim($theme->primary->light);
	$primary_5 = trim($theme->primary->lighter);
	$primary_6 = trim($theme->primary->lightest);
	
	$secondary_0 = trim($theme->secondary->darkest);
	$secondary_1 = trim($theme->secondary->lightest);

	$label_0     = trim($theme->label->label_0); // label_default
	$label_1     = trim($theme->label->label_1); // label_info
	$label_2     = trim($theme->label->label_2); // label_ok
	$label_3     = trim($theme->label->label_3); // label_warn
	$label_4     = trim($theme->label->label_4); // label_error
	$label_5     = trim($theme->label->label_5); // label_medium
	$label_6     = trim($theme->label->label_6); // label_light
}

	# set default colors
	if(!is_object($primary_0)) $primary_0   = '#0E1316'; # darkest
	if(!is_object($primary_1)) $primary_1   = '#182227';
	if(!is_object($primary_2)) $primary_2   = '#283840';
	if(!is_object($primary_3)) $primary_3   = '#415A66';
	if(!is_object($primary_4)) $primary_4   = '#618899';
	if(!is_object($primary_5)) $primary_5   = '#E8EDF0';
	if(!is_object($primary_6)) $primary_6   = '#AFC5CF'; # lightest
	
	if(!is_object($secondary_0)) $secondary_0 = '#9F2C04'; # darkest
	if(!is_object($secondary_1)) $secondary_1 = '#CF3805'; # lightest

	if(!is_object($label_0))   $label_0     = '#F2F2F2'; // label_default
	if(!is_object($label_1))   $label_1     = '#0B5584'; // label_info
	if(!is_object($label_2))   $label_2     = '#008C00'; // label_ok
	if(!is_object($label_3))   $label_3     = '#FF8500'; // label_warn
	if(!is_object($label_4))   $label_4     = '#CC0000'; // label_error
	if(!is_object($label_5))   $label_5     = '#FFFFFF'; // label_light
	if(!is_object($label_6))   $label_6     = '#999999'; // label_medium

include('css.php');

if( isset($_GET['s']) and in_array('wide',explode(',',$_GET['s'])) ) include('css-wide.php');

file_put_contents($cachefile, compress(ob_get_contents()));
chmod($cachefile, 0644);

ob_end_flush();