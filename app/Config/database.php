<?php
/**
 * This is core configuration file.
 *
 * Use it to configure core behaviour of Cake.
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
 * In this file you set up your database connection details.
 *
 * @package       cake.config
 */
/**
 * Database configuration class.
 * You can specify multiple configurations for production, development and testing.
 *
 * datasource => The name of a supported datasource; valid options are as follows:
 *		Database/Mysql 		- MySQL 4 & 5,
 *		Database/Sqlite		- SQLite (PHP5 only),
 *		Database/Postgres	- PostgreSQL 7 and higher,
 *		Database/Sqlserver	- Microsoft SQL Server 2005 and higher
 *
 * You can add custom database datasources (or override existing datasources) by adding the
 * appropriate file to app/Model/Datasource/Database.  Datasources should be named 'MyDatasource.php',
 *
 *
 * persistent => true / false
 * Determines whether or not the database should use a persistent connection
 *
 * host =>
 * the host you connect to the database. To add a socket or port number, use 'port' => #
 *
 * prefix =>
 * Uses the given prefix for all the tables in this database.  This setting can be overridden
 * on a per-table basis with the Model::$tablePrefix property.
 *
 * schema =>
 * For Postgres specifies which schema you would like to use the tables in. Postgres defaults to 'public'.
 *
 * encoding =>
 * For MySQL, Postgres specifies the character encoding to use when connecting to the
 * database. Uses database default not specified.
 *
 * unix_socket =>
 * For MySQL to connect via socket specify the `unix_socket` parameter instead of `host` and `port`
 */
class DATABASE_CONFIG {

/*
	public $default = array(
		'datasource' => 'Utilities.Database/Mysql',
		'persistent' => false,
		'host' => '',
		'login' => '',
		'password' => '',
		'database' => '',
		'prefix' => '',
		//'encoding' => 'utf8',
	);
*/

	public $default = array(
		'name' => 'default',
		'datasource' => 'Utilities.Database/MysqlExt',
		'persistent' => false,
		'host' => '',
		'login' => '',
		'password' => '',
		'database' => '',
		'prefix' => '',
		//'encoding' => 'utf8',
		'ignore_tables' => array(
			'uuids', 'db_myblocks', 
			'dns_transaction_logs', 'import_manager_logs', 'login_histories', 'whois_transaction_logs', 
			'nslookup_logs', 'vector_sources', 'whois_logs', 
			'categories_keywords', 'dumps_keywords', 'reports_keywords', 'uploads_keywords', 'keywords',  
			'temp_keywords', 'temp_categories_keywords', 'temp_reports_keywords', 'temp_uploads_keywords',
			'categories_details', 'vector_details', 'reports_details', 'dumps_details',
			'temp_reports_details', 'temp_categories_details', 
			'nslookups',
			
			//'imports', 'categories',
			//'geoips', 'vectors',
			// 'categories_editors', 'categories_signatures', 'categories_vectors', 'category_types', 
			// 'document_states', 'documents', 'dumps', 'dumps_vectors',
			// 'hash_signatures', 'hostnames', 'import_managers',
			// 'imports_vectors', 'ipaddresses', 'mains', 'nameservers', 'nslookups',
			// 'uploads', 'uploads_vectors', 'users', 'vector_types', 
			// 'whoiser_transactions', 'yara_signature_index', 'yara_signatures',
			// 'org_groups', 'report_types', 'reports', 'reports_editors', 'reports_signatures', 
			// 'reports_vectors', 'saved_filters', 'signature_sources', 'signatures', 'snort_signature_index', 
			// 'snort_signatures', 'tagged', 'tags', 'temp_categories', 'temp_categories_editors', 
			// 'temp_categories_vectors', 'temp_imports_vectors', 
			// 'temp_reports', 'temp_reports_editors', 'temp_reports_vectors', 
			// 'temp_uploads', 'temp_uploads_vectors', 'temp_vectors', 'upload_types', 
			// 'vt_detected_urls', 'vt_nt_records', 'vt_related_samples', 'whois', 'whois_nameservers', 
		),
	);

	public $backup = array(
		'name' => 'backup',
		'datasource' => 'Utilities.Database/MysqlExt',
		'persistent' => false,
		'host' => '',
		'login' => '',
		'password' => '',
		'database' => '',
		'prefix' => '',
		'snapshot' => false,
		//'encoding' => 'utf8',
	);

	public $test = array(
		'name' => 'test',
		'datasource' => 'Utilities.Database/MysqlExt',
		'persistent' => false,
		'host' => '',
		'login' => '',
		'password' => '',
		'database' => '',
		'prefix' => '',
		'snapshot' => false,
	);
	
	public function __construct() {
		if(CakePlugin::loaded('Utilities'))
		{
			loadDbConfigs($this);
		}
	}
}
