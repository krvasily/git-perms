<?php

$permsFile = '.perms';

if(empty($argv[1]))
{
	echo "set option \"store\" or \"apply\"\n";
}
elseif($argv[1] == 'store')
{
	$fp = fopen($permsFile, 'w');
	storeFilePerms('.');
}
elseif($argv[1] == 'apply')
{
	if(file_exists($permsFile))
	{
		$fp = fopen($permsFile, 'r');
		applyFilePerms();
	}
}


function applyFilePerms() 
{
	global $fp;

	while(!feof($fp))
	{
		$data = explode(';', rtrim(fgets($fp)));
		if(count($data) == 2)
		{
			echo $data[0]."; ".$data[1]."\n";

			if(!file_exists($data[0]))
			{
				mkdir($data[0], $data[1], true);
			}
			else
			{
				chmod($data[0], octdec($data[1]));
			}
		}
	}
}

function storeFilePerms($dir)
{
	global $fp;

	if(is_dir($dir))
	{
	    if($dh = opendir($dir))
	    {
	        while(($fileName = readdir($dh)) !== false)
	        {
	            if($fileName != '.' && $fileName != '..' && $fileName != '.git' && $fileName != '.svn')
	            {
	            	$filePath = $dir."/".$fileName;

	            	$perms = substr(sprintf('%o', fileperms($filePath)), -4);
	            	if($perms != '0755' && $perms != '0644')
	            	{
	            		fputs($fp, $filePath.";".$perms."\n");
	            	}

	            	if(is_dir($filePath))
	            	{
	            		storeFilePerms($filePath);
	            	}
	            }
	        }

	        closedir($dh);
	    }
	}
}


?>