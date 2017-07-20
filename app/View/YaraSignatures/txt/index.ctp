<?php 
foreach ($yara_signatures as $i => $yara_signature)
{
	if(isset($yara_signature['YaraSignature']['compiled']))
		echo $yara_signature['YaraSignature']['compiled']. "\n\n";
	if(isset($yara_signature['SnortSignature']['compiled']))
		echo $yara_signature['SnortSignature']['compiled']. "\n\n";
}