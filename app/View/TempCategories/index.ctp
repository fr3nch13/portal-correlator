<?php 
$page_title = (isset($page_title)?$page_title:__('Temp Categories'));
$page_subtitle = (isset($page_subtitle)?$page_subtitle:__('All Available'));
$page_description = (isset($page_description)?$page_description:'');
$page_options = (isset($page_options)?$page_options:array());
$use_multiselect = (isset($use_multiselect)?$use_multiselect:false);
$multiselect_options = (isset($multiselect_options)?$multiselect_options:array());
$use_gridedit = (isset($use_gridedit)?$use_gridedit:true);

$page_options['add'] = $this->Html->link(__('Add %s', __('Category')), array('action' => 'add', 'admin' => false));

$th = array();
$th['TempCategory.name'] = array('content' => __('Name'), 'options' => array('sort' => 'TempCategory.name', 'editable' => array('type' => 'text')));
$th['TempCategory.category_type_id'] = array('content' => __('Category Group'), 'options' => array('sort' => 'CategoryType.name', 'editable' => array('type' => 'select', 'options' => $categoryTypes) ));
$th['TempCategory.mysource'] = array('content' => __('User Source'), 'options' => array('sort' => 'TempCategory.mysource', 'editable' => array('type' => 'text')));
$th['TempCategory.sac_id'] = array('content' => __('SAC'), 'options' => array('sort' => 'Sac.shortname', 'editable' => array('type' => 'select', 'options' => $sacs) ));
$th['TempCategory.adaccount'] = array('content' => __('AD Account'), 'options' => array('sort' => 'TempCategory.ad_account_id', 'editable' => array('type' => 'autocomplete', 'rel' => array('controller' => 'ad_accounts', 'action' => 'autocomplete'))));
$th['TempCategory.victim_ip'] = array('content' => __('Victim IP'), 'options' => array('sort' => 'TempCategory.victim_ip', 'editable' => array('type' => 'text')));
$th['TempCategory.victim_mac'] = array('content' => __('Victim MAC'), 'options' => array('sort' => 'TempCategory.victim_mac', 'editable' => array('type' => 'text')));
$th['TempCategory.victim_asset_tag'] = array('content' => __('Victim Asset Tag'), 'options' => array('sort' => 'TempCategory.victim_asset_tag', 'editable' => array('type' => 'text')));
$th['TempCategory.assessment_nih_risk_id'] = array('content' => __('NIH Risk'), 'options' => array('sort' => 'AssessmentNihRisk.name', 'editable' => array('type' => 'select', 'options' => $assessmentNihRisks) ));
$th['TempCategory.assessment_cust_risk_id'] = array('content' => __('Customer Risk'), 'options' => array('sort' => 'AssessmentCustRisk.name', 'editable' => array('type' => 'select', 'options' => $assessmentCustRisks) ));
$th['TempCategory.targeted'] = array('content' => __('Target APT'), 'options' => array('sort' => 'TempCategory.targeted', 'editable' => array('type' => 'boolean')));
$th['TempCategory.compromise_date'] = array('content' => __('Compromised Date'), 'options' => array('sort' => 'TempCategory.compromise_date', 'editable' => array('type' => 'date')));
$th['Vector.count'] = array('content' => __('# %s', __('Vectors')), 'options' => array('class' => 'count'));
$th['TempCategory.public'] = array('content' => __('Share State'), 'options' => array('sort' => 'TempCategory.public'));
$th['TempCategory.created'] = array('content' => __('Created'), 'options' => array('sort' => 'TempCategory.created'));
$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));
$th['multiselect'] = $use_multiselect;

$td = array();
foreach ($temp_categories as $i => $temp_category)
{
	$td[$i] = array();
	$td[$i]['TempCategory.name'] = $this->Html->link($temp_category['TempCategory']['name'], array('action' => 'view', $temp_category['TempCategory']['id']));
	$td[$i]['TempCategory.category_type_id'] = array(
		(isset($temp_category['CategoryType']['name'])?$this->Html->link($temp_category['CategoryType']['name'], array('admin' => false, 'controller' => 'category_types', 'action' => 'view', $temp_category['CategoryType']['id'])):'&nbsp;'),
		array('class' => 'nowrap', 'value' => (isset($temp_category['CategoryType']['id'])?$temp_category['CategoryType']['id']:0)),
	);
	$td[$i]['TempCategory.mysource'] = $temp_category['TempCategory']['mysource'];
	$sac = $temp_category; if(isset($sac['AdAccount'])) unset($sac['AdAccount']);
	$td[$i]['TempCategory.sac_id'] = array(
		$this->Contacts->makePath($sac),
		array('class' => 'nowrap', 'value' => (isset($temp_category['Sac']['id'])?$temp_category['Sac']['id']:0)),
	);
	$td[$i]['TempCategory.adaccount'] = (isset($temp_category['AdAccount']['username'])?$temp_category['AdAccount']['username']:false);
	$td[$i]['TempCategory.victim_ip'] = $temp_category['TempCategory']['victim_ip'];
	$td[$i]['TempCategory.victim_mac'] = $temp_category['TempCategory']['victim_mac'];
	$td[$i]['TempCategory.victim_asset_tag'] = $temp_category['TempCategory']['victim_asset_tag'];
	$td[$i]['TempCategory.assessment_nih_risk_id'] = array(
		(isset($temp_category['AssessmentNihRisk']['name'])?$temp_category['AssessmentNihRisk']['name']:'&nbsp;'),
		array('class' => 'nowrap', 'value' => (isset($temp_category['AssessmentNihRisk']['id'])?$temp_category['AssessmentNihRisk']['id']:0)),
	);
	$td[$i]['TempCategory.assessment_cust_risk_id'] = array(
		(isset($temp_category['AssessmentCustRisk']['name'])?$temp_category['AssessmentCustRisk']['name']:'&nbsp;'),
		array('class' => 'nowrap', 'value' => (isset($temp_category['AssessmentCustRisk']['id'])?$temp_category['AssessmentCustRisk']['id']:0)),
	);
	$td[$i]['TempCategory.targeted'] = array(
		$this->Wrap->yesNoUnknown($temp_category['TempCategory']['targeted']),
		array('class' => 'nowrap', 'value' => $temp_category['TempCategory']['targeted']),
	);
	$td[$i]['TempCategory.compromise_date'] = array(
		$this->Wrap->niceDay($temp_category['TempCategory']['compromise_date']),
		array('class' => 'nowrap', 'value' => $temp_category['TempCategory']['compromise_date']),
	);
	$td[$i]['Vector.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'temp_categories_vectors', 'action' => 'temp_category', $temp_category['TempCategory']['id']),
		'url' => array('action' => 'view', $temp_category['TempCategory']['id'], 'tab' => 'vectors'),
	));
	$td[$i]['TempCategory.public'] = $this->Wrap->publicState($temp_category['TempCategory']['public']);
	$td[$i]['TempCategory.created'] = $this->Wrap->niceTime($temp_category['TempCategory']['created']);
	
	$actions = array();
	$actions['view'] = $this->Html->link(__('View'), array('action' => 'view', $temp_category['TempCategory']['id']));
	$actions['edit'] = $this->Html->link(__('Edit'), array('action' => 'edit', $temp_category['TempCategory']['id']));
	$actions['delete'] = $this->Html->link(__('Delete'), array('action' => 'delete', $temp_category['TempCategory']['id']), array('confirm' => __('Are you sure?')));
	$actions['reviewed'] = $this->Html->link(__('Reviewed'), array('action' => 'reviewed', $temp_category['TempCategory']['id']), array('confirm' => __('Are you sure?'), 'class' => 'button_red'));
	
	$td[$i]['actions'] = array(
		implode('', $actions),
		array('class' => 'actions'),
	);
	$td[$i]['edit_id'] = array(
		'TempCategory' => $temp_category['TempCategory']['id'],
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