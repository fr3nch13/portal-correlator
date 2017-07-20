<?php 
if (AuthComponent::user('id')): 
	$dashboardUserRole = $this->Common->dashboardUserRole();
?>
<ul class="sf-menu">
	<?php if($dashboardUserRole): ?>
	<li><?php echo $this->Html->link(__('Portal Overview'), array('controller' => 'main', 'action' => 'dashboard', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
	<li><?php echo $this->Html->link(__('My Overview'), array('controller' => 'main', 'action' => 'my_dashboard', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
	<li><?php echo $this->Html->link(__('Categories'), array('controller' => 'categories', 'action' => 'dashboard', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
	<li><?php echo $this->Html->link(__('Reports'), array('controller' => 'reports', 'action' => 'dashboard', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
	<li><?php echo $this->Html->link(__('Vectors'), array('controller' => 'vectors', 'action' => 'dashboard', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
	<li><?php echo $this->Html->link(__('DNS Records'), array('controller' => 'nslookups', 'action' => 'dashboard', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
	<li><?php echo $this->Html->link(__('WHOIS Records'), array('controller' => 'whois', 'action' => 'dashboard', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
	<?php else: ?>							
	<?php if ($this->Common->roleCheck('admin')): ?>
	<li>
		<?php echo $this->Html->link(__('Admin'), '#', array('class' => 'top')); ?>
		<ul>
			<li><?php echo $this->Html->link(__('Categories'), array('controller' => 'categories', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Category Groups'), array('controller' => 'category_types', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Reports'), array('controller' => 'reports', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Report Groups'), array('controller' => 'report_types', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Files'), array('controller' => 'uploads', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('File Groups'), array('controller' => 'upload_types', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Imports'), array('controller' => 'imports', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Import Managers'), array('controller' => 'import_managers', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
			<li>
				<?php echo $this->Html->link(__('Tools'), '#', array('class' => 'sub')); ?>
				<ul>
					<li><?php echo $this->Html->link(__('Remove Duplicate Lines'), array('controller' => 'main', 'action' => 'tool_duplicates', 'admin' => false, 'plugin' => false)); ?></li>
				</lu>
			</li>
			<li>
				<?php echo $this->Html->link(__('Vectors'), '#', array('class' => 'sub')); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All %s', __('Vectors')), array('controller' => 'vectors', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Active %s', __('Vectors')), array('controller' => 'vectors', 'action' => 'good', 'admin' => true, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Benign %s', __('Vectors')), array('controller' => 'vectors', 'action' => 'bad', 'admin' => true, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('%s Groups', __('Vectors')), array('controller' => 'vector_types', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Whois Records'), array('controller' => 'whois', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Whoiser Transactions'), array('controller' => 'whoiser_transactions', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li><?php echo $this->Html->link(__('Users'), '#', array('class' => 'sub')); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All %s', __('Users')), array('controller' => 'users', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Login History'), array('controller' => 'login_histories', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Org Groups'), array('controller' => 'org_groups', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li><?php echo $this->Html->link(__('App Admin'), '#', array('class' => 'sub')); ?>
				<ul>
					<li><?php echo $this->Html->link(__('Config'), array('controller' => 'users', 'action' => 'config', 'admin' => true, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Statistics'), array('controller' => 'users', 'action' => 'stats', 'admin' => true, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<?php echo $this->Common->loadPluginMenuItems('admin'); ?>
		</ul>
	</li>
	<?php endif; ?>
	<li><?php echo $this->Html->link(__('Search'), array('controller' => 'main', 'action' => 'search', 'prefix' => false, 'plugin' => false), array('class' => 'top')); ?></li>
	<li>
		<?php echo $this->Html->link(__('Create New &hellip;'), '#', array('class' => 'top', 'escape' => false)); ?>
		<ul>
			<li><?php echo $this->Html->link(__('Category'), array('controller' => 'temp_categories', 'action' => 'add', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Report'), array('controller' => 'temp_reports', 'action' => 'add', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('File'), array('controller' => 'temp_uploads', 'action' => 'add', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Signature'), array('controller' => 'signatures', 'action' => 'add', 'admin' => false, 'plugin' => false)); ?></li>
		</ul>
	</li>
	<li>
		<?php echo $this->Html->link(__('Overviews'), '#', array('class' => 'top')); ?>
		<ul>
			<li><?php echo $this->Html->link(__('Overview'), array('controller' => 'main', 'action' => 'dashboard', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('My Overview'), array('controller' => 'main', 'action' => 'my_dashboard', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Combined Views'), array('controller' => 'combined_views', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
		</ul>
	</li>
	<li>
		<?php echo $this->Html->link(__('Categories'), '#', array('class' => 'top')); ?>
		<ul>
			<li><?php echo $this->Html->link(__('Dashboard'), array('controller' => 'categories', 'action' => 'dashboard', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Add %s', __('Category')), array('controller' => 'temp_categories', 'action' => 'add', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('All Available %s', __('Categories')), array('controller' => 'categories', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Global  %s', __('Categories')), array('controller' => 'categories', 'action' => 'index_global', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Org Shared  %s', __('Categories')), array('controller' => 'categories', 'action' => 'index_org', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('My  %s', __('Categories')), array('controller' => 'categories', 'action' => 'mine', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('My  %s', __('Temp Categories')), array('controller' => 'temp_categories', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Category Groups'), array('controller' => 'category_types', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
		</ul>
	</li>
	<li>
		<?php echo $this->Html->link(__('Reports'), '#', array('class' => 'top')); ?>
		<ul>
			<li><?php echo $this->Html->link(__('Dashboard'), array('controller' => 'reports', 'action' => 'dashboard', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Add %s', __('Report')), array('controller' => 'temp_reports', 'action' => 'add', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('All Available %s', __('Reports')), array('controller' => 'reports', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Global %s', __('Reports')), array('controller' => 'reports', 'action' => 'index_global', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Org Shared %s', __('Reports')), array('controller' => 'reports', 'action' => 'index_org', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('My %s', __('Reports')), array('controller' => 'reports', 'action' => 'mine', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('My %s', __('Temp Reports')), array('controller' => 'temp_reports', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Report Groups'), array('controller' => 'report_types', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
		</ul>
	</li>
	<li><?php echo $this->Html->link(__('Assessments'), '#', array('class' => 'top')); ?>
		<ul>
			<li><?php echo $this->Html->link(__('Customer Risks'), array('controller' => 'assessment_cust_risks', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('NIH Risks'), array('controller' => 'assessment_nih_risks', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Offices'), array('controller' => 'assessment_offices', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Organizations'), array('controller' => 'assessment_organizations', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
		</ul>
	</li>
	<li>
		<?php echo $this->Html->link(__('Files'), '#', array('class' => 'top')); ?>
		<ul>
			<li><?php echo $this->Html->link(__('Add File'), array('controller' => 'temp_uploads', 'action' => 'add', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('All Available Files'), array('controller' => 'uploads', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Global Files'), array('controller' => 'uploads', 'action' => 'index_global', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Org Files'), array('controller' => 'uploads', 'action' => 'index_org', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('My Files'), array('controller' => 'uploads', 'action' => 'mine', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('My Temp Files'), array('controller' => 'temp_uploads', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('File Groups'), array('controller' => 'upload_types', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
		</ul>
	</li>
	<li>
		<?php echo $this->Html->link(__('Signatures'), '#', array('class' => 'top')); ?>
		<ul>
			<li><?php echo $this->Html->link(__('All'), array('controller' => 'signatures', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Snort Signatures'), array('controller' => 'snort_signatures', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Yara Signatures'), array('controller' => 'yara_signatures', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Signature Sources'), array('controller' => 'signature_sources', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
		</ul>
	</li>
	<li>
		<?php echo $this->Html->link(__('Imports'), array('controller' => 'imports', 'action' => 'index', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?>
		<?php echo $this->element('Utilities.menu_items', array(
			'request_url' => array('controller' => 'import_managers', 'action' => 'menu', 'admin' => false, 'plugin' => false),
		)); ?>
	</li>
	<li><?php echo $this->Html->link(__('Dumps'), array('controller' => 'dumps', 'action' => 'index', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
	<li>
		<?php echo $this->Html->link(__('Vectors'), '#', array('class' => 'top')); ?>
		<ul>
			<li><?php echo $this->Html->link(__('Dashboard'), array('controller' => 'vectors', 'action' => 'dashboard', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Vectors'), array('controller' => 'vectors', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Hostnames'), '#', array('class' => 'sub')); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'vectors', 'action' => 'hostnames', 0, 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Only Local'), array('controller' => 'vectors', 'action' => 'hostnames', 'local', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Only Remote'), array('controller' => 'vectors', 'action' => 'hostnames', 'remote', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li><?php echo $this->Html->link(__('Ip Addresses'), '#', array('class' => 'sub')); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'vectors', 'action' => 'ipaddresses', 0, 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Only Local'), array('controller' => 'vectors', 'action' => 'ipaddresses', 'local', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Only Remote'), array('controller' => 'vectors', 'action' => 'ipaddresses', 'remote', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			
			<li><?php echo $this->Html->link(__('VirusTotal Tracking'), '#', array('class' => 'sub')); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'vectors', 'action' => 'auto_tracking_vt', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Hostnames'), array('controller' => 'vectors', 'action' => 'auto_tracking_vt_hostnames', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Ip Addresses'), array('controller' => 'vectors', 'action' => 'auto_tracking_vt_ipaddresses', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Hashes'), array('controller' => 'vectors', 'action' => 'auto_tracking_vt_hashes', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			
			<li><?php echo $this->Html->link(__('VirusTotal Records'), '#', array('class' => 'sub')); ?>
				<ul>
					<li><?php echo $this->Html->link(__('Network Records'), array('controller' => 'vt_nt_records', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Related Samples'), array('controller' => 'vt_related_samples', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Detected Urls'), array('controller' => 'vt_detected_urls', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			
			<li><?php echo $this->Html->link(__('DNS Tracking'), '#', array('class' => 'sub')); ?>
				<ul>
					<li><?php echo $this->Html->link(__('Dashboard'), array('controller' => 'nslookups', 'action' => 'dashboard', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('DNS Records'), array('controller' => 'nslookups', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'vectors', 'action' => 'auto_tracking_dns', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Hostnames'), array('controller' => 'vectors', 'action' => 'auto_tracking_dns_hostnames', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Ip Addresses'), array('controller' => 'vectors', 'action' => 'auto_tracking_dns_ipaddresses', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li><?php echo $this->Html->link(__('WHOIS Tracking'), '#', array('class' => 'sub')); ?>
				<ul>
					<li><?php echo $this->Html->link(__('Dashboard'), array('controller' => 'whois', 'action' => 'dashboard', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('WHOIS Records'), array('controller' => 'whois', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'vectors', 'action' => 'auto_tracking_whois', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Hostnames'), array('controller' => 'vectors', 'action' => 'auto_tracking_whois_hostnames', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Ip Addresses'), array('controller' => 'vectors', 'action' => 'auto_tracking_whois_ipaddresses', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li><?php echo $this->Html->link(__('Nameservers'), array('controller' => 'nameservers', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Vector Groups'), array('controller' => 'vector_types', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
		</ul>
	</li>
	<li>
		<?php echo $this->Html->link(__('Contacts'), '#', array('class' => 'top')); ?>
		<ul>
			<li><?php echo $this->Html->link(__('Associated Accounts'), array('controller' => 'assoc_accounts', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'assoc_accounts', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Duplicates'), array('controller' => 'assoc_accounts', 'action' => 'duplicates', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Orphans'), array('controller' => 'assoc_accounts', 'action' => 'orphans', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li>
				<?php echo $this->Html->link(__('Fisma Systems'), array('controller' => 'fisma_systems', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?>
			</li>
			<li>
				<?php echo $this->Html->link(__('Fisma Inventories'), array('controller' => 'fisma_inventories', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?>
			</li>
			<li>
				<?php echo $this->Html->link(__('AD Accounts'), array('controller' => 'ad_accounts', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'ad_accounts', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Duplicates'), array('controller' => 'ad_accounts', 'action' => 'duplicates', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Empty'), array('controller' => 'ad_accounts', 'action' => 'empties', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Orphans'), array('controller' => 'ad_accounts', 'action' => 'orphans', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li>
				<?php echo $this->Html->link(__('SACs'), array('controller' => 'sacs', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'sacs', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Duplicates'), array('controller' => 'sacs', 'action' => 'duplicates', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Empty'), array('controller' => 'sacs', 'action' => 'empties', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Orphans'), array('controller' => 'sacs', 'action' => 'orphans', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li>
				<?php echo $this->Html->link(__('Branches'), array('controller' => 'branches', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'branches', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Duplicates'), array('controller' => 'branches', 'action' => 'duplicates', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Empty'), array('controller' => 'branches', 'action' => 'empties', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Orphans'), array('controller' => 'branches', 'action' => 'orphans', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li>
				<?php echo $this->Html->link(__('Divisions'), array('controller' => 'divisions', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'divisions', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Duplicates'), array('controller' => 'divisions', 'action' => 'duplicates', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Empty'), array('controller' => 'divisions', 'action' => 'empties', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Orphans'), array('controller' => 'divisions', 'action' => 'orphans', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li>
				<?php echo $this->Html->link(__('ORG/ICs'), array('controller' => 'orgs', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'orgs', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Duplicates'), array('controller' => 'orgs', 'action' => 'duplicates', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Empty'), array('controller' => 'orgs', 'action' => 'empties', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
		</ul>
	</li>
	<li><?php echo $this->Html->link(__('Users'), array('controller' => 'users', 'action' => 'index', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
	
	<?php echo $this->Common->loadPluginMenuItems(); ?>
	
	<?php if ($this->Common->roleCheck('manager')): ?>
	<li>
		<?php echo $this->Html->link(__('Manager Admin'), '#', array('class' => 'top')); ?>
		<ul>
			<li><?php echo $this->Html->link(__('Manage Category Groups'), array('controller' => 'category_types', 'action' => 'index', 'manager' => true, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Manage Report Groups'), array('controller' => 'report_types', 'action' => 'index', 'manager' => true, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Manage File Groups'), array('controller' => 'upload_types', 'action' => 'index', 'manager' => true, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('Manage Users'), array('controller' => 'users', 'action' => 'index', 'manager' => true, 'plugin' => false)); ?></li>
			<?php echo $this->Common->loadPluginMenuItems('manager'); ?>
		</ul>
	</li>
	<?php endif; ?>
	<?php endif; ?>
</ul>
<?php endif; ?>