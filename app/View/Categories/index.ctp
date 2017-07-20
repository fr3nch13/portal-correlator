<?php 

$page_title = (isset($page_title)?$page_title:__('Categories'));
$page_subtitle = (isset($page_subtitle)?$page_subtitle:__('All Available'));
$page_description = (isset($page_description)?$page_description:'');
$page_options = (isset($page_options)?$page_options:array());
$use_multiselect = (isset($use_multiselect)?$use_multiselect:false);
$multiselect_options = (isset($multiselect_options)?$multiselect_options:array());
$use_gridedit = (isset($use_gridedit)?$use_gridedit:false);

// viewing a list of my categories
$mine = false;
if($this->request->param('action') == 'mine' or $this->Common->roleCheck('admin'))
{
	$mine = true;
	$use_gridedit = true;
}

$offset = false;
if(in_array($this->request->param('action'), array('vector', 'signature')))
	$offset = true;

if($mine)
{
	$page_options['add'] = $this->Html->link(__('Add %s', __('Category')), array('controller' => 'temp_categories', 'action' => 'add', 'admin' => false));
}

// content
$th = array();
$th['Category.id'] = array('content' => __('ID'), 'options' => array('sort' => 'Category.id'));
$th['Category.name'] = array('content' => __('Name'), 'options' => array('sort' => 'Category.name'));
if($mine) $th['Category.name']['options']['editable'] = array('type' => 'text');
$th['Category.category_type_id'] = array('content' => __('Category Group'), 'options' => array('sort' => 'CategoryType.name'));
if($mine) $th['Category.category_type_id']['options']['editable'] = array('type' => 'select', 'options' => $categoryTypes);
if($offset)
	$th['Category.category_type_id'] = array('content' => __('Category Group'));
$th['Category.mysource'] = array('content' => __('User Source'), 'options' => array('sort' => 'Category.mysource'));
if($mine) $th['Category.mysource']['options']['editable'] = array('type' => 'text');
$th['Category.sac_id'] = array('content' => __('SAC'), 'options' => array('sort' => 'Category.sac_id'));
if($mine) $th['Category.sac_id']['options']['editable'] = array('type' => 'select', 'options' => $sacs);
$th['Category.adaccount'] = array('content' => __('AD Account'), 'options' => array('sort' => 'Category.ad_account_id'));
if($mine) $th['Category.adaccount']['options']['editable'] = array('type' => 'autocomplete', 'rel' => array('controller' => 'ad_accounts', 'action' => 'autocomplete'));
$th['Category.victim_ip'] = array('content' => __('Victim IP'), 'options' => array('sort' => 'Category.victim_ip'));
if($mine) $th['Category.victim_ip']['options']['editable'] = array('type' => 'text');
$th['Category.victim_mac'] = array('content' => __('Victim MAC'), 'options' => array('sort' => 'Category.victim_mac'));
if($mine) $th['Category.victim_mac']['options']['editable'] = array('type' => 'text');
$th['Category.victim_asset_tag'] = array('content' => __('Victim Asset Tag'), 'options' => array('sort' => 'Category.victim_asset_tag'));
if($mine) $th['Category.victim_asset_tag']['options']['editable'] = array('type' => 'text');
$th['Category.assessment_nih_risk_id'] = array('content' => __('NIH Risk'), 'options' => array('sort' => 'AssessmentNihRisk.name'));
if($mine) $th['Category.assessment_nih_risk_id']['options']['editable'] = array('type' => 'select', 'options' => $assessmentNihRisks);
$th['Category.assessment_cust_risk_id'] = array('content' => __('Customer Risk'), 'options' => array('sort' => 'AssessmentCustRisk.name'));
if($mine) $th['Category.assessment_cust_risk_id']['options']['editable'] = array('type' => 'select', 'options' => $assessmentCustRisks);
$th['Category.targeted'] = array('content' => __('Targeted/APT'), 'options' => array('sort' => 'Category.targeted'));
if($mine) $th['Category.targeted']['options']['editable'] = array('type' => 'boolean');
$th['Category.compromise_date'] = array('content' => __('Compromised Date'), 'options' => array('sort' => 'Category.compromise_date'));
if($mine) $th['Category.compromise_date']['options']['editable'] = array('type' => 'date');
$th['Vector.count'] = array('content' => __('# %s', __('Active Vectors')), 'options' => array('class' => 'count'));
$th['Reports.related.count'] = array('content' => __('# %s', __('Related Reports')), 'options' => array('class' => 'count'));
$th['Categories.related.count'] = array('content' => __('# %s', __('Related Categories')), 'options' => array('class' => 'count'));
$th['FismaSystems.related.count'] = array('content' => __('# %s', __('FISMA Systems')), 'options' => array('class' => 'count'));
$th['FismaInventories.related.count'] = array('content' => __('# %s', __('FISMA Inventory')), 'options' => array('class' => 'count'));

if(!$mine)
{
	$th['User.name'] = array('content' => __('Owner'), 'options' => array('sort' => 'User.name'));
	if($offset)
		$th['User.name'] = array('content' => __('Owner'));
}
$th['OrgGroup.name'] = array('content' => __('Org Group'), 'options' => array('sort' => 'OrgGroup.name'));
if($offset)
	$th['OrgGroup.name'] = array('content' => __('Org Group'));

$th['Category.public'] = array('content' => __('Share State'), 'options' => array('sort' => 'Category.public'));
$th['Category.reviewed'] = array('content' => __('Reviewed'), 'options' => array('sort' => 'Category.reviewed'));
$th['Category.created'] = array('content' => __('Created'), 'options' => array('sort' => 'Category.created'));
$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));
$th['multiselect'] = $use_multiselect;

$td = array();
foreach ($categories as $i => $category)
{
	if($offset)
	{
		$this_category_type = $category['Category']['CategoryType'];
		$this_owner = $category['Category']['User'];
		$this_org_group = $category['Category']['OrgGroup'];
	}
	else
	{
		$this_category_type = $category['CategoryType'];
		$this_owner = $category['User'];
		$this_org_group = $category['OrgGroup'];
	}
	
	$category_type = (isset($this_category_type['name'])?$this_category_type['name']:false);
	if(isset($this_category_type['org_group_id']) and in_array($this_category_type['org_group_id'], array(0, AuthComponent::user('org_group_id'))))
	{
		$category_type = $this->Html->link($this_category_type['name'], array('admin' => false, 'controller' => 'category_types', 'action' => 'view', $this_category_type['id']));
	}
	if($mine)
		$category_type = array(
			(isset($this_category_type['name'])?$this->Html->link($this_category_type['name'], array('admin' => false, 'controller' => 'category_types', 'action' => 'view', $this_category_type['id'])):'&nbsp;'),
			array('class' => 'nowrap', 'value' => (isset($this_category_type['id'])?$this_category_type['id']:0)),
		);
	
	$td[$i] = array();
	$td[$i]['Category.id'] = $this->Html->link($category['Category']['id'], array('action' => 'view', $category['Category']['id']));
	$td[$i]['Category.name'] = $this->Html->link($category['Category']['name'], array('action' => 'view', $category['Category']['id']));
	$td[$i]['Category.category_type_id'] = $category_type;
	$td[$i]['Category.mysource'] = $category['Category']['mysource'];
	$sac = $category; if(isset($sac['AdAccount'])) unset($sac['AdAccount']);
	$td[$i]['Category.sac_id'] = array(
		$this->Contacts->makePath($sac),
		array('class' => 'nowrap', 'value' => (isset($category['Sac']['id'])?$category['Sac']['id']:0)),
	);
	$td[$i]['Category.adaccount'] = (isset($category['AdAccount']['username'])?$category['AdAccount']['username']:false);
	$td[$i]['Category.victim_ip'] = $category['Category']['victim_ip'];
	$td[$i]['Category.victim_mac'] = $category['Category']['victim_mac'];
	$td[$i]['Category.victim_asset_tag'] = $category['Category']['victim_asset_tag'];
	$td[$i]['Category.assessment_nih_risk_id'] = array(
		(isset($category['AssessmentNihRisk']['name'])?$category['AssessmentNihRisk']['name']:'&nbsp;'),
		array('class' => 'nowrap', 'value' => (isset($category['AssessmentNihRisk']['id'])?$category['AssessmentNihRisk']['id']:0)),
	);
	$td[$i]['Category.assessment_cust_risk_id'] = array(
		(isset($category['AssessmentCustRisk']['name'])?$category['AssessmentCustRisk']['name']:'&nbsp;'),
		array('class' => 'nowrap', 'value' => (isset($category['AssessmentCustRisk']['id'])?$category['AssessmentCustRisk']['id']:0)),
	);
	$td[$i]['Category.targeted'] = array(
		$this->Wrap->yesNoUnknown($category['Category']['targeted']),
		array('class' => 'nowrap', 'value' => $category['Category']['targeted']),
	);
	$td[$i]['Category.compromise_date'] = array(
		$this->Wrap->niceDay($category['Category']['compromise_date']),
		array('class' => 'nowrap', 'value' => $category['Category']['compromise_date']),
	);
	$td[$i]['Vector.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'categories_vectors', 'action' => 'category', $category['Category']['id']),
		'url' => array('action' => 'view', $category['Category']['id'], 'tab' => 'active_vectors'),
	));
	$td[$i]['Reports.related.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'reports', 'action' => 'category', $category['Category']['id']),
		'url' => array('action' => 'view', $category['Category']['id'], 'tab' => 'reports'),
	));
	$td[$i]['Categories.related.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'categories', 'action' => 'category', $category['Category']['id']),
		'url' => array('action' => 'view', $category['Category']['id'], 'tab' => 'categories'),
	));
	$td[$i]['FismaSystems.related.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'fisma_systems', 'action' => 'category', $category['Category']['id']),
		'url' => array('action' => 'view', $category['Category']['id'], 'tab' => 'fisma_systems'),
	));
	$td[$i]['FismaInventories.related.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'fisma_inventories', 'action' => 'category', $category['Category']['id']),
		'url' => array('action' => 'view', $category['Category']['id'], 'tab' => 'fisma_inventories'),
	));
	
	if(!$mine)
		$td[$i]['User.name'] = $this->Html->link($this_owner['name'], array('controller' => 'users', 'action' => 'view', $this_owner['id']));
	$td[$i]['OrgGroup.name'] = $this_org_group['name'];
	$td[$i]['Category.public'] = $this->Wrap->publicState($category['Category']['public']);
	$td[$i]['Category.reviewed'] = $this->Wrap->niceTime($category['Category']['reviewed']);
	$td[$i]['Category.created'] = $this->Wrap->niceTime($category['Category']['created']);
	
	$actions = array();
	
	if($this->request->param('action') == 'category')
		$actions['compare'] = $this->Html->link(__('Compare'), array('action' => 'compare', $this->params['pass'][0], $category['Category']['id']));
	elseif($this->request->param('action') == 'report')
		$actions['compare'] = $this->Html->link(__('Compare'), array('controller' => 'vectors', 'action' => 'compare_category_report', $category['Category']['id'], $this->params['pass'][0]));
	elseif($this->request->param('action') == 'upload')
		$actions['compare'] = $this->Html->link(__('Compare'), array('controller'=>'vectors', 'action' => 'compare_category_upload', $category['Category']['id'], $this->params['pass'][0]));
	elseif($this->request->param('action') == 'import')
		$actions['compare'] = $this->Html->link(__('Compare'), array('controller' => 'vectors', 'action' => 'compare_category_import', $category['Category']['id'], $this->params['pass'][0]));
	elseif($this->request->param('action') == 'dump')
		$actions['compare'] = $this->Html->link(__('Compare'), array('controller' => 'vectors', 'action' => 'compare_category_dump', $category['Category']['id'], $this->params['pass'][0]));
	if($this->request->param('action') == 'combined_view')
		$actions['remove_combined_view'] = $this->Html->link(__('Remove'), array('controller' => 'combined_view_categories', 'action' => 'remove', $category['Category']['id'], $this->params['pass'][0]));
	
	$actions['view'] = $this->Html->link(__('View'), array('action' => 'view', $category['Category']['id']));
	
	if($mine or $category['Category']['user_id'] == AuthComponent::user('id') or $this->Common->roleCheck('admin'))
	{
		$actions['edit'] = $this->Html->link(__('Edit'), array('action' => 'edit', $category['Category']['id']));
		$actions['delete'] = $this->Html->link(__('Delete'), array('action' => 'delete', $category['Category']['id']), array('confirm' => __('Are you sure?')));
	}
	
	$td[$i]['actions'] = array(
		implode('', $actions),
		array('class' => 'actions'),
	);
	
	$td[$i]['edit_id'] = array(
		'Category' => $category['Category']['id'],
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => $page_title,
	'page_subtitle' => $page_subtitle,
	'page_description' => $page_description,
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	// grid/inline edit options
	'use_gridedit' => $use_gridedit,
	'use_multiselect' => $use_multiselect,
	'multiselect_options' => $multiselect_options,
));