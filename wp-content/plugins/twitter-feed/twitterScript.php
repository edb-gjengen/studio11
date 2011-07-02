<?php

/**
 * twitterScriptMain 
 *
 * retrieves Twitter blog data from the Twitter website and stores it in the local Wordpress database
 *
 * 
 * @access public
 * @returns boolean true = successful, false = some error occured
 */

require_once dirname( __FILE__ ) . '/includeTwitter.php';
require_once dirname( __FILE__ ) . '/../../../wp-config.php';

function twitterScriptMain()
{
	// start: check for existance of the curl library

	if ( !function_exists( 'curl_init' ) )
	{
		echo 'Warning: The php curl library is missing. 
		      Please check you have the curl library installed.';

		return false;
	}

	$strUserIdsForScript = get_option( TWITTER_USER_IDS );

	$strShowAtUsername = get_option( TWITTER_SHOW_AT_USERNAME );

	if ( 'checked' == $strShowAtUsername )
	{
		$bolShowAtUsername = true;
	}
	else
	{
		$bolShowAtUsername = false;
	}

	// start: check that all returned values have been set

	if ( false == $strUserIdsForScript || false == $strShowAtUsername )
	{
		echo 'Key values have not been set. 
			Please visit the admin control pannel and enter the required values.';

		return false;
	}

	// end: check that all returned values have been set
	
	$arrNames = explode( ',', $strUserIdsForScript );

	$bolWriteToFile = true;

	$arrSaveData = array();

	$arrResponse = array();

	$intIndex = 0;

	foreach ( $arrNames as $strElement )
	{
		if ( preg_match( TWITTER_USERNAME_REGEX, $strElement ) )
		{
			$strResponse = fetchXML( $strElement );

			if( false == $strResponse )
			{
				return false;
			}

			$strXMLFormat = substr( $strResponse, 2, 34 );

			// start: check the returned data is in an xml format

			if ( 'xml version="1.0" encoding="UTF-8"' != $strXMLFormat )
			{
				echo 'Oops, the Twitter site appears to be down at the moment. 
					Please try again later.'."\n";

				return false;

			}
			// end: check the returned data is in an xml format

			// start: retrieve data from the xml data structure

			try
			{
				$objXml = new SimpleXMLElement( $strResponse );

				if ( $objXml->hash->error == 'User not found' )
				{
					$arrResponse[$intIndex] = null;
				}
				else
				{
					foreach ( $objXml->status as $status )
					{
						if ( ( false == $bolShowAtUsername && 
                               '@' != substr( $status->text, 0, 1 ) ) || 
						     ( true ==  $bolShowAtUsername ) 
						   )
						{
							$arrResponse[$intIndex] = array('tweet' => (string) $status->text,
							                                'name' => (string) $status->user->screen_name,
							                                'avatar' => (string) $status->user->profile_image_url,
							                                'date' => (string) strtotime( $status->created_at )
							                               );
							break;
						}
					}
				}
			}

			// end: retrieve data from the xml data structure

			catch( Exception $e )
			{
				echo 'Oops, the Twitter site appears to be down at the moment. 
				Please try again later. Error: '.$e."\n";

				return false;
			}

		}
		else
		{
			echo 'One or more Tweets have been disabled for XSS security reasons.'."\n";

			return false;
		}

		$intIndex++;
	}

	update_option( TWITTER_USER_DATA, $arrResponse );

	return true;

} // end: MAIN FUNCTION

function fetchXML( $strElement )
{
	$strUrl = 'http://twitter.com/statuses/user_timeline/'.$strElement.'.xml';
			
	$resCurl = curl_init( $strUrl );

	if ( false == $resCurl )
	{
		echo 'Twitter Curl Function failed to initialise.';
		return false;
	}

	curl_setopt( $resCurl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $resCurl, CURLOPT_HEADER, false );
	curl_setopt( $resCurl, CURLOPT_CONNECTTIMEOUT, 3 );
	curl_setopt( $resCurl, CURLOPT_TIMEOUT, 300 );
	$strRsp = curl_exec( $resCurl );

	if ( false == $strRsp )
	{
		$strError = curl_error( $resCurl );
		echo 'Twitter Curl Function failed to execute: '.$strError;
		return false;
	}

	curl_close( $resCurl );

	return $strRsp;
}

?>
