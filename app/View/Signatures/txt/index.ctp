<?php 
foreach ($signatures as $i => $signature)
{
	if(isset($signature['YaraSignature']['compiled']))
		echo $signature['YaraSignature']['compiled']. "\n\n";
	if(isset($signature['SnortSignature']['compiled']))
		echo $signature['SnortSignature']['compiled']. "\n\n";
}