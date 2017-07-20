<?php 
$page_title = (isset($page_title)?$page_title:__('Temp Reports'));
$page_subtitle = (isset($page_subtitle)?$page_subtitle:__('All Available'));
$page_description = (isset($page_description)?$page_description:'');
$page_options = (isset($page_options)?$page_options:array());
$use_multiselect = (isset($use_multiselect)?$use_multiselect:false);
$multiselect_options = (isset($multiselect_options)?$multiselect_options:array());
$use_gridedit = (isset($use_gridedit)?$use_gridedit:true);

$page_options['add'] = $this->Html->link(__('Add %s', __('Report')), array('action' => 'add', 'admin' => false));
//$page_options['batchadd'] = $this->Html->link(__('Add Multiple %s', __('Reports')), array('action' => 'batchadd', 'admin' => false));

$th = array();
$th['TempReport.id'] = array('content' => __('ID'), 'options' => array('sort' => 'TempReport.id'));
$th['TempReport.name'] = array('content' => __('Name'), 'options' => array('sort' => 'TempReport.name', 'editable' => array('type' => 'text')));
$th['TempReport.report_type_id'] = array('content' => __('Report Group'), 'options' => array('sort' => 'ReportType.name', 'editable' => array('type' => 'select', 'options' => $reportTypes) ));
$th['TempReport.mysource'] = array('content' => __('User Source'), 'options' => array('sort' => 'TempReport.mysource', 'editable' => array('type' => 'text')));
$th['TempReport.sac_id'] = array('content' => __('SAC'), 'options' => array('sort' => 'Sac.shortname', 'editable' => array('type' => 'select', 'options' => $sacs) ));
$th['TempReport.adaccount'] = array('content' => __('AD Account'), 'options' => array('sort' => 'TempReport.ad_account_id', 'editable' => array('type' => 'autocomplete', 'rel' => array('controller' => 'ad_accounts', 'action' => 'autocomplete'))));
$th['TempReport.victim_ip'] = array('content' => __('Victim IP'), 'options' => array('sort' => 'TempReport.victim_ip', 'editable' => array('type' => 'text')));
$th['TempReport.victim_mac'] = array('content' => __('Victim MAC'), 'options' => array('sort' => 'TempReport.victim_mac', 'editable' => array('type' => 'text')));
$th['TempReport.victim_asset_tag'] = array('content' => __('Victim Asset Tag'), 'options' => array('sort' => 'TempReport.victim_asset_tag', 'editable' => array('type' => 'text')));
$th['TempReport.assessment_nih_risk_id'] = array('content' => __('NIH Risk'), 'options' => array('sort' => 'AssessmentNihRisk.name', 'editable' => array('type' => 'select', 'options' => $assessmentNihRisks) ));
$th['TempReport.assessment_cust_risk_id'] = array('content' => __('Customer Risk'), 'options' => array('sort' => 'AssessmentCustRisk.name', 'editable' => array('type' => 'select', 'options' => $assessmentCustRisks) ));
$th['TempReport.targeted'] = array('content' => __('Target APT'), 'options' => array('sort' => 'TempReport.targeted', 'editable' => array('type' => 'boolean')));
$th['TempReport.compromise_date'] = array('content' => __('Compromised Date'), 'options' => array('sort' => 'TempReport.compromise_date', 'editable' => array('type' => 'date')));
$th['Vector.count'] = array('content' => __('# %s', __('Vectors')), 'options' => array('class' => 'count'));
$th['TempReport.public'] = array('content' => __('Share State'), 'options' => array('sort' => 'TempReport.public'));
$th['TempReport.created'] = array('content' => __('Created'), 'options' => array('sort' => 'TempReport.created'));
$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));
$th['multiselect'] = $use_multiselect;

$td = array();
foreach ($temp_reports as $i => $temp_report)
{
	$td[$i] = array();
	$td[$i]['TempReport.id'] = $this->Html->link($temp_report['TempReport']['id'], array('action' => 'view', $temp_report['TempReport']['id']));
	$td[$i]['TempReport.name'] = $this->Html->link($temp_report['TempReport']['name'], array('action' => 'view', $temp_report['TempReport']['id']));
	$td[$i]['TempReport.report_type_id'] = array(
		(isset($temp_report['ReportType']['name'])?$this->Html->link($temp_report['ReportType']['name'], array('admin' => false, 'controller' => 'report_types', 'action' => 'view', $temp_report['ReportType']['id'])):'&nbsp;'),
		array('class' => 'nowrap', 'value' => (isset($temp_report['ReportType']['id'])?$temp_report['ReportType']['id']:0)),
	);
	$td[$i]['TempReport.mysource'] = $temp_report['TempReport']['mysource'];
	$sac = $temp_report; if(isset($sac['AdAccount'])) unset($sac['AdAccount']);
	$td[$i]['TempReport.sac_id'] = array(
		$this->Contacts->makePath($sac),
		array('class' => 'nowrap', 'value' => (isset($temp_report['Sac']['id'])?$temp_report['Sac']['id']:0)),
	);
	$td[$i]['TempReport.adaccount'] = (isset($temp_report['AdAccount']['username'])?$temp_report['AdAccount']['username']:false);
	$td[$i]['TempReport.victim_ip'] = $temp_report['TempReport']['victim_ip'];
	$td[$i]['TempReport.victim_mac'] = $temp_report['TempReport']['victim_mac'];
	$td[$i]['TempReport.victim_asset_tag'] = $temp_report['TempReport']['victim_asset_tag'];
	$td[$i]['TempReport.assessment_nih_risk_id'] = array(
		(isset($temp_report['AssessmentNihRisk']['name'])?$temp_report['AssessmentNihRisk']['name']:'&nbsp;'),
		array('class' => 'nowrap', 'value' => (isset($temp_report['AssessmentNihRisk']['id'])?$temp_report['AssessmentNihRisk']['id']:0)),
	);
	$td[$i]['TempReport.assessment_cust_risk_id'] = array(
		(isset($temp_report['AssessmentCustRisk']['name'])?$temp_report['AssessmentCustRisk']['name']:'&nbsp;'),
		array('class' => 'nowrap', 'value' => (isset($temp_report['AssessmentCustRisk']['id'])?$temp_report['AssessmentCustRisk']['id']:0)),
	);
	$td[$i]['TempReport.targeted'] = array(
		$this->Wrap->yesNoUnknown($temp_report['TempReport']['targeted']),
		array('class' => 'nowrap', 'value' => $temp_report['TempReport']['targeted']),
	);
	$td[$i]['TempReport.compromise_date'] = array(
		$this->Wrap->niceDay($temp_report['TempReport']['compromise_date']),
		array('class' => 'nowrap', 'value' => $temp_report['TempReport']['compromise_date']),
	);
	$td[$i]['Vector.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'temp_reports_vectors', 'action' => 'temp_report', $temp_report['TempReport']['id']),
		'url' => array('action' => 'view', $temp_report['TempReport']['id'], '#' => 'ui-tabs-1'),
	));
	$td[$i]['TempReport.public'] = $this->Wrap->publicState($temp_report['TempReport']['public']);
	$td[$i]['TempReport.created'] = $this->Wrap->niceTime($temp_report['TempReport']['created']);
	
	$actions = array();
	$actions['view'] = $this->Html->link(__('View'), array('action' => 'view', $temp_report['TempReport']['id']));
	$actions['edit'] = $this->Html->link(__('Edit'), array('action' => 'edit', $temp_report['TempReport']['id']));
	$actions['delete'] = $this->Html->link(__('Delete'), array('action' => 'delete', $temp_report['TempReport']['id']), array('confirm' => __('Are you sure?')));
	$actions['reviewed'] = $this->Html->link(__('Reviewed'), array('action' => 'reviewed', $temp_report['TempReport']['id']), array('confirm' => __('Are you sure?'), 'class' => 'button_red'));
	
	$td[$i]['actions'] = array(
		implode('', $actions),
		array('class' => 'actions'),
	);
	$td[$i]['edit_id'] = array(
		'TempReport' => $temp_report['TempReport']['id'],
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