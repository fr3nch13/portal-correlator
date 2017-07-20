<?php 
class AppSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $assessment_cust_risks = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'slug' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'color_code_hex' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'show' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'show' => array('column' => 'show', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $assessment_nih_risks = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'slug' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'color_code_hex' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'show' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'show' => array('column' => 'show', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $assessment_offices = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'slug' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'color_code_hex' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'show' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'show' => array('column' => 'show', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $assessment_organizations = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'slug' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'color_code_hex' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'show' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'show' => array('column' => 'show', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $categories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'editor_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'contributor_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'category_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'org_group_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assoc_account_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'ad_account_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'sac_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'fisma_system_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'public' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'key' => 'index'),
		'mysource' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'assessment_organization_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assessment_office_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assessment_nih_risk_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assessment_cust_risk_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'targeted' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 1, 'unsigned' => false),
		'compromise_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'victim_ip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'victim_mac' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'victim_asset_tag' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'reviewed' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'public' => array('column' => 'public', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'category_type_id' => array('column' => 'category_type_id', 'unique' => 0),
			'org_group_id' => array('column' => 'org_group_id', 'unique' => 0),
			'mysource' => array('column' => 'mysource', 'unique' => 0),
			'editor_user_id' => array('column' => 'editor_user_id', 'unique' => 0),
			'contributor_user_id' => array('column' => 'contributor_user_id', 'unique' => 0),
			'assessment_organization_id' => array('column' => 'assessment_organization_id', 'unique' => 0),
			'assessment_office_id' => array('column' => 'assessment_office_id', 'unique' => 0),
			'assessment_nih_risk_id' => array('column' => 'assessment_nih_risk_id', 'unique' => 0),
			'assessment_cust_risk_id' => array('column' => 'assessment_cust_risk_id', 'unique' => 0),
			'ad_account_id' => array('column' => 'ad_account_id', 'unique' => 0),
			'sac_id' => array('column' => 'sac_id', 'unique' => 0),
			'assoc_account_id' => array('column' => 'assoc_account_id', 'unique' => 0),
			'victim_ip' => array('column' => 'victim_ip', 'unique' => 0),
			'victim_mac' => array('column' => 'victim_mac', 'unique' => 0),
			'victim_asset_tag' => array('column' => 'victim_asset_tag', 'unique' => 0),
			'fisma_system_id' => array('column' => 'fisma_system_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $categories_details = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'category_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'desc' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc_private' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'category_id' => array('column' => 'category_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $categories_editors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'category_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'type' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'category_id' => array('column' => 'category_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'type' => array('column' => 'type', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $categories_signatures = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'category_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'temp_category_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'signature_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'signature_source_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'category_id' => array('column' => 'category_id', 'unique' => 0),
			'temp_category_id' => array('column' => 'temp_category_id', 'unique' => 0),
			'signature_id' => array('column' => 'signature_id', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'signature_source_id' => array('column' => 'signature_source_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $categories_vectors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'category_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'vector_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'category_id' => array('column' => 'category_id', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'vector_type_id' => array('column' => 'vector_type_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $category_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'holder' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'org_group_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'holder' => array('column' => 'holder', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'org_group_id' => array('column' => 'org_group_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $combined_view_categories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'combined_view_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'category_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'combined_view_id' => array('column' => 'combined_view_id', 'unique' => 0),
			'category_id' => array('column' => 'category_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $combined_view_reports = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'combined_view_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'report_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'combined_view_id' => array('column' => 'combined_view_id', 'unique' => 0),
			'report_id' => array('column' => 'report_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $combined_views = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $dns_transaction_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'result_count' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => false),
		'sources' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'error_raw' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'error_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'automatic' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'vector_id' => array('column' => 'vector_id', 'unique' => 0),
			'automatic' => array('column' => 'automatic', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $dumps = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'filename' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 30, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'mimetype' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'size' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'md5' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $dumps_details = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'dump_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'desc' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'dumptext' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'allvectors' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'newvectors' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'dump_id' => array('column' => 'dump_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $dumps_vectors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'dump_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'vector_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'dump_id' => array('column' => 'dump_id', 'unique' => 0),
			'vector_id' => array('column' => 'vector_id', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'vector_type_id' => array('column' => 'vector_type_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $geoips = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'country_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'country_iso' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 5, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'region_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'region_iso' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 5, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'city' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'postal_code' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'latitude' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'longitude' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'vector_id' => array('column' => 'vector_id', 'unique' => 0),
			'country_name' => array('column' => 'country_name', 'unique' => 0),
			'country_iso' => array('column' => 'country_iso', 'unique' => 0),
			'region_name' => array('column' => 'region_name', 'unique' => 0),
			'region_iso' => array('column' => 'region_iso', 'unique' => 0),
			'city' => array('column' => 'city', 'unique' => 0),
			'postal_code' => array('column' => 'postal_code', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $hash_signatures = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'vector_id' => array('column' => 'vector_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $hostnames = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_checked' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_checked_dnsdbapi' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_updated_dnsdbapi' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_checked_virustotal' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_updated_virustotal' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_checked_passivetotal' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_updated_passivetotal' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_checked_hexillion' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_updated_hexillion' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_level' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'key' => 'index'),
		'auto_lookup_virustotal' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1, 'unsigned' => false, 'key' => 'index'),
		'dns_auto_lookup' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1, 'unsigned' => false, 'key' => 'index'),
		'dns_auto_lookup_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'hexillion_auto_lookup' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1, 'unsigned' => false, 'key' => 'index'),
		'hexillion_auto_lookup_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'tld' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'whois_checked' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'whois_updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'whois_level' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
		'whois_auto_lookup' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1, 'unsigned' => false, 'key' => 'index'),
		'whois_auto_lookup_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'ttl' => array('type' => 'integer', 'null' => true, 'default' => '300', 'length' => 20, 'unsigned' => false),
		'ttl_dynamic' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'ttl_manual' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'source' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'subsource' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'subsource2' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'last_hash' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 40, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'vector_id' => array('column' => 'vector_id', 'unique' => 0),
			'source' => array('column' => 'source', 'unique' => 0),
			'subsource' => array('column' => 'subsource', 'unique' => 0),
			'dns_level' => array('column' => 'dns_level', 'unique' => 0),
			'dns_auto_lookup' => array('column' => 'dns_auto_lookup', 'unique' => 0),
			'whois_auto_lookup' => array('column' => 'whois_auto_lookup', 'unique' => 0),
			'auto_lookup_virustotal' => array('column' => 'auto_lookup_virustotal', 'unique' => 0),
			'dns_auto_lookup_user_id' => array('column' => 'dns_auto_lookup_user_id', 'unique' => 0),
			'whois_auto_lookup_user_id' => array('column' => 'whois_auto_lookup_user_id', 'unique' => 0),
			'hexillion_auto_lookup' => array('column' => 'hexillion_auto_lookup', 'unique' => 0),
			'hexillion_auto_lookup_user_id' => array('column' => 'hexillion_auto_lookup_user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $import_manager_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'import_manager_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'success' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'num_added' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'num_empty' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'num_duplicate' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'num_failed' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'msg' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'starttime' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'endtime' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'import_manager_id' => array('column' => 'import_manager_id', 'unique' => 0),
			'success' => array('column' => 'success', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $import_managers = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'key' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'parser' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'public' => array('type' => 'integer', 'null' => true, 'default' => '2', 'unsigned' => false),
		'csv_fields' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'added_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'org_group_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'cron' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'url' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'local_path' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'auto_reviewed' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'location' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'last_cron' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'added_user_id' => array('column' => 'added_user_id', 'unique' => 0),
			'org_group_id' => array('column' => 'org_group_id', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'auto_reviewed' => array('column' => 'auto_reviewed', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $imports = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'import_manager_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'sha1' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 80, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'filename' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 30, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'mimetype' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'size' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'org_group_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'public' => array('type' => 'integer', 'null' => true, 'default' => '2', 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'reviewed' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'public' => array('column' => 'public', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'org_group_id' => array('column' => 'org_group_id', 'unique' => 0),
			'import_manager_id' => array('column' => 'import_manager_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $imports_vectors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'import_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'vector_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'import_id' => array('column' => 'import_id', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'vector_type_id' => array('column' => 'vector_type_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $ipaddresses = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_checked' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_checked_dnsdbapi' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_updated_dnsdbapi' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_checked_virustotal' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_updated_virustotal' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_checked_passivetotal' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_updated_passivetotal' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_checked_hexillion' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_updated_hexillion' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dns_level' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'key' => 'index'),
		'auto_lookup_virustotal' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1, 'unsigned' => false, 'key' => 'index'),
		'dns_auto_lookup' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1, 'unsigned' => false, 'key' => 'index'),
		'dns_auto_lookup_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'tld' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'whois_checked' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'whois_updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'whois_level' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
		'whois_auto_lookup' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1, 'unsigned' => false, 'key' => 'index'),
		'whois_auto_lookup_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'hexillion_auto_lookup' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1, 'unsigned' => false, 'key' => 'index'),
		'hexillion_auto_lookup_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'geoip_checked' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'geoip_updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'ttl' => array('type' => 'integer', 'null' => true, 'default' => '300', 'length' => 20, 'unsigned' => false),
		'ttl_dynamic' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'ttl_manual' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'source' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'subsource' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'subsource2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'vector_id' => array('column' => 'vector_id', 'unique' => 0),
			'source' => array('column' => 'source', 'unique' => 0),
			'subsource' => array('column' => 'subsource', 'unique' => 0),
			'dns_level' => array('column' => 'dns_level', 'unique' => 0),
			'dns_auto_lookup' => array('column' => 'dns_auto_lookup', 'unique' => 0),
			'whois_auto_lookup' => array('column' => 'whois_auto_lookup', 'unique' => 0),
			'auto_lookup_virustotal' => array('column' => 'auto_lookup_virustotal', 'unique' => 0),
			'dns_auto_lookup_user_id' => array('column' => 'dns_auto_lookup_user_id', 'unique' => 0),
			'whois_auto_lookup_user_id' => array('column' => 'whois_auto_lookup_user_id', 'unique' => 0),
			'hexillion_auto_lookup' => array('column' => 'hexillion_auto_lookup', 'unique' => 0),
			'hexillion_auto_lookup_user_id' => array('column' => 'hexillion_auto_lookup_user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $login_histories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => false, 'key' => 'index'),
		'email' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ipaddress' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_agent' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'success' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'timestamp' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'ipaddress' => array('column' => 'ipaddress', 'unique' => 0),
			'success' => array('column' => 'success', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $mains = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $nameservers = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'nameserver' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $nslookup_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'nslookup_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'vector_hostname_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'vector_ipaddress_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'ttl' => array('type' => 'integer', 'null' => true, 'default' => '300', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'ttl_dynamic' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'ttl_manual' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'source' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'first_seen' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'last_seen' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'source' => array('column' => 'source', 'unique' => 0),
			'vector_hostname_id' => array('column' => 'vector_hostname_id', 'unique' => 0),
			'vector_ipaddress_id' => array('column' => 'vector_ipaddress_id', 'unique' => 0),
			'ttl' => array('column' => 'ttl', 'unique' => 0),
			'nslookup_id' => array('column' => 'nslookup_id', 'unique' => 0),
			'ttl_dynamic' => array('column' => 'ttl', 'unique' => 0),
			'ttl_manual' => array('column' => 'ttl', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $nslookups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'vector_hostname_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'vector_ipaddress_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'ttl' => array('type' => 'integer', 'null' => true, 'default' => '300', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'ttl_dynamic' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'ttl_manual' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'source' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'subsource' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'first_seen' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'last_seen' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'source' => array('column' => 'source', 'unique' => 0),
			'vector_ipaddress_id' => array('column' => 'vector_ipaddress_id', 'unique' => 0),
			'ttl' => array('column' => 'ttl', 'unique' => 0),
			'subsource' => array('column' => 'subsource', 'unique' => 0),
			'vector_hostname_id' => array('column' => 'vector_hostname_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $org_groups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $report_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'holder' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'org_group_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'holder' => array('column' => 'holder', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'org_group_id' => array('column' => 'org_group_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $reports = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'editor_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'contributor_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'org_group_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assoc_account_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'ad_account_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'sac_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'fisma_system_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'report_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'public' => array('type' => 'integer', 'null' => true, 'default' => '1', 'unsigned' => false, 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'mysource' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'assessment_organization_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assessment_office_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assessment_nih_risk_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assessment_cust_risk_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'targeted' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 1, 'unsigned' => false),
		'compromise_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'victim_ip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'victim_mac' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'victim_asset_tag' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'reviewed' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'public' => array('column' => 'public', 'unique' => 0),
			'report_type_id' => array('column' => 'report_type_id', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'org_group_id' => array('column' => 'org_group_id', 'unique' => 0),
			'mysource' => array('column' => 'mysource', 'unique' => 0),
			'editor_user_id' => array('column' => 'editor_user_id', 'unique' => 0),
			'contributor_user_id' => array('column' => 'contributor_user_id', 'unique' => 0),
			'assessment_organization_id' => array('column' => 'assessment_organization_id', 'unique' => 0),
			'assessment_office_id' => array('column' => 'assessment_office_id', 'unique' => 0),
			'assessment_nih_risk_id' => array('column' => 'assessment_nih_risk_id', 'unique' => 0),
			'assessment_cust_risk_id' => array('column' => 'assessment_cust_risk_id', 'unique' => 0),
			'ad_account_id' => array('column' => 'ad_account_id', 'unique' => 0),
			'sac_id' => array('column' => 'sac_id', 'unique' => 0),
			'assoc_account_id' => array('column' => 'assoc_account_id', 'unique' => 0),
			'victim_ip' => array('column' => 'victim_ip', 'unique' => 0),
			'victim_mac' => array('column' => 'victim_mac', 'unique' => 0),
			'victim_asset_tag' => array('column' => 'victim_asset_tag', 'unique' => 0),
			'fisma_system_id' => array('column' => 'fisma_system_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $reports_details = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'report_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'desc' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc_private' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'report_id' => array('column' => 'report_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $reports_editors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'report_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'type' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'report_id' => array('column' => 'report_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'type' => array('column' => 'type', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $reports_signatures = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'report_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'temp_report_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'signature_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'signature_source_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'report_id' => array('column' => 'report_id', 'unique' => 0),
			'temp_report_id' => array('column' => 'temp_report_id', 'unique' => 0),
			'signature_id' => array('column' => 'signature_id', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'signature_source_id' => array('column' => 'signature_source_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $reports_vectors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'report_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'vector_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'report_id' => array('column' => 'report_id', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'vector_type_id' => array('column' => 'vector_type_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $signature_sources = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'slug' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'slug' => array('column' => 'slug', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $signatures = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'hash' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'signature' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'added_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'updated_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'signature_type' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'signature_source_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'active' => array('column' => 'active', 'unique' => 0),
			'signature_type' => array('column' => 'signature_type', 'unique' => 0),
			'signature_source_id' => array('column' => 'signature_source_id', 'unique' => 0),
			'added_user_id' => array('column' => 'added_user_id', 'unique' => 0),
			'updated_user_id' => array('column' => 'updated_user_id', 'unique' => 0),
			'hash' => array('column' => 'hash', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $snort_signature_index = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'snort_signature_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'key' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'snort_signature_id' => array('column' => 'snort_signature_id', 'unique' => 0),
			'key' => array('column' => 'key', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $snort_signatures = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'signature_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'signature_source_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'hash' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'action' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'protocol' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'src_ip' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'src_port' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'direction' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'dest_ip' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'dest_port' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'raw' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'added_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'updated_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'active' => array('column' => 'active', 'unique' => 0),
			'signature_id' => array('column' => 'signature_id', 'unique' => 0),
			'hash' => array('column' => 'hash', 'unique' => 0),
			'action' => array('column' => 'action', 'unique' => 0),
			'protocol' => array('column' => 'protocol', 'unique' => 0),
			'src_ip' => array('column' => 'src_ip', 'unique' => 0),
			'src_port' => array('column' => 'src_port', 'unique' => 0),
			'direction' => array('column' => 'direction', 'unique' => 0),
			'dest_ip' => array('column' => 'dest_ip', 'unique' => 0),
			'dest_port' => array('column' => 'dest_port', 'unique' => 0),
			'added_user_id' => array('column' => 'added_user_id', 'unique' => 0),
			'updated_user_id' => array('column' => 'updated_user_id', 'unique' => 0),
			'signature_source_id' => array('column' => 'signature_source_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $temp_categories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'category_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'org_group_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assoc_account_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'ad_account_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'sac_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'public' => array('type' => 'integer', 'null' => true, 'default' => '1', 'unsigned' => false, 'key' => 'index'),
		'mysource' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'assessment_organization_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assessment_office_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assessment_nih_risk_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assessment_cust_risk_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'targeted' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 1, 'unsigned' => false),
		'compromise_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'victim_ip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'victim_mac' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'victim_asset_tag' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'public' => array('column' => 'public', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'category_type_id' => array('column' => 'category_type_id', 'unique' => 0),
			'org_group_id' => array('column' => 'org_group_id', 'unique' => 0),
			'mysource' => array('column' => 'mysource', 'unique' => 0),
			'assessment_organization_id' => array('column' => 'assessment_organization_id', 'unique' => 0),
			'assessment_office_id' => array('column' => 'assessment_office_id', 'unique' => 0),
			'assessment_nih_risk_id' => array('column' => 'assessment_nih_risk_id', 'unique' => 0),
			'assessment_cust_risk_id' => array('column' => 'assessment_cust_risk_id', 'unique' => 0),
			'ad_account_id' => array('column' => 'ad_account_id', 'unique' => 0),
			'sac_id' => array('column' => 'sac_id', 'unique' => 0),
			'assoc_account_id' => array('column' => 'assoc_account_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $temp_categories_details = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'temp_category_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'desc' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc_private' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'temp_category_id' => array('column' => 'temp_category_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $temp_categories_editors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'temp_category_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'type' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'temp_category_id' => array('column' => 'temp_category_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'type' => array('column' => 'type', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $temp_categories_vectors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'temp_category_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'temp_vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'vector_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'temp_category_id' => array('column' => 'temp_category_id', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'vector_type_id' => array('column' => 'vector_type_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $temp_imports_vectors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'import_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'temp_vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'vector_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'import_id' => array('column' => 'import_id', 'unique' => 0),
			'temp_vector_id' => array('column' => 'temp_vector_id', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'vector_type_id' => array('column' => 'vector_type_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $temp_reports = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'org_group_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'report_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assoc_account_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'ad_account_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'sac_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'public' => array('type' => 'integer', 'null' => true, 'default' => '1', 'unsigned' => false, 'key' => 'index'),
		'mysource' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'assessment_organization_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assessment_office_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assessment_nih_risk_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'assessment_cust_risk_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'targeted' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 1, 'unsigned' => false),
		'compromise_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'victim_ip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'victim_mac' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'victim_asset_tag' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'public' => array('column' => 'public', 'unique' => 0),
			'report_type_id' => array('column' => 'report_type_id', 'unique' => 0),
			'org_group_id' => array('column' => 'org_group_id', 'unique' => 0),
			'mysource' => array('column' => 'mysource', 'unique' => 0),
			'assessment_organization_id' => array('column' => 'assessment_organization_id', 'unique' => 0),
			'assessment_office_id' => array('column' => 'assessment_office_id', 'unique' => 0),
			'assessment_nih_risk_id' => array('column' => 'assessment_nih_risk_id', 'unique' => 0),
			'assessment_cust_risk_id' => array('column' => 'assessment_cust_risk_id', 'unique' => 0),
			'ad_account_id' => array('column' => 'ad_account_id', 'unique' => 0),
			'sac_id' => array('column' => 'sac_id', 'unique' => 0),
			'assoc_account_id' => array('column' => 'assoc_account_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $temp_reports_details = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'temp_report_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'desc' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc_private' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'temp_report_id' => array('column' => 'temp_report_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $temp_reports_editors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'temp_report_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'type' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'temp_report_id' => array('column' => 'temp_report_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'type' => array('column' => 'type', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $temp_reports_vectors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'temp_report_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'temp_vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'vector_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'temp_report_id' => array('column' => 'temp_report_id', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'vector_type_id' => array('column' => 'vector_type_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $temp_uploads = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'filename' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 30, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'mimetype' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'size' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'md5' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'added_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'org_group_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'temp_category_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'temp_report_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'category_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'report_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'upload_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'public' => array('type' => 'integer', 'null' => true, 'default' => '1', 'unsigned' => false, 'key' => 'index'),
		'mysource' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc_private' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'public' => array('column' => 'public', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'temp_category_id' => array('column' => 'temp_category_id', 'unique' => 0),
			'temp_report_id' => array('column' => 'temp_report_id', 'unique' => 0),
			'category_id' => array('column' => 'category_id', 'unique' => 0),
			'report_id' => array('column' => 'report_id', 'unique' => 0),
			'upload_type_id' => array('column' => 'upload_type_id', 'unique' => 0),
			'org_group_id' => array('column' => 'org_group_id', 'unique' => 0),
			'mysource' => array('column' => 'mysource', 'unique' => 0),
			'added_user_id' => array('column' => 'added_user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $temp_uploads_vectors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'temp_upload_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'temp_vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'vector_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'temp_upload_id' => array('column' => 'temp_upload_id', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'vector_type_id' => array('column' => 'vector_type_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $temp_vectors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'temp_vector' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'bad' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'vector_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'user_vtype_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'whois_auto_lookup' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
		'dns_auto_lookup' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
		'hexillion_auto_lookup' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'bad' => array('column' => 'bad', 'unique' => 0),
			'vector_type_id' => array('column' => 'vector_type_id', 'unique' => 0),
			'type' => array('column' => 'type', 'unique' => 0),
			'user_vtype_id' => array('column' => 'vector_type_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $upload_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'holder' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'org_group_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'holder' => array('column' => 'holder', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'org_group_id' => array('column' => 'org_group_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $uploads = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'filename' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 30, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'mimetype' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'size' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'md5' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'added_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'org_group_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'category_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'report_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'upload_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'public' => array('type' => 'integer', 'null' => true, 'default' => '1', 'unsigned' => false, 'key' => 'index'),
		'mysource' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc_private' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'reviewed' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'public' => array('column' => 'public', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'category_id' => array('column' => 'category_id', 'unique' => 0),
			'report_id' => array('column' => 'report_id', 'unique' => 0),
			'upload_type_id' => array('column' => 'upload_type_id', 'unique' => 0),
			'org_group_id' => array('column' => 'org_group_id', 'unique' => 0),
			'mysource' => array('column' => 'mysource', 'unique' => 0),
			'added_user_id' => array('column' => 'added_user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $uploads_vectors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'upload_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'vector_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'upload_id' => array('column' => 'upload_id', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'vector_type_id' => array('column' => 'vector_type_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'old_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'email' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'password' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'division_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'ad_account_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'assoc_account_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'firstname' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'lastname' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adaccount' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'userid' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'remote_user' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'role' => array('type' => 'string', 'null' => false, 'default' => 'regular', 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'admin_emails' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'paginate_items' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 5, 'unsigned' => false),
		'org_group_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'lastlogin' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'active' => array('column' => 'active', 'unique' => 0),
			'org_group_id' => array('column' => 'org_group_id', 'unique' => 0),
			'admin_emails' => array('column' => 'admin_emails', 'unique' => 0),
			'old_id' => array('column' => 'old_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $uuids = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'uuid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'model' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'model_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'deleted' => array('column' => 'deleted', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $vector_details = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'vt_lookup' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1, 'unsigned' => false, 'key' => 'index'),
		'vt_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'vt_checked' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'vt_updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'vector_id' => array('column' => 'vector_id', 'unique' => 0),
			'vt_lookup' => array('column' => 'vt_lookup', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $vector_sources = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'source_type' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'source' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'sub_source' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'first' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'last' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'count' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'source_type' => array('column' => 'source_type', 'unique' => 0),
			'source' => array('column' => 'source', 'unique' => 0),
			'first' => array('column' => 'first', 'unique' => 0),
			'last' => array('column' => 'last', 'unique' => 0),
			'vector_id' => array('column' => 'vector_id', 'unique' => 0),
			'vector_id_first' => array('column' => array('vector_id', 'first'), 'unique' => 0),
			'vector_id_last' => array('column' => array('vector_id', 'last'), 'unique' => 0),
			'count' => array('column' => 'count', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $vector_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'holder' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'bad' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'holder' => array('column' => 'holder', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $vectors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'vector' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'bad' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'reviewed' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'vector_type_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'user_vtype_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'manual_type_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'bad' => array('column' => 'bad', 'unique' => 0),
			'type' => array('column' => 'type', 'unique' => 0),
			'vector_type_id' => array('column' => 'vector_type_id', 'unique' => 0),
			'user_vtype_id' => array('column' => 'user_vtype_id', 'unique' => 0),
			'bad_type' => array('column' => array('bad', 'type'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $vt_detected_urls = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'vector_lookup_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'vector_url_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'total' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => false),
		'positives' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => false),
		'scan_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'first_seen' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'last_seen' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'vector_lookup_id' => array('column' => 'vector_lookup_id', 'unique' => 0),
			'vector_url_id' => array('column' => 'vector_url_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $vt_nt_records = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'vector_lookup_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'vector_src_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'vector_dst_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'src_port' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => false, 'key' => 'index'),
		'dst_port' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => false, 'key' => 'index'),
		'protocol' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'first_seen' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'last_seen' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'vector_lookup_id' => array('column' => 'vector_lookup_id', 'unique' => 0),
			'vector_src_id' => array('column' => 'vector_src_id', 'unique' => 0),
			'vector_dst_id' => array('column' => 'vector_dst_id', 'unique' => 0),
			'src_port' => array('column' => 'src_port', 'unique' => 0),
			'dst_port' => array('column' => 'dst_port', 'unique' => 0),
			'protocol' => array('column' => 'protocol', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $vt_related_samples = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'vector_lookup_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'vector_sample_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'total' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => false),
		'positives' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => false),
		'type' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'first_seen' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'last_seen' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'vector_lookup_id' => array('column' => 'vector_lookup_id', 'unique' => 0),
			'vector_sample_id' => array('column' => 'vector_sample_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $whois = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'source' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'whois_checked' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'whois_updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'tld' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'sha1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'recordDate' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'createdDate' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'updatedDate' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'expiresDate' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'registrarName' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'contactEmail' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrarStatus' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantName' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantOrg' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantAddress' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantCity' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantState' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantPostalCode' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantCountry' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantPhone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantFax' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantEmail' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingName' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingOrg' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingAddress' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingCity' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingState' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingPostalCode' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingCountry' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingPhone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingFax' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingEmail' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminName' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminOrg' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminAddress' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminCity' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminState' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminPostalCode' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminCountry' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminPhone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminFax' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminEmail' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techName' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techOrg' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techAddress' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techCity' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techState' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techPostalCode' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techCountry' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techPhone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techFax' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techEmail' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'vector_id' => array('column' => 'vector_id', 'unique' => 0),
			'source' => array('column' => 'source', 'unique' => 0),
			'tld' => array('column' => 'tld', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $whois_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'whois_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'source' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'tld' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'sha1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'recordDate' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'createdDate' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'updatedDate' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'expiresDate' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'registrarName' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'contactEmail' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrarStatus' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantName' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantOrg' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantAddress' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantCity' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantState' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantPostalCode' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantCountry' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantPhone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantFax' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'registrantEmail' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingName' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingOrg' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingAddress' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingCity' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingState' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingPostalCode' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingCountry' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingPhone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingFax' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'billingEmail' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminName' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminOrg' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminAddress' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminCity' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminState' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminPostalCode' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminCountry' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminPhone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminFax' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adminEmail' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techName' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techOrg' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techAddress' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techCity' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techState' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techPostalCode' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techCountry' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techPhone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techFax' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'techEmail' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'vector_id' => array('column' => 'vector_id', 'unique' => 0),
			'whois_id' => array('column' => 'vector_id', 'unique' => 0),
			'source' => array('column' => 'source', 'unique' => 0),
			'tld' => array('column' => 'tld', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $whois_nameservers = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'whois_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'nameserver_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'whois_id' => array('column' => 'whois_id', 'unique' => 0),
			'nameserver_id' => array('column' => 'nameserver_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $whois_transaction_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'result_count' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => false),
		'sources' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'error_raw' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'error_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'automatic' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'vector_id' => array('column' => 'vector_id', 'unique' => 0),
			'automatic' => array('column' => 'automatic', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $whoiser_transactions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'vector_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'whoiser_search_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => false, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => false, 'key' => 'index'),
		'status' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'last_changed' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'last_checked' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'vector_id' => array('column' => 'vector_id', 'unique' => 0),
			'whoiser_search_id' => array('column' => 'whoiser_search_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'status' => array('column' => 'status', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $yara_signature_index = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'yara_signature_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'type' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'key' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'yara_signature_id' => array('column' => 'yara_signature_id', 'unique' => 0),
			'type' => array('column' => 'type', 'unique' => 0),
			'key' => array('column' => 'key', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $yara_signatures = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'signature_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'signature_source_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'hash' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'scope' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'raw' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'raw_compiled' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'added_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'updated_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'active' => array('column' => 'active', 'unique' => 0),
			'signature_id' => array('column' => 'signature_id', 'unique' => 0),
			'hash' => array('column' => 'hash', 'unique' => 0),
			'added_user_id' => array('column' => 'added_user_id', 'unique' => 0),
			'updated_user_id' => array('column' => 'updated_user_id', 'unique' => 0),
			'signature_source_id' => array('column' => 'signature_source_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

}
