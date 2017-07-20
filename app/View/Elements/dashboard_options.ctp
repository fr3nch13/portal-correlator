<?php
$this->extend('Utilities.object_dashboard_options');

$dashboard_options_title = __('Switch Dashboards');

$dashboard_options_items = array();
$dashboard_options_items[] = $this->Html->link(__('Overview'), array('controller' => 'main', 'action' => 'dashboard'));
$dashboard_options_items[] = $this->Html->link(__('My Overview'), array('controller' => 'main', 'action' => 'my_dashboard'));
$dashboard_options_items[] = $this->Html->link(__('Categories'), array('controller' => 'categories', 'action' => 'dashboard'));
$dashboard_options_items[] = $this->Html->link(__('Reports'), array('controller' => 'reports', 'action' => 'dashboard'));
$dashboard_options_items[] = $this->Html->link(__('Vectors'), array('controller' => 'vectors', 'action' => 'dashboard'));
$dashboard_options_items[] = $this->Html->link(__('DNS Records'), array('controller' => 'nslookups', 'action' => 'dashboard'));
$dashboard_options_items[] = $this->Html->link(__('WHOIS Records'), array('controller' => 'whois', 'action' => 'dashboard'));

$this->set('dashboard_options_title', $dashboard_options_title);
$this->set('dashboard_options_items', $dashboard_options_items);
