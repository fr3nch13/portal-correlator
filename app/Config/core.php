<?php
/**
 * This is core configuration file.
 *
 * Use it to configure core behavior of Cake.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Used to track the processing time, and not just in the views
 */
if (!defined('PROC_START'))
{
	define('PROC_START', microtime(true));
}
/**
 * CakePHP Debug Level:
 *
 * Production Mode:
 * 	0: No error messages, errors, or warnings shown. Flash messages redirect.
 *
 * Development Mode:
 * 	1: Errors and warnings shown, model caches refreshed, flash messages halted.
 * 	2: As in 1, but also with full debug messages and SQL output.
 *
 * In production mode, flash messages redirect after a time interval.
 * In development mode, you need to click the flash message to continue.
 */
	Configure::write('debug', 2);

/**
 * Configure the Error handler used to handle errors for your application.  By default
 * ErrorHandler::handleError() is used.  It will display errors using Debugger, when debug > 0
 * and log errors with CakeLog when debug = 0.
 *
 * Options:
 *
 * - `handler` - callback - The callback to handle errors. You can set this to any callback type,
 *    including anonymous functions.
 * - `level` - int - The level of errors you are interested in capturing.
 * - `trace` - boolean - Include stack traces for errors in log files.
 *
 * @see ErrorHandler for more information on error handling and configuration.
 */
	Configure::write('Error', array(
		'handler' => 'ErrorHandler::handleError',
		'level' => E_ALL & ~E_DEPRECATED,
		'trace' => true
	));

/**
 * Configure the Exception handler used for uncaught exceptions.  By default,
 * ErrorHandler::handleException() is used. It will display a HTML page for the exception, and
 * while debug > 0, framework errors like Missing Controller will be displayed.  When debug = 0,
 * framework errors will be coerced into generic HTTP errors.
 *
 * Options:
 *
 * - `handler` - callback - The callback to handle exceptions. You can set this to any callback type,
 *   including anonymous functions.
 * - `renderer` - string - The class responsible for rendering uncaught exceptions.  If you choose a custom class you
 *   should place the file for that class in app/Lib/Error. This class needs to implement a render method.
 * - `log` - boolean - Should Exceptions be logged?
 *
 * @see ErrorHandler for more information on exception handling and configuration.
 */
	Configure::write('Exception', array(
		'handler' => 'ErrorHandler::handleException',
		'renderer' => 'ExceptionRenderer',
		'log' => true
	));

/**
 * Application wide charset encoding
 */
	Configure::write('App.encoding', 'UTF-8');

/**
 * To configure CakePHP *not* to use mod_rewrite and to
 * use CakePHP pretty URLs, remove these .htaccess
 * files:
 *
 * /.htaccess
 * /app/.htaccess
 * /app/webroot/.htaccess
 *
 * And uncomment the App.baseUrl below:
 */
	//Configure::write('App.baseUrl', env('SCRIPT_NAME'));

/**
 * The different roles a user can be assigned to
 *
 */
Configure::write('App.user_roles', array('admin' => 'Admin', 'manager' => 'Manager', 'regular' => 'Regular'));

/**
 * Uncomment the define below to use CakePHP prefix routes.
 *
 * The value of the define determines the names of the routes
 * and their associated controller actions:
 *
 * Set to an array of prefixes you want to use in your application. Use for
 * admin or other prefixed routes.
 *
 * 	Routing.prefixes = array('admin', 'manager');
 *
 * Enables:
 *	`admin_index()` and `/admin/controller/index`
 *	`manager_index()` and `/manager/controller/index`
 *
 */
Configure::write('Routing.prefixes', array('admin', 'regular', 'manager'));


/**
 * Turn off all caching application-wide.
 *
 */
	//Configure::write('Cache.disable', true);

/**
 * Enable cache checking.
 *
 * If set to true, for view caching you must still use the controller
 * public $cacheAction inside your controllers to define caching settings.
 * You can either set it controller-wide by setting public $cacheAction = true,
 * or in each action using $this->cacheAction = true.
 *
 */
	//Configure::write('Cache.check', true);

/**
 * Defines the default error type when using the log() function. Used for
 * differentiating error logging and debugging. Currently PHP supports LOG_DEBUG.
 */
	define('LOG_ERROR', LOG_ERR);

/**
 * Session configuration.
 *
 * Contains an array of settings to use for session configuration. The defaults key is
 * used to define a default preset to use for sessions, any settings declared here will override
 * the settings of the default config.
 *
 * ## Options
 *
 * - `Session.cookie` - The name of the cookie to use. Defaults to 'CAKEPHP'
 * - `Session.timeout` - The number of minutes you want sessions to live for. This timeout is handled by CakePHP
 * - `Session.cookieTimeout` - The number of minutes you want session cookies to live for.
 * - `Session.checkAgent` - Do you want the user agent to be checked when starting sessions? You might want to set the
 *    value to false, when dealing with older versions of IE, Chrome Frame or certain web-browsing devices and AJAX
 * - `Session.defaults` - The default configuration set to use as a basis for your session.
 *    There are four builtins: php, cake, cache, database.
 * - `Session.handler` - Can be used to enable a custom session handler.  Expects an array of of callables,
 *    that can be used with `session_save_handler`.  Using this option will automatically add `session.save_handler`
 *    to the ini array.
 * - `Session.autoRegenerate` - Enabling this setting, turns on automatic renewal of sessions, and
 *    sessionids that change frequently. See CakeSession::$requestCountdown.
 * - `Session.ini` - An associative array of additional ini values to set.
 *
 * The built in defaults are:
 *
 * - 'php' - Uses settings defined in your php.ini.
 * - 'cake' - Saves session files in CakePHP's /tmp directory.
 * - 'database' - Uses CakePHP's database sessions.
 * - 'cache' - Use the Cache class to save sessions.
 *
 * To define a custom session handler, save it at /app/Model/Datasource/Session/<name>.php.
 * Make sure the class implements `CakeSessionHandlerInterface` and set Session.handler to <name>
 *
 * To use database sessions, run the app/Config/Schema/sessions.php schema using
 * the cake shell command: cake schema create Sessions
 *
 */
	Configure::write('Session', array(
		'cookie' => 'CORRELATOR',
		'defaults' => 'php',
		'timeout' => 360,
		'cookieTimeout' => 360,
		'autoRegenerate' => false,
		'checkAgent' => true,
	));

/**
 * The level of CakePHP security.
 */
	Configure::write('Security.level', 'medium');

/**
 * A random string used in security hashing methods.
 */
	Configure::write('Security.salt', '');

/**
 * A random numeric string (digits only) used to encrypt/decrypt strings.
 */
	Configure::write('Security.cipherSeed', '');

/**
 * Apply timestamps with the last modified time to static assets (js, css, images).
 * Will append a querystring parameter containing the time the file was modified. This is
 * useful for invalidating browser caches.
 *
 * Set to `true` to apply timestamps when debug > 0. Set to 'force' to always enable
 * timestamping regardless of debug value.
 */
	//Configure::write('Asset.timestamp', true);

/**
 * Compress CSS output by removing comments, whitespace, repeating tags, etc.
 * This requires a/var/cache directory to be writable by the web server for caching.
 * and /vendors/csspp/csspp.php
 *
 * To use, prefix the CSS link URL with '/ccss/' instead of '/css/' or use HtmlHelper::css().
 */
	//Configure::write('Asset.filter.css', 'css.php');

/**
 * Plug in your own custom JavaScript compressor by dropping a script in your webroot to handle the
 * output, and setting the config below to the name of the script.
 *
 * To use, prefix your JavaScript link URLs with '/cjs/' instead of '/js/' or use JavaScriptHelper::link().
 */
	//Configure::write('Asset.filter.js', 'custom_javascript_output_filter.php');

/**
 * The classname and database used in CakePHP's
 * access control lists.
 */
	Configure::write('Acl.classname', 'DbAcl');
	Configure::write('Acl.database', 'default');

/**
 * Uncomment this line and correct your server timezone to fix 
 * any date & time related errors.
 */
	date_default_timezone_set('America/New_York');

/**
 * Pick the caching engine to use.  If APC is enabled use it.
 * If running via cli - apc is disabled by default. ensure it's available and enabled in this case
 *
 * Note: 'default' and other application caches should be configured in app/Config/bootstrap.php.
 *       Please check the comments in boostrap.php for more info on the cache engines available 
 *       and their setttings.
 */
$engine = 'File';
/*
if (extension_loaded('apc') && function_exists('apc_dec') && (php_sapi_name() !== 'cli' || ini_get('apc.enable_cli'))) {
	$engine = 'Apc';
}
*/

// In development mode, caches should expire quickly.
$duration = '+999 days';
if (Configure::read('debug') >= 1) {
	$duration = '+10 seconds';
}

// Prefix each application on the same server with a different string, to avoid Memcache and APC conflicts.
$prefix = 'correlator_';
if(defined('ROOT'))
{
	$parts = explode(DS, ROOT);
	$prefix = array_pop($parts). '_';
}

/**
 * Configure the cache used for general framework caching.  Path information,
 * object listings, and translation cache files are stored with this configuration.
 */
Cache::config('_cake_core_', array(
	'engine' => $engine,
	'prefix' => $prefix . 'cake_core_',
	'path' => CACHE . 'persistent' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => $duration,
	'mask' => 0666,
));

/**
 * Configure the cache for model and datasource caches.  This cache configuration
 * is used to store schema descriptions, and table listings in connections.
 */
Cache::config('_cake_model_', array(
	'engine' => $engine,
	'prefix' => $prefix . 'cake_model_',
	'path' => CACHE . 'models' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => $duration,
	'mask' => 0666,
));


/**
 * Application Specific Settings
 *
 */

Configure::write('Site.title', __('Portal: Correlator'));


// the Imports/Importer Config settings
Configure::write('Site.exportable', true);

// the valid extensions
//Configure::write('Site.export_extensions', array('csv', 'xml', 'json', 'maltego'));
Configure::write('Site.export_extensions', array('csv', 'xml', 'json', 'txt'));

// used to obfuscate the original source map
Configure::write('Site.source_map', array(
	'hexillion' => __('DNS Source A'),
	'zoneedit' => __('DNS Source B'),
	'dnsdbapi' => __('DNS Source C'),
	'domaintools_dns' => __('DNS Source D'),
	'domaintools' => __('DNS Source D'),
	'domaintools_dns_history' => __('DNS Source E'),
	'domaintools_history' => __('DNS Source E'),
	'whoisxmlapi' => __('Whois Source A'),
	'domaintools_whois' => __('Whois Source B'),
	'domaintools_whois_history' => __('Whois Source C'),
));

// Signature Types
Configure::write('Site.sig_type_map', array(
	'snort' => __('Snort'),
	'yara' => __('Yara'),
));

// the Imports/Importer Config settings
Configure::write('Importer.parsers', array(
	'csv' => __('CSV File'),
));

Configure::write('Importer.locations', array(
//	'url' => __('URL'),
	'local' => __('Local Path'),
//	'upload' => __('Uploaded File'),
));

Configure::write('Importer.cron_options', array(
	'0' => __('No, Manually add Imports from this Manager.'),
	'1' => __('Yes, auto add Imports from this Manager.'),
));

Configure::write('Importer.auto_reviewed_options', array(
	'0' => __('No, require the vectors to be reviewed.'),
	'1' => __('Yes, make the vectors active.'),
));

/**
 * Include the application specific config if the file exists.
 * 
 */
 	// the keys that will be used to help create/modify the app_config file
 	// keep the keys linear, value should match what would be accepted by Form::input
	Configure::write('AppConfigKeys', array(
		'Site.title' => array(
			'label' => __('Website Title'), 
			'type' => 'text',
			'div' => array('class' => 'half'),
		),
		'Site.base_url' => array(
			'label' => __('Base URL for the website'), 
			'type' => 'text',
			'div' => array('class' => 'half'),
		),
		'Site.debug' => array(
			'label' => __('Debug level'),
			'options' => array(
				0 => __('0: No error messages, errors, or warnings shown. Flash messages redirect.'),
				1 => __('1: Errors and warnings shown, model caches refreshed, flash messages halted.'),
				2 => __('2: As in 1, but also with full debug messages and SQL output.'),
			),
			'div' => array('class' => 'half'),
		),
		'RememberMe.cookie.time' => array(
			'label' => __('Website Login Timeout'), 
			'options' => array(
				'+10 Minutes' => __('10 Minutes'),
				'+30 Minutes' => __('30 Minutes'),
				'+1 Hour' => __('1 Hour'),
				'+2 Hours' => __('2 Hours'),
				'+1 Day' => __('1 Day'),
			),
			'div' => array('class' => 'half'),
		),
		'Proctime.threshold' => array(
			'label' => 'Process time threshold',
			'type' => 'text',
			'between' => __('The time is in seconds, it can accept anything over 0 (like: 0.1). This is the process time tracking threshold to track if we should log requests that take longer then this to complete.'),
			'div' => array('class' => 'half'),
		),
		'OAuth.clientId' => array(
			'label' => __('The Client ID for this App from the Accounts App.'), 
			'type' => 'text',
			'div' => array('class' => 'third'),
		),
		'OAuth.clientSecret' => array(
			'label' => __('The Secret for this App from the Accounts App.'), 
			'type' => 'text',
			'div' => array('class' => 'third'),
		),
		'OAuth.serverURI' => array(
			'label' => __('The URI to the OAuth Server.'), 
			'type' => 'text',
			'div' => array('class' => 'third'),
		),
		
		'Cacher.sql_threshold' => array(
			'label' => 'Query process time threshold',
			'type' => 'text',
			'between' => __('The time is in seconds, it can accept integer over 0 (like: 1, 1001). This is the query time for Recacher Queries. If the actual query time is below this, it will be automatically removed from the recacher.'),
			'div' => array('class' => 'half'),
		),
		'Nslookup.internal_ips' => array(
			'label' => 'Internal Ip Address Groups',
			'type' => 'text',
			'between' => __('Seperate by a comma. Ip addresses that are considered internal. Used as regex (ex: "10." : /^10\./)'),
			'div' => array('class' => 'half'),
		),
		'Nslookup.internal_hosts' => array(
			'label' => 'Internal Hostname Groups',
			'type' => 'text',
			'between' => __('Seperate by a comma. Hostnames that are considered internal. Used as regex (ex: ".gov" : /\.gov$/)'),
			'div' => array('class' => 'half'),
		),
		'Nslookup.exemption_ips' => array(
			'label' => 'Exempt IP Address groups',
			'type' => 'text',
			'between' => __('Seperate by a comma. Ip addresses that are considered exempt from dns lookups in the cron (can still be manually looked up). Used as regex (ex: "10." : /^10\./)'),
			'div' => array('class' => 'half'),
		),
		'Nslookup.exemption_hosts' => array(
			'label' => 'Exempt Hostname Groups',
			'type' => 'text',
			'between' => __('Seperate by a comma. Hostnames that are considered exempt from dns lookups in the cron (can still be manually looked up). Used as regex (ex: ".gov" : /\.gov$/)'),
			'div' => array('class' => 'half'),
		),
		'Whoiser.email' => array(
			'label' => 'Whoiser Api Email',
			'type' => 'text',
			'between' => __('The email address used to connect to Whoiser\'s API.'),
			'div' => array('class' => 'half'),
		),
		'Whoiser.api_key' => array(
			'label' => 'Whoiser API Key',
			'type' => 'text',
			'between' => __('The API key associated with the above email address.'),
			'div' => array('class' => 'half'),
		),
	));
	
$app_config_file = dirname(__FILE__). DS. 'app_config.php';
if(file_exists($app_config_file) and is_readable($app_config_file))
{
	include_once($app_config_file);
}

// overwrite/ 
$AppConfig = Configure::read('AppConfig');
$AppConfig = Set::flatten($AppConfig);
if($AppConfig)
{
	foreach($AppConfig as $k => $v)
	{
			Configure::write($k, $v);
	}

	// overwrite existing config settings
	if(isset($AppConfig['Site.base_url']))
	{
		Configure::write('Site.base_url',$AppConfig['Site.base_url']);
	}
	
	if(isset($AppConfig['Site.title']))
	{
		Configure::write('Site.title',$AppConfig['Site.title']);
	}
	
	if(isset($AppConfig['Site.debug']))
	{
		Configure::write('debug',$AppConfig['Site.debug']);
	}
}