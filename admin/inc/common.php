<?php
/**
 * Common Setup File
 * 
 * This file initializes up most variables for the site. It is also where most files
 * are included from. It also reads and stores certain variables.
 *
 * @package GetSimple
 * @subpackage init
 */

function unreal__FILE__() {
    $result = __FILE__;
    // echo("<p>res: $result</p>");
    $f = $_SERVER['SCRIPT_FILENAME'];
    // echo("<p>f: $f</p>");
    $rf = realpath($_SERVER['SCRIPT_FILENAME']);
    // echo("<p>rf: $rf</p>");
    // TODO: it does not work if index.php is not from the same repo as admin/
    // TODO: is there a way to support files that have renamed in the symlink? index.php -> index2.php
    if ($f != $rf) {
        $ff = explode(DIRECTORY_SEPARATOR, $f);
        $rff = explode(DIRECTORY_SEPARATOR, $rf);
        // echo("<pre>ff:\n".print_r($ff, 1)."</pre>");
        // echo("<pre>rff:\n".print_r($rff, 1)."</pre>");
        while (!empty($ff) && !empty($rff)) {
            $fi = array_pop($ff);
            // echo("<p>fi: $fi</p>");
            $rfi = array_pop($rff);
            // echo("<p>rfi: $rfi</p>");
            if ($fi != $rfi) {
                $ff[] = $fi;
                $rff[] = $rfi;
                break;
            }
        }
        // echo("<pre>ff:\n".print_r($ff, 1)."</pre>");
        // echo("<pre>rff:\n".print_r($rff, 1)."</pre>");
        $result = implode(DIRECTORY_SEPARATOR, $ff).substr(__FILE__, strlen(implode(DIRECTORY_SEPARATOR, $rff)));
    }
    // echo("<p>res: $result</p>");
    return $result;
}

define('IN_GS', TRUE); // GS enviroment flag

// GS Debugger
global $GS_debug; // GS debug trace array
if(!isset($GS_debug)) $GS_debug = array();	

/**
 * Debug Console Log
 *
 * @since 3.1
 *
 * @param $txt string
 */
function debugLog($txt = '') {
	global $GS_debug;
	array_push($GS_debug,$txt);
}

/**
 * Set PHP enviroment
 */
if(function_exists('mb_internal_encoding')) mb_internal_encoding("UTF-8"); // set multibyte encoding

/**
 *  GSCONFIG definitions
 */
if(!defined('GSSTYLEWIDE')) define('GSSTYLEWIDE','wide'); // wide style sheet
if(!defined('GSSTYLE_SBFIXED')) define('GSSTYLE_SBFIXED','sbfixed'); // fixed sidebar

/**
 * Variable Globalization
 */
global 
 $SITENAME,       // sitename setting
 $SITEURL,        // siteurl setting
 $TEMPLATE,       // current theme
 $TIMEZONE,       // current timezone either from config or user
 $LANG,           // settings language
 $SALT,           // salt holds gsconfig GSUSECUSTOMSALT or authentication.xml salt
 $i18n,
 $USR,            // logged in user
 $PERMALINK,      // permalink structure
 $GSADMIN,        // admin foldername
 $GS_debug,       // debug log array
 $components,     // components array
 $nocache,        // disable site wide cache
 $microtime_start,// used for benchmark timers
 $pagesArray      // page cache array, used for all page fields aside from content
;

if(isset($_GET['nocache'])){
	// @todo: disables caching, this should probably only be allowed for auth users
	$nocache = true;
}

/**
 * Init debug log array
 */
$GS_debug = array();

/*
 * Defines Root Path
 */
// hack to accept xxxx.php and admin/xxxx.php as an entry point and return the root path of the site
// XXX: there is a get_root_path also in admin/inc/template_functions.php ... i hope that this one is replacing it
function get_root_path() {
    $result = "";
    $result = dirname($_SERVER['SCRIPT_FILENAME']);
    // TODO: is there a way to solve this without listing the special cases?
    if (basename($result) == "template" && basename(dirname($result)) == "admin") {
        $result = dirname(dirname($result));
    } elseif (basename($result) == "admin") {
        $result = dirname($result);
    }
    return $result;
}
// define('GSROOTPATH', dirname(dirname(dirname(unreal__FILE__()))).DIRECTORY_SEPARATOR);
define('GSROOTPATH', get_root_path().DIRECTORY_SEPARATOR);
// echo(GSROOTPATH);

/*
 * Load config
 */
if(!isset($base)){
if (file_exists(GSROOTPATH . 'gsconfig.php')) {
	require_once(GSROOTPATH . 'gsconfig.php');
}

/*
 * Set custom GSADMINPATH path from config
 */
if (defined('GSADMIN')) {
	# make sure trailing slashes are standardized from user input
	$GSADMIN = rtrim(GSADMIN,'/\\');
} else {
	$GSADMIN = 'admin';
}
}

/**
 * Define some constants
 */
define('GSADMINPATH'     , GSROOTPATH      . $GSADMIN.'/'); // admin/
define('GSADMININCPATH'  , GSADMINPATH     . 'inc/');       // admin/inc/
define('GSPLUGINPATH'    , GSROOTPATH      . 'plugins/');   // plugins/
define('GSLANGPATH'      , GSADMINPATH     . 'lang/');      // lang/

// data
define('GSDATAPATH'      , GSROOTPATH      . 'data/');      // data/
define('GSDATAOTHERPATH' , GSDATAPATH      . 'other/');     // data/other/
define('GSDATAPAGESPATH' , GSDATAPATH      . 'pages/');     // data/pages/
define('GSAUTOSAVEPATH'  , GSDATAPAGESPATH . 'autosave/');  // data/pages/autosave/
define('GSDATAUPLOADPATH', GSDATAPATH      . 'uploads/');   // data/uploads/
define('GSTHUMBNAILPATH' , GSDATAPATH      . 'thumbs/');    // data/thumbs/
define('GSUSERSPATH'     , GSDATAPATH      . 'users/');     // data/users/
define('GSCACHEPATH'     , GSDATAPATH      . 'cache/');     // data/cache/

define('GSBACKUPSPATH'   , GSROOTPATH      . 'backups/');   // backups/
define('GSBACKUSERSPATH' , GSBACKUPSPATH   . 'users/');     // backups/users
define('GSTHEMESPATH'    , GSROOTPATH      . 'theme/');     // theme/

/**
 * Init debug mode
 * Enable php error logging	
 */
if(defined('GSDEBUG') and (bool)GSDEBUG == true) {
	error_reporting(-1);
	ini_set('display_errors', 1);
	$nocache = true;
} else if( defined('SUPRESSERRORS') and (bool)SUPPRESSERRORS == true ) {
	error_reporting(0);
	ini_set('display_errors', 0);
}

ini_set('log_errors', 1);
ini_set('error_log', GSDATAOTHERPATH .'logs/errorlog.txt');


/**
 * Basic file inclusions
 */
include('basic.php');
include('template_functions.php');
include('theme_functions.php');
include('logging.class.php');

require_once(GSADMININCPATH.'configuration.php');

/**
 * Bad stuff protection
 */
include_once('security_functions.php');

if (version_compare(PHP_VERSION, "5")  >= 0) {
	foreach ($_GET as &$xss) $xss = antixss($xss);
}

/**
 * Variable check to prevent debugging going off
 * @todo some of these may not even be needed anymore
 */
$admin_relative = (isset($admin_relative)) ? $admin_relative : '';
$lang_relative = (isset($lang_relative)) ? $lang_relative : '';
$load['login'] = (isset($load['login'])) ? $load['login'] : '';
$load['plugin'] = (isset($load['plugin'])) ? $load['plugin'] : '';



/**
 * Pull data from storage
 */
 
/** grab website data */
$thisfilew = GSDATAOTHERPATH .'website.xml';
if (file_exists($thisfilew)) {
	$dataw = getXML($thisfilew);
	$SITENAME = stripslashes($dataw->SITENAME);
	$SITEURL = $dataw->SITEURL;
	$TEMPLATE = $dataw->TEMPLATE;
	$PRETTYURLS = $dataw->PRETTYURLS;
	$PERMALINK = $dataw->PERMALINK;
} else {
	$SITENAME = '';
	$SITEURL = '';
} 


/** grab user data */
if (isset($_COOKIE['GS_ADMIN_USERNAME'])) {
	$cookie_user_id = _id($_COOKIE['GS_ADMIN_USERNAME']);
	if (file_exists(GSUSERSPATH . $cookie_user_id.'.xml')) {
		$datau = getXML(GSUSERSPATH  . $cookie_user_id.'.xml');
		$USR = stripslashes($datau->USR);
		$HTMLEDITOR = $datau->HTMLEDITOR;
		$TIMEZONE = $datau->TIMEZONE;
		$LANG = $datau->LANG;
	} else {
		$USR = null;
	}
} else {
	$USR = null;
}



/** grab authorization and security data */

if (defined('GSUSECUSTOMSALT')) {
	// use GSUSECUSTOMSALT
	$SALT = sha1(GSUSECUSTOMSALT);
} 
else {
	// use from authorization.xml
	if (file_exists(GSDATAOTHERPATH .'authorization.xml')) {
		$dataa = getXML(GSDATAOTHERPATH .'authorization.xml');
		$SALT = stripslashes($dataa->apikey);
	} else {
		if($SITEURL !='' && notInInstall()) die(i18n_r('KILL_CANT_CONTINUE')."<br/>".i18n_r('MISSING_FILE').": "."authorization.xml");
	}
}

$SESSIONHASH = sha1($SALT . $SITENAME);

/**
 * Language control
 */
if(!isset($LANG) || $LANG == '') {
	$filenames = getFiles(GSLANGPATH);
	$cntlang = count($filenames);
	if ($cntlang == 1) {
		$LANG = basename($filenames[0], ".php");
	} elseif($cntlang > 1) {
		$LANG = 'en_US';
	}
}

include_once(GSLANGPATH . $LANG . '.php');

// Merge in default lang to avoid empty lang tokens
// if GSMERGELANG is undefined or false merge en_US
if(getDef('GSMERGELANG', true) !== false and !getDef('GSMERGELANG', true) ){
	if($LANG !='en_US')	i18n_merge(null,"en_US");
} else{
	// merge GSMERGELANG defined lang
	if($LANG !=getDef('GSMERGELANG') ) i18n_merge(null,getDef('GSMERGELANG'));	
}	

// Set Locale
if (array_key_exists('LOCALE', $i18n))
  setlocale(LC_ALL, preg_split('/s*,s*/', $i18n['LOCALE']));

/** 
 * Init Editor globals
 * @uses $EDHEIGHT
 * @uses $EDLANG
 * @uses $EDTOOL js array string | php array | 'none' | ck toolbar_ name
 * @uses $EDOPTIONS js obj param strings, comma delimited
 */
if (defined('GSEDITORHEIGHT')) { $EDHEIGHT = GSEDITORHEIGHT .'px'; } else {	$EDHEIGHT = '500px'; }
if (defined('GSEDITORLANG'))   { $EDLANG = GSEDITORLANG; } else {	$EDLANG = i18n_r('CKEDITOR_LANG'); }
if (defined('GSEDITORTOOL') and !isset($EDTOOL)) { $EDTOOL = GSEDITORTOOL; }
if (defined('GSEDITOROPTIONS') and !isset($EDOPTIONS) && trim(GSEDITOROPTIONS)!="" ) $EDOPTIONS = GSEDITOROPTIONS; 

if(!isset($EDTOOL)) $EDTOOL = 'basic'; // default gs toolbar

if(strpos($EDTOOL,'[')!==false){ $EDTOOL = "[$EDTOOL]"; } // toolbar is js array
else if(is_array($EDTOOL)) $EDTOOL = json_encode($EDTOOL); // toolbar is php array, convert to js str
// else if($EDTOOL === null) $EDTOOL = 'null'; // not supported in cke 3.x
else if($EDTOOL == "none") $EDTOOL = null; // toolbar to use cke default
else $EDTOOL = "'$EDTOOL'"; // toolbar is a toolbar config variable config.js config.toolbar_$var = []


/**
 * Timezone setup
 */

// set defined timezone from config if not set on user
if( (!isset($TIMEZONE) || trim($TIMEZONE) == '' ) && defined('GSTIMEZONE') ){
	$TIMEZONE = GSTIMEZONE;
}

if(isset($TIMEZONE) && function_exists('date_default_timezone_set') && ($TIMEZONE != "" || stripos($TIMEZONE, '--')) ) { 
	date_default_timezone_set($TIMEZONE);
}


function serviceUnavailable(){
	GLOBAL $base;
	if(isset($base)){
		header('HTTP/1.1 503 Service Temporarily Unavailable');
		header('Status: 503 Service Temporarily Unavailable');
		header('Retry-After: 7200'); // in seconds
		i18n('SERVICE_UNAVAILABLE');
		die();
	}
}

/**
 * Check to make sure site is already installed
 */
if (notInInstall()) {
	$fullpath = suggest_site_path();
	
	# if there is no SITEURL set, then it's a fresh install. Start installation process
	# siteurl check is not good for pre 3.0 since it will be empty, so skip and run update first.
	if ($SITEURL == '' &&  get_gs_version() >= 3.0)	{
		serviceUnavailable();
		redirect($fullpath . $GSADMIN.'/install.php');
	} 
	else {	
	# if an update file was included in the install package, redirect there first	
		if (file_exists(GSADMINPATH.'update.php') && !isset($_GET['updated']) && !getDef('GSDEBUGINSTALL'))	{
			serviceUnavailable();
			redirect($fullpath . $GSADMIN.'/update.php');
		}
	}

	if(!getDef('GSDEBUGINSTALL',true)){	
		# if you've made it this far, the site is already installed so remove the installation files
		$filedeletionstatus=true;
        foreach (array('install.php', 'setup.php', 'update.php') as $item) {
            if (file_exists(GSADMINPATH.$item))	{
                $filedeletionstatus = $filedeletionstatus && is_writable(GSADMINPATH.$item) && unlink(GSADMINPATH.$item);
            }
        }
		if (!$filedeletionstatus) {
			$error = sprintf(i18n_r('ERR_CANNOT_DELETE'), '<code>/'.$GSADMIN.'/install.php</code>, <code>/'.$GSADMIN.'/setup.php</code> or <code>/'.$GSADMIN.'/update.php</code>');
		}
	}

		}

/**
 * Include other files depending if they are needed or not
 */
include_once(GSADMININCPATH.'cookie_functions.php');

if(isset($load['plugin']) && $load['plugin']){
	# remove the pages.php plugin if it exists. 	
	if (file_exists(GSPLUGINPATH.'pages.php'))	{
		unlink(GSPLUGINPATH.'pages.php');
	}

	include_once(GSADMININCPATH.'plugin_functions.php');

	if(get_filename_id()=='settings' || get_filename_id()=='load') {
		/* this core plugin only needs to be visible when you are viewing the 
		settings page since that is where its sidebar item is. */
		if (defined('GSEXTAPI') && GSEXTAPI==1) {
			include_once('api.plugin.php');
		}
	}

	# include core plugin for page caching
	include_once('caching_functions.php');
	
	# main hook for common.php
	exec_action('common');
	
}
if(isset($load['login']) && $load['login']){ 	include_once(GSADMININCPATH.'login_functions.php'); }
