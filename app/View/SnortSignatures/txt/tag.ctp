<?php 
foreach ($snort_signatures as $i => $snort_signature)
{
	if(isset($snort_signature['YaraSignature']['compiled']))
		echo $snort_signature['YaraSignature']['compiled']. "\n\n";
	if(isset($snort_signature['SnortSignature']['compiled']))
		echo $snort_signature['SnortSignature']['compiled']. "\n\n";
}