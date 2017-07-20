<?php 
// File: app/View/Whois/index.ctp

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'Whois.tld' => array('content' => __('Tld'), 'options' => array('sort' => 'Whois.tld')),
	'Whois.source' => array('content' => __('Source'), 'options' => array('sort' => 'Whois.source')),
	'Whois.createdDate' => array('content' => __('Whois Created'), 'options' => array('sort' => 'Whois.createdDate')),
	'Whois.updatedDate' => array('content' => __('Whois Updated'), 'options' => array('sort' => 'Whois.updatedDate')),
	'Whois.expiresDate' => array('content' => __('Whois Expires'), 'options' => array('sort' => 'Whois.expiresDate')),
	'Whois.whois_checked' => array('content' => __('Checked'), 'options' => array('sort' => 'Whois.whois_checked')),
	'Whois.whois_updated' => array('content' => __('Updated'), 'options' => array('sort' => 'Whois.whois_updated')),
//	'Whois.modified' => array('content' => __('Updated'), 'options' => array('sort' => 'Whois.modified')),
	'Whois.created' => array('content' => __('Added'), 'options' => array('sort' => 'Whois.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($whois as $i => $_whois)
{
	$td[$i] = array(
		$_whois['Vector']['vector'],
		$this->Html->link($_whois['Whois']['tld'], array('action' => 'index'), array('filter_field' => 'tld')),
		$this->Wrap->sourceUser($_whois['Whois']['source']),
		$this->Wrap->niceDay($_whois['Whois']['createdDate']),
		$this->Wrap->niceDay($_whois['Whois']['updatedDate']),
		$this->Wrap->niceDay($_whois['Whois']['expiresDate']),
		$this->Wrap->niceTime($_whois['Whois']['whois_checked']),
		$this->Wrap->niceTime($_whois['Whois']['whois_updated']),
//		$this->Wrap->niceTime($_whois['Whois']['modified']),
		$this->Wrap->niceTime($_whois['Whois']['created']),
		array(
			$this->Html->link(__('View Whois'), array('controller' => 'whois', 'action' => 'view', $_whois['Whois']['id'])).
			$this->Html->link(__('View Vector'), array('controller' => 'vectors', 'action' => 'view', $_whois['Vector']['id'])),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Whois Records'),
	'search_placeholder' => __('Whois Records'),
	'th' => $th,
	'td' => $td,
	));
?>