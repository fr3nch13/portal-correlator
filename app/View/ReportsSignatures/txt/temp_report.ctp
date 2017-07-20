<?php 
foreach ($reports_signatures as $i => $reports_signature)
{
	if(isset($reports_signature['YaraSignature']['compiled']))
		echo $reports_signature['YaraSignature']['compiled']. "\n\n";
	if(isset($reports_signature['SnortSignature']['compiled']))
		echo $reports_signature['SnortSignature']['compiled']. "\n\n";
}