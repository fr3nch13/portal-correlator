<?php 

$page_title = (isset($page_title)?$page_title:__('Reports'));
$page_subtitle = (isset($page_subtitle)?$page_subtitle:__('All Available'));
$page_description = (isset($page_description)?$page_description:'');
$page_options = (isset($page_options)?$page_options:array());
$use_multiselect = (isset($use_multiselect)?$use_multiselect:false);
$multiselect_options = (isset($multiselect_options)?$multiselect_options:array());
$use_gridedit = (isset($use_gridedit)?$use_gridedit:false);

// viewing a list of my reports
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
	$page_options['add'] = $this->Html->link(__('Add %s', __('Report')), array('controller' => 'temp_reports', 'action' => 'add', 'admin' => false));
}

// content
$th = array();
$th['Report.id'] = array('content' => __('ID'), 'options' => array('sort' => 'Report.id'));
$th['Report.name'] = array('content' => __('Name'), 'options' => array('sort' => 'Report.name'));
if($mine) $th['Report.name']['options']['editable'] = array('type' => 'text');
$th['Report.report_type_id'] = array('content' => __('Report Group'), 'options' => array('sort' => 'ReportType.name'));
if($mine) $th['Report.report_type_id']['options']['editable'] = array('type' => 'select', 'options' => $reportTypes);
if($offset)
	$th['Report.report_type_id'] = array('content' => __('Report Group'));
$th['Report.mysource'] = array('content' => __('User Source'), 'options' => array('sort' => 'Report.mysource'));
if($mine) $th['Report.mysource']['options']['editable'] = array('type' => 'text');
$th['Report.sac_id'] = array('content' => __('SAC'), 'options' => array('sort' => 'Report.sac_id'));
if($mine) $th['Report.sac_id']['options']['editable'] = array('type' => 'select', 'options' => $sacs);
$th['Report.adaccount'] = array('content' => __('AD Account'), 'options' => array('sort' => 'Report.ad_account_id'));
if($mine) $th['Report.adaccount']['options']['editable'] = array('type' => 'autocomplete', 'rel' => array('controller' => 'ad_accounts', 'action' => 'autocomplete'));
$th['Report.victim_ip'] = array('content' => __('Victim IP'), 'options' => array('sort' => 'Report.victim_ip'));
if($mine) $th['Report.victim_ip']['options']['editable'] = array('type' => 'text');
$th['Report.victim_mac'] = array('content' => __('Victim MAC'), 'options' => array('sort' => 'Report.victim_mac'));
if($mine) $th['Report.victim_mac']['options']['editable'] = array('type' => 'text');
$th['Report.victim_asset_tag'] = array('content' => __('Victim Asset Tag'), 'options' => array('sort' => 'Report.victim_asset_tag'));
if($mine) $th['Report.victim_asset_tag']['options']['editable'] = array('type' => 'text');
$th['Report.assessment_nih_risk_id'] = array('content' => __('NIH Risk'), 'options' => array('sort' => 'AssessmentNihRisk.name'));
if($mine) $th['Report.assessment_nih_risk_id']['options']['editable'] = array('type' => 'select', 'options' => $assessmentNihRisks);
$th['Report.assessment_cust_risk_id'] = array('content' => __('Customer Risk'), 'options' => array('sort' => 'AssessmentCustRisk.name'));
if($mine) $th['Report.assessment_cust_risk_id']['options']['editable'] = array('type' => 'select', 'options' => $assessmentCustRisks);
$th['Report.targeted'] = array('content' => __('Targeted/APT'), 'options' => array('sort' => 'Report.targeted'));
if($mine) $th['Report.targeted']['options']['editable'] = array('type' => 'boolean');
$th['Report.compromise_date'] = array('content' => __('Compromised Date'), 'options' => array('sort' => 'Report.compromise_date'));
if($mine) $th['Report.compromise_date']['options']['editable'] = array('type' => 'date');
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

$th['Report.public'] = array('content' => __('Share State'), 'options' => array('sort' => 'Report.public'));
$th['Report.reviewed'] = array('content' => __('Reviewed'), 'options' => array('sort' => 'Report.reviewed'));
$th['Report.created'] = array('content' => __('Created'), 'options' => array('sort' => 'Report.created'));
$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));
$th['multiselect'] = $use_multiselect;

$td = array();
foreach ($reports as $i => $report)
{
	if($offset)
	{
		$this_report_type = $report['Report']['ReportType'];
		$this_owner = $report['Report']['User'];
		$this_org_group = $report['Report']['OrgGroup'];
	}
	else
	{
		$this_report_type = $report['ReportType'];
		$this_owner = $report['User'];
		$this_org_group = $report['OrgGroup'];
	}
	
	$report_type = false;
	if(isset($this_report_type['name']))
	{
		$report_type = $this_report_type['name'];
		if(in_array($this_report_type['org_group_id'], array(0, AuthComponent::user('org_group_id'))))
		{
			$report_type = $this->Html->link($this_report_type['name'], array('admin' => false, 'controller' => 'report_types', 'action' => 'view', $this_report_type['id']));
		}
		if($mine)
			$report_type = array(
				(isset($this_report_type['name'])?$this->Html->link($this_report_type['name'], array('admin' => false, 'controller' => 'report_types', 'action' => 'view', $this_report_type['id'])):'&nbsp;'),
				array('class' => 'nowrap', 'value' => (isset($this_report_type['id'])?$this_report_type['id']:0)),
			);
	}
	
	$td[$i] = array();
	$td[$i]['Report.id'] = $this->Html->link($report['Report']['id'], array('action' => 'view', $report['Report']['id']));
	$td[$i]['Report.name'] = $this->Html->link($report['Report']['name'], array('action' => 'view', $report['Report']['id']));
	$td[$i]['Report.report_type_id'] = $report_type;
	$td[$i]['Report.mysource'] = $report['Report']['mysource'];
	$sac = $report; if(isset($sac['AdAccount'])) unset($sac['AdAccount']);
	$td[$i]['Report.sac_id'] = array(
		$this->Contacts->makePath($sac),
		array('class' => 'nowrap', 'value' => (isset($report['Sac']['id'])?$report['Sac']['id']:0)),
	);
	$td[$i]['Report.adaccount'] = (isset($report['AdAccount']['username'])?$report['AdAccount']['username']:false);
	$td[$i]['Report.victim_ip'] = $report['Report']['victim_ip'];
	$td[$i]['Report.victim_mac'] = $report['Report']['victim_mac'];
	$td[$i]['Report.victim_asset_tag'] = $report['Report']['victim_asset_tag'];
	$td[$i]['Report.assessment_nih_risk_id'] = array(
		(isset($report['AssessmentNihRisk']['name'])?$report['AssessmentNihRisk']['name']:'&nbsp;'),
		array('class' => 'nowrap', 'value' => (isset($report['AssessmentNihRisk']['id'])?$report['AssessmentNihRisk']['id']:0)),
	);
	$td[$i]['Report.assessment_cust_risk_id'] = array(
		(isset($report['AssessmentCustRisk']['name'])?$report['AssessmentCustRisk']['name']:'&nbsp;'),
		array('class' => 'nowrap', 'value' => (isset($report['AssessmentCustRisk']['id'])?$report['AssessmentCustRisk']['id']:0)),
	);
	$td[$i]['Report.targeted'] = array(
		$this->Wrap->yesNoUnknown($report['Report']['targeted']),
		array('class' => 'nowrap', 'value' => $report['Report']['targeted']),
	);
	$td[$i]['Report.compromise_date'] = array(
		$this->Wrap->niceDay($report['Report']['compromise_date']),
		array('class' => 'nowrap', 'value' => $report['Report']['compromise_date']),
	);
	$td[$i]['Vector.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'reports_vectors', 'action' => 'report', $report['Report']['id']),
		'url' => array('action' => 'view', $report['Report']['id'], 'tab' => 'active_vectors'),
	));
	$td[$i]['Reports.related.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'reports', 'action' => 'report', $report['Report']['id']),
		'url' => array('action' => 'view', $report['Report']['id'], 'tab' => 'reports'),
	));
	$td[$i]['Categories.related.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'categories', 'action' => 'report', $report['Report']['id']),
		'url' => array('action' => 'view', $report['Report']['id'], 'tab' => 'categories'),
	));
	$td[$i]['FismaSystems.related.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'fisma_systems', 'action' => 'report', $report['Report']['id']),
		'url' => array('action' => 'view', $report['Report']['id'], 'tab' => 'fisma_systems'),
	));
	$td[$i]['FismaInventories.related.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'fisma_inventories', 'action' => 'report', $report['Report']['id']),
		'url' => array('action' => 'view', $report['Report']['id'], 'tab' => 'fisma_inventories'),
	));
	
	if(!$mine)
		$td[$i]['User.name'] = $this->Html->link($this_owner['name'], array('controller' => 'users', 'action' => 'view', $this_owner['id']));
	$td[$i]['OrgGroup.name'] = $this_org_group['name'];
	$td[$i]['Report.public'] = $this->Wrap->publicState($report['Report']['public']);
	$td[$i]['Report.reviewed'] = $this->Wrap->niceTime($report['Report']['reviewed']);
	$td[$i]['Report.created'] = $this->Wrap->niceTime($report['Report']['created']);
	
	$actions = array();
	
	if($this->request->param('action') == 'report')
		$actions['compare'] = $this->Html->link(__('Compare'), array('action' => 'compare', $this->params['pass'][0], $report['Report']['id']));
	elseif($this->request->param('action') == 'report')
		$actions['compare'] = $this->Html->link(__('Compare'), array('controller' => 'vectors', 'action' => 'compare_report_report', $report['Report']['id'], $this->params['pass'][0]));
	elseif($this->request->param('action') == 'upload')
		$actions['compare'] = $this->Html->link(__('Compare'), array('controller'=>'vectors', 'action' => 'compare_report_upload', $report['Report']['id'], $this->params['pass'][0]));
	elseif($this->request->param('action') == 'import')
		$actions['compare'] = $this->Html->link(__('Compare'), array('controller' => 'vectors', 'action' => 'compare_report_import', $report['Report']['id'], $this->params['pass'][0]));
	elseif($this->request->param('action') == 'dump')
		$actions['compare'] = $this->Html->link(__('Compare'), array('controller' => 'vectors', 'action' => 'compare_report_dump', $report['Report']['id'], $this->params['pass'][0]));
	if($this->request->param('action') == 'combined_view')
		$actions['remove_combined_view'] = $this->Html->link(__('Remove'), array('controller' => 'combined_view_reports', 'action' => 'remove', $report['Report']['id'], $this->params['pass'][0]));
	
	$actions['view'] = $this->Html->link(__('View'), array('action' => 'view', $report['Report']['id']));
	
	if($mine or $report['Report']['user_id'] == AuthComponent::user('id') or $this->Common->roleCheck('admin'))
	{
		$actions['edit'] = $this->Html->link(__('Edit'), array('action' => 'edit', $report['Report']['id']));
		$actions['delete'] = $this->Html->link(__('Delete'), array('action' => 'delete', $report['Report']['id']), array('confirm' => __('Are you sure?')));
	}
	
	$td[$i]['actions'] = array(
		implode('', $actions),
		array('class' => 'actions'),
	);
	
	$td[$i]['edit_id'] = array(
		'Report' => $report['Report']['id'],
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