<?php 
$page_options = array();
if($category['Category']['user_id'] == AuthComponent::user('id') or $this->Common->isAdmin())
{
	$page_options['edit'] = $this->Html->link(__('Edit'), array('action' => 'edit', $category['Category']['id']));
	$page_options['delete'] = $this->Form->postLink(__('Delete'),array('action' => 'delete', $category['Category']['id']),array('confirm' => 'Are you sure?'));
}
elseif($is_editor)
{
	$page_options['edit_editor'] = $this->Html->link(__('Edit'), array('action' => 'edit_editor', $category['Category']['id']));
}
elseif($is_contributor)
{
	$page_options['edit_contributor'] = $this->Html->link(__('Edit'), array('action' => 'edit_contributor', $category['Category']['id']));
}

$details_blocks = array();
$details_blocks[1][1] = array(
	'title' => __('Details'),
	'details' => array(),
);
$details_blocks[1][1]['details'][] = array('name' => __('Owner'), 'value' => $this->Html->link($category['User']['name'], array('controller' => 'users', 'action' => 'view', $category['User']['id'])));
$details_blocks[1][1]['details'][] = array('name' => __('Org Group'), 'value' => $category['OrgGroup']['name']);
$details_blocks[1][1]['details'][] = array('name' => __('Share State'), 'value' => $this->Wrap->publicState($category['Category']['public']));
if($category['CategoryType']['org_group_id'] == AuthComponent::user('org_group_id'))
{
	$details_blocks[1][1]['details'][] = array('name' => __('Category Group'), 'value' => $this->Html->link($category['CategoryType']['name'], array('admin' => false, 'controller' => 'category_types', 'action' => 'view', $category['CategoryType']['id'])). '&nbsp;');
}
else
{
	$details_blocks[1][1]['details'][] = array('name' => __('Category Group'), 'value' => $category['CategoryType']['name']. '&nbsp;');
}
$details_blocks[1][1]['details'][] = array('name' => __('User Source'), 'value' => $category['Category']['mysource']);

$details_blocks[1][2] = array(
	'title' => __('Assessments'),
	'details' => array(),
);
$pathObject = $category;
if(isset($pathObject['AdAccount'])) unset($pathObject['AdAccount']);
$details_blocks[1][2]['details'][] = array('name' => __('SAC'), 'value' => $this->Contacts->makePath($pathObject));
$details_blocks[1][2]['details'][] = array('name' => __('NIH Risk'), 'value' => $this->Html->link($category['AssessmentNihRisk']['name'], array('controller' => 'assessment_nih_risks', 'action' => 'view', $category['AssessmentNihRisk']['id'])));
$details_blocks[1][2]['details'][] = array('name' => __('Customer Risk'), 'value' => $this->Html->link($category['AssessmentCustRisk']['name'], array('controller' => 'assessment_cust_risks', 'action' => 'view', $category['AssessmentCustRisk']['id'])));
$details_blocks[1][2]['details'][] = array('name' => __('Targeted/APT'), 'value' => $this->Wrap->yesNoUnknown($category['Category']['targeted']));
$details_blocks[1][2]['details'][] = array('name' => __('Compromised Date'), 'value' => $this->Wrap->niceDay($category['Category']['compromise_date']));

$details_blocks[1][3] = array(
	'title' => __('Victim'),
	'details' => array(),
);
$details_blocks[1][3]['details'][] = array('name' => __('AD Account'), 'value' => $this->Contacts->linkAdAccount($category));
$details_blocks[1][3]['details'][] = array('name' => __('IP Address'), 'value' => $category['Category']['victim_ip']);
$details_blocks[1][3]['details'][] = array('name' => __('MAC Address'), 'value' => $category['Category']['victim_mac']);
$details_blocks[1][3]['details'][] = array('name' => __('Asset Tag'), 'value' => $category['Category']['victim_asset_tag']);

$details_blocks[1][4] = array(
	'title' => __('Dates'),
	'details' => array(),
);
$details_blocks[1][4]['details'][] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($category['Category']['created']));
$details_blocks[1][4]['details'][] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($category['Category']['modified']));
$details_blocks[1][4]['details'][] = array('name' => __('Reviewed'), 'value' => $this->Wrap->niceTime($category['Category']['reviewed']));

$stats = array();
$tabs = array();

if($category['Category']['user_id'] == AuthComponent::user('id') or $this->Common->isAdmin())
{
	$stats['all_vectors'] = array(
		'id' => 'all_vectors',
		'name' => __('All Vectors'), 
		'ajax_url' => array('controller' => 'categories_vectors', 'action' => 'category', $category['Category']['id']),
	);
	$tabs['all_vectors'] = array(
		'id' => 'all_vectors',
		'name' => __('All Vectors'),
		'ajax_url' => array('controller' => 'categories_vectors', 'action' => 'category', $category['Category']['id']),
	);
}

$stats['active_vectors'] = array(
	'id' => 'active_vectors',
	'name' => __('Active Vectors'), 
	'ajax_url' => array('controller' => 'categories_vectors', 'action' => 'category', $category['Category']['id'], 1),
);
$tabs['active_vectors'] = array(
	'id' => 'active_vectors',
	'name' => __('Active Vectors'),
	'ajax_url' => array('controller' => 'categories_vectors', 'action' => 'category', $category['Category']['id'], 1),
);

if($category['Category']['user_id'] == AuthComponent::user('id') or $this->Common->isAdmin())
{
	$stats['inactive_vectors'] = array(
		'id' => 'inactive_vectors',
		'name' => __('Inactive Vectors'), 
		'ajax_url' => array('controller' => 'categories_vectors', 'action' => 'category', $category['Category']['id'], 0),
	);
	$tabs['inactive_vectors'] = array(
		'id' => 'inactive_vectors',
		'name' => __('Inactive Vectors'),
		'ajax_url' => array('controller' => 'categories_vectors', 'action' => 'category', $category['Category']['id'], 0),
	);
}

$stats['vectors_unique'] = array(
	'id' => 'vectors_unique',
	'name' => __('Unique Vectors'), 
	'ajax_url' => array('controller' => 'categories_vectors', 'action' => 'unique', $category['Category']['id']),
);
$tabs['vectors_unique'] = array(
	'id' => 'vectors_unique',
	'name' => __('Unique Vectors'),
	'ajax_url' => array('controller' => 'categories_vectors', 'action' => 'unique', $category['Category']['id']),
);
	
$stats['categories'] = array(
	'id' => 'categories',
	'name' => __('Related Categories'), 
	'ajax_url' => array('controller' => 'categories', 'action' => 'category', $category['Category']['id']),
);
$tabs['categories'] = array(
	'id' => 'categories',
	'name' => __('Related Categories'),
	'ajax_url' => array('controller' => 'categories', 'action' => 'category', $category['Category']['id']),
);
	
$stats['categories_vectors'] = array(
	'id' => 'categories_vectors',
	'name' => __('Related Category Vectors'), 
	'ajax_url' => array('controller' => 'categories_vectors', 'action' => 'category_related', $category['Category']['id']),
);
$tabs['categories_vectors'] = array(
	'id' => 'categories_vectors',
	'name' => __('Related Category Vectors'),
	'ajax_url' => array('controller' => 'categories_vectors', 'action' => 'category_related', $category['Category']['id']),
);

$stats['reports'] = array(
	'id' => 'reports',
	'name' => __('Related Reports'), 
	'ajax_url' => array('controller' => 'reports', 'action' => 'category', $category['Category']['id']),
);
$tabs['reports'] = array(
	'id' => 'reports',
	'name' => __('Related Reports'),
	'ajax_url' => array('controller' => 'reports', 'action' => 'category', $category['Category']['id']),
);

$stats['reports_vectors'] = array(
	'id' => 'reports_vectors',
	'name' => __('Related Report Vectors'),  
	'ajax_url' => array('controller' => 'reports_vectors', 'action' => 'category_related', $category['Category']['id']),
);
$tabs['reports_vectors'] = array(
	'id' => 'reports_vectors',
	'name' => __('Related Report Vectors'),
	'ajax_url' => array('controller' => 'reports_vectors', 'action' => 'category_related', $category['Category']['id']),
);
//////////////////

$stats['imports'] = array(
	'id' => 'imports',
	'name' => __('Related Imports'), 
	'ajax_url' => array('controller' => 'imports', 'action' => 'category', $category['Category']['id']),
);
$tabs['imports'] = array(
	'id' => 'imports',
	'name' => __('Related Imports'),
	'ajax_url' => array('controller' => 'imports', 'action' => 'category', $category['Category']['id']),
);

$stats['imports_vectors'] = array(
	'id' => 'imports_vectors',
	'name' => __('Related Import Vectors'), 
	'ajax_url' => array('controller' => 'imports_vectors', 'action' => 'category_related', $category['Category']['id']), 
);
$tabs['imports_vectors'] = array(
	'id' => 'imports_vectors',
	'name' => __('Related Import Vectors'),
	'ajax_url' => array('controller' => 'imports_vectors', 'action' => 'category_related', $category['Category']['id']),
);
$tabs['fisma_systems'] = $stats['fisma_systems'] = array(
	'id' => 'fisma_systems',
	'name' => __('FISMA Systems'), 
	'ajax_url' => array('controller' => 'fisma_systems', 'action' => 'category', $category['Category']['id']),
);
$tabs['fisma_inventories'] = $stats['fisma_inventories'] = array(
	'id' => 'fisma_inventories',
	'name' => __('FISMA Inventories'), 
	'ajax_url' => array('controller' => 'fisma_inventories', 'action' => 'category', $category['Category']['id']),
);

$stats['dns_records'] = array(
	'id' => 'dns_records',
	'name' => __('DNS Records'), 
	'ajax_url' => array('controller' => 'nslookups', 'action' => 'category', $category['Category']['id'], 'admin' => false),
);
$tabs['dns_records'] = array(
	'id' => 'dns_records',
	'name' => __('DNS Records'),
	'ajax_url' => array('controller' => 'nslookups', 'action' => 'category', $category['Category']['id'], 'admin' => false),
);

if($category['Category']['user_id'] == AuthComponent::user('id') or $this->Common->isAdmin())
{
	$stats['files'] = array(
		'id' => 'files',
		'name' => __('Attached Files'), 
		'ajax_url' => array('controller' => 'uploads', 'action' => 'category', $category['Category']['id']),
	);
	$tabs['files'] = array(
		'id' => 'files',
		'name' => __('Attached Files'),
		'ajax_url' => array('controller' => 'uploads', 'action' => 'category', $category['Category']['id']),
	);
	$stats['temp_files'] = array(
		'id' => 'temp_files',
		'name' => __('Temp Files'), 
		'ajax_url' => array('controller' => 'temp_uploads', 'action' => 'category', $category['Category']['id'], 'admin' => false),
	);
	$tabs['temp_files'] = array(
		'id' => 'temp_files',
		'name' => __('Temp Files'),
		'ajax_url' => array('controller' => 'temp_uploads', 'action' => 'category', $category['Category']['id'], 'admin' => false),
	);
	$stats['editors'] = array(
		'id' => 'editors',
		'name' => __('Editors'), 
		'ajax_url' => array('controller' => 'categories_editors', 'action' => 'category', $category['Category']['id'], 'admin' => false),
	);
	$tabs['editors'] = array(
		'id' => 'editors',
		'name' => __('Editors'),
		'ajax_url' => array('controller' => 'categories_editors', 'action' => 'category', $category['Category']['id'], 'admin' => false),
	);
	$stats['signatures'] = array(
		'id' => 'signatures',
		'name' => __('Signatures'), 
		'ajax_url' => array('controller' => 'categories_signatures', 'action' => 'category', $category['Category']['id'], 'admin' => false),
	);
	$tabs['signatures'] = array(
		'id' => 'signatures',
		'name' => __('Signatures'),
		'ajax_url' => array('controller' => 'categories_signatures', 'action' => 'category', $category['Category']['id'], 'admin' => false),
	);
	$stats['tags'] = array(
		'id' => 'tags',
		'name' => __('Tags'), 
		'ajax_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'category', $category['Category']['id']),
	);
	$tabs['tags'] = array(
		'id' => 'tags',
		'name' => __('Tags'),
		'ajax_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'category', $category['Category']['id']),
	);
}
else
{
	$stats['files'] = array(
		'id' => 'files',
		'name' => __('Attached Files'), 
		'ajax_url' => array('controller' => 'uploads', 'action' => 'category', $category['Category']['id']),
	);
	$tabs['files'] = array(
		'id' => 'files',
		'name' => __('Attached Files'),
		'ajax_url' => array('controller' => 'uploads', 'action' => 'category', $category['Category']['id']),
	);
	if($is_editor or $is_contributor or $this->Common->isAdmin())
	{
		$stats['temp_files'] = array(
			'id' => 'temp_files',
			'name' => __('Temp Files'), 
			'ajax_url' => array('controller' => 'temp_uploads', 'action' => 'category', $category['Category']['id']),
					);
		$tabs['temp_files'] = array(
			'id' => 'temp_files',
			'name' => __('Temp Files'),
			'ajax_url' => array('controller' => 'temp_uploads', 'action' => 'category', $category['Category']['id']),
		);
		$stats['signatures'] = array(
			'id' => 'signatures',
			'name' => __('Signatures'), 
			'ajax_url' => array('controller' => 'categories_signatures', 'action' => 'category', $category['Category']['id']),
					);
		$tabs['signatures'] = array(
			'id' => 'signatures',
			'name' => __('Signatures'),
			'ajax_url' => array('controller' => 'categories_signatures', 'action' => 'category', $category['Category']['id']),
		);
		$stats['tags'] = array(
			'id' => 'tags',
			'name' => __('Tags'), 
			'ajax_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'category', $category['Category']['id']),
					);
		$tabs['tags'] = array(
			'id' => 'tags',
			'name' => __('Tags'),
			'ajax_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'category', $category['Category']['id']),
		);
	}
	else
	{
		$stats['signatures'] = array(
			'id' => 'signatures',
			'name' => __('Signatures'), 
			'ajax_url' => array('controller' => 'categories_signatures', 'action' => 'category', $category['Category']['id']),
					);
		$tabs['signatures'] = array(
			'id' => 'signatures',
			'name' => __('Signatures'),
			'ajax_url' => array('controller' => 'categories_signatures', 'action' => 'category', $category['Category']['id']),
		);
		$stats['tags'] = array(
			'id' => 'tags',
			'name' => __('Tags'), 
			'ajax_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'category', $category['Category']['id']),
					);
		$tabs['tags'] = array(
			'id' => 'tags',
			'name' => __('Tags'),
			'ajax_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'category', $category['Category']['id']),
		);
	}
}

$tabs['description'] = array(
	'id' => 'description',
	'name' => __('Description'),
	'content' => $this->Wrap->descView($category['CategoriesDetail']['desc']),
);

if($category['Category']['user_id'] == AuthComponent::user('id'))
{
	$tabs['notes'] = array(
		'id' => 'notes',
		'name' => __('Private Notes'),
		'content' => $this->Wrap->descView($category['CategoriesDetail']['desc_private']),
	);
}

echo $this->element('Utilities.page_view_columns', array(
	'page_title' => __('%s: %s', __('Category'), $category['Category']['name']),
	'page_options' => $page_options,
	'details_blocks' => $details_blocks,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));