<?php 
// File: app/View/Vectors/vtview.ctp

$page_options = array(
	$this->Html->link(__('%s Details', __('Regular')), array('action' => 'view', $vector['Vector']['id'])),
	$this->Form->postLink(__('Update %s Details', __('VirusTotal')),array('action' => 'update_vt', $vector['Vector']['id']), array('confirm' => 'Are you sure? Depending on the results, this may take awhile.')),
);

$details = array();
$details[] = array('name' => __('VT Tracking Level'), 'value' => $this->Wrap->vtAutoLookupLevel($vector['VectorDetail']['vt_lookup']));
$details[] = array('name' => __('VT Checked'), 'value' => $this->Wrap->niceTime($vector['VectorDetail']['vt_checked']));
$details[] = array('name' => __('VT Updated'), 'value' => $this->Wrap->niceTime($vector['VectorDetail']['vt_updated']));


$stats = array();
$tabs = array();
$stat_count = count($stats);
$tab_count = count($tabs);

$stat_count++;
$tab_count++;
$stats[] = array(
	'id' => 'VtNtRecords',
	'name' => __('VT Network Records'), 
	'value' => $vector['Vector']['counts']['VtNtRecord.all'], 
	'tab' => array('tabs', $tab_count), // the tab to display
);
$tabs[] = array(
	'key' => 'VtNtRecords',
	'title' => __('VT Network Records'),
	'url' => array('controller' => 'vt_nt_records', 'action' => 'vector', $vector['Vector']['id'], 'vtview'),
);

$stat_count++;
$tab_count++;
$stats[] = array(
	'id' => 'VtRelatedSamples',
	'name' => __('VT Related Samples'), 
	'value' => $vector['Vector']['counts']['VtRelatedSample.all'], 
	'tab' => array('tabs', $tab_count), // the tab to display
);
$tabs[] = array(
	'key' => 'VtRelatedSamples',
	'title' => __('VT Related Samples'),
	'url' => array('controller' => 'vt_related_samples', 'action' => 'vector', $vector['Vector']['id'], 'vtview'),
);

$stat_count++;
$tab_count++;
$stats[] = array(
	'id' => 'VtDetectedUrls',
	'name' => __('VT Detected Urls'), 
	'value' => $vector['Vector']['counts']['VtDetectedUrl.all'], 
	'tab' => array('tabs', $tab_count), // the tab to display
);
$tabs[] = array(
	'key' => 'VtDetectedUrls',
	'title' => __('VT Detected Urls'),
	'url' => array('controller' => 'vt_detected_urls', 'action' => 'vector', $vector['Vector']['id'], 'vtview'),
);

$stat_count++;
$tab_count++;
$stats[] = array(
	'id' => 'Vectors',
	'name' => __('VT Related %s', __('Vectors')), 
	'value' => $vector['Vector']['counts']['Vector.related'], 
	'tab' => array('tabs', $tab_count), // the tab to display
);
$tabs[] = array(
	'key' => 'Vectors',
	'title' => __('VT Related %s', __('Vectors')),
	'url' => array('controller' => 'vectors', 'action' => 'vt_related', $vector['Vector']['id'], 'vtview'),
);

$stat_count++;
$tab_count++;
$stats[] = array(
	'id' => 'RawFiles',
	'name' => __('Raw Files'), 
	'value' => count($raw_files), 
	'tab' => array('tabs', $tab_count), // the tab to display
);
$tabs[] = array(
	'key' => 'RawFiles',
	'title' => __('Raw Files'),
	'url' => array('controller' => 'vectors', 'action' => 'vt_raw_files', $vector['Vector']['id'], 'vtview'),
);

$stat_count++;
$tab_count++;
$stats[] = array(
	'id' => 'CategoriesVector',
	'name' => __('Related %s %s', __('Category'), __('Vectors')), 
	'value' => $vector['Vector']['counts']['CategoriesVector.related'], 
	'tab' => array('tabs', $tab_count), // the tab to display
);
$tabs[] = array(
	'key' => 'CategoriesVector',
	'title' => __('Related %s %s', __('Category'), __('Vectors')), 
	'url' => array('controller' => 'categories_vectors', 'action' => 'vt_related', $vector['Vector']['id'], 'vtview'),
);

$stat_count++;
$tab_count++;
$stats[] = array(
	'id' => 'ReportsVector',
	'name' => __('Related %s %s', __('Report'), __('Vectors')), 
	'value' => $vector['Vector']['counts']['ReportsVector.related'], 
	'tab' => array('tabs', $tab_count), // the tab to display
);
$tabs[] = array(
	'key' => 'ReportsVector',
	'title' => __('Related %s %s', __('Report'), __('Vectors')), 
	'url' => array('controller' => 'reports_vectors', 'action' => 'vt_related', $vector['Vector']['id'], 'vtview'),
);

$stat_count++;
$tab_count++;
$stats[] = array(
	'id' => 'UploadsVector',
	'name' => __('Related %s %s', __('File'), __('Vectors')), 
	'value' => $vector['Vector']['counts']['UploadsVector.related'], 
	'tab' => array('tabs', $tab_count), // the tab to display
);
$tabs[] = array(
	'key' => 'UploadsVector',
	'title' => __('Related %s %s', __('File'), __('Vectors')), 
	'url' => array('controller' => 'uploads_vectors', 'action' => 'vt_related', $vector['Vector']['id'], 'vtview'),
);

$stat_count++;
$tab_count++;
$stats[] = array(
	'id' => 'ImportsVector',
	'name' => __('Related %s %s', __('Import'), __('Vectors')), 
	'value' => $vector['Vector']['counts']['ImportsVector.related'], 
	'tab' => array('tabs', $tab_count), // the tab to display
);
$tabs[] = array(
	'key' => 'ImportsVector',
	'title' => __('Related %s %s', __('Import'), __('Vectors')), 
	'url' => array('controller' => 'imports_vectors', 'action' => 'vt_related', $vector['Vector']['id'], 'vtview'),
);



echo $this->element('Utilities.page_view', array(
	'page_title' => __('%s - %s Details: %s', __('Vector'), __('VirusTotal'), $vector['Vector']['vector']),
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));