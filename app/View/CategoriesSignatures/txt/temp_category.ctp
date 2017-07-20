<?php 
foreach ($categories_signatures as $i => $categories_signature)
{
	if(isset($categories_signature['YaraSignature']['compiled']))
		echo $categories_signature['YaraSignature']['compiled']. "\n\n";
	if(isset($categories_signature['SnortSignature']['compiled']))
		echo $categories_signature['SnortSignature']['compiled']. "\n\n";
}