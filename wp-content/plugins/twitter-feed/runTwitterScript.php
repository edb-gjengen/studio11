<?php

/*
	Twitter Feed
	Run Script File
	-allows external processes (such as an automated cron entry) to run the Twitter Script file

	Built by Plusnet
	http://community.plus.net/open-source/

	Developer Responsible: James Tuck (Web Development Team)
*/


	require_once dirname( __FILE__ ) . '/twitterScript.php';
	require_once dirname( __FILE__ ) . '/../../../wp-config.php';

	$bolScriptHasRun = twitterScriptMain();

	if ( false == $bolScriptHasRun )
	{
		exit("ERROR: This script failed to run correctly.");
	}
?>
