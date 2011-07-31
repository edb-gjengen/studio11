<?php

/*
Plugin Name: Twitter Feed
Plugin URI: http://community.plus.net/opensource/twitter-wordpress/
Description: Selects and displays the latest blog posts from Twitter for specified users.
Version: 1.2
Author: Plusnet Plc - Developer Responsible: James Tuck (Development Team)
Author URI: http://community.plus.net/opensource/
*/

/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// PLEASE NOTE: This plugin requires the use of the php curl library. 
//              Please ensure you have this installed

require_once dirname( __FILE__ ) . '/includeTwitter.php';
require_once dirname( __FILE__ ) . '/../../../wp-config.php';
require_once dirname( __FILE__ ) . '/twitterScript.php';

function startTwitterWidget()
{
	if ( function_exists( 'register_sidebar_widget' ) )
	{
		function widget_twitterblog( $args )
		{
			$before_widget = '';
			$before_title = '';
			$after_title = '';
			$after_widget = '';

			if ( is_array( $args ) )
			{
				if ( array_key_exists( 'before_widget', $args ) )
				{
					$before_widget = $args['before_widget'];
				}
				if ( array_key_exists( 'before_title', $args ) )
				{
					$before_title = $args['before_title'];
				}
				if ( array_key_exists( 'after_title', $args ) )
				{
					$after_title = $args['after_title'];
				}
				if (array_key_exists( 'after_widget', $args ) )
				{
					$after_widget = $args['after_widget'];
				}

				echo $before_widget;
				echo $before_title. 'Twitter Blog' . $after_title;

				displayTwitterPlugin();

				echo $after_widget;
			}
		}
		register_sidebar_widget( 'Twitter Blog', 'widget_twitterblog' );
	}
}

function displayTwitterPlugin($bolShowAllTweets = false)
{
	$strUserIdsValue = get_option( TWITTER_USER_IDS );
	$strIsShow = get_option( TWITTER_SHOW_STATUS );
	$strShowLogo = get_option( TWITTER_SHOW_LOGO );
	$strShowAtUsername = get_option( TWITTER_SHOW_AT_USERNAME );
	$intAllTweetsPageId = (int) get_option( TWITTER_PAGE_ID );

	$intLimitNumber = (int) get_option( TWITTER_RESTRICT_TWEETS );

	// start: retrieve user data

	$arrUserData = get_option( TWITTER_USER_DATA );	

	// end: retrieve user data

	$arrNames = explode( ',', $strUserIdsValue );

	$arrIsShow = explode( ',', $strIsShow );

	if ( is_array( $arrUserData ) )
	{

		// start: display header

		echo '
			<div id="twitterSpace">

				<div id="twitterHeader">
					<span id="twitterImage">
						<img class="main" src="'.get_option( 'siteurl' ).'/wp-content/plugins/twitter/twitterImage.gif"/>
					</span>';

		$intUserDataCount = count( $arrUserData );

		$intRunCount = 0;

		$intShowCount = count( $arrIsShow );

		$intIndexCount = 0;

		/***** Revised Section ********/

		for ( $intIndex = 0; $intIndex < $intUserDataCount; $intIndex++ )
		{
			if ( null == $arrUserData[$intIndex]['tweet'] )
			{
				$intRunCount++;
			}

			if ( 'unchecked' == $arrIsShow[$intIndex] )
			{
				$intIndexCount++;
			}

			$arrUserData[$intIndex]['show'] = $arrIsShow[$intIndex];
		}

		/***** ****** ********/

		if ( ( $intUserDataCount != $intRunCount ) && ( $intShowCount != $intIndexCount ) )
		{
			echo '
				<span id="toggleSpace">
				</span>';
		}

		echo '
			</div>';

		// end: display header

		// start: put into order by date

		function compareDates( $a, $b )
		{
			return ( $a['date'] > $b['date'] ) ? -1 : 1;
		}

		usort( $arrUserData, 'compareDates' );

		// end: put into order by date

		// start: display individual blogs

		if ( ( '' == $intLimitNumber ) || ( true == $bolShowAllTweets ) )
		{
			$intLimitNumber = count( $arrUserData );
		}

		$intCount = 0;

		foreach ( $arrUserData as $strElement )
		{
			if ( ( null != $strElement['tweet'] ) && ( 'checked' == $strElement['show'] ) )
			{
				if ( preg_match( TWITTER_USERNAME_REGEX, $strElement['name'] ) && 
					( preg_match( TWITTER_SOURCE_PATTERN_C, $strElement['avatar'] ) || 
					  preg_match( TWITTER_SOURCE_PATTERN_A, $strElement['avatar'] ) ||
					  preg_match( TWITTER_SOURCE_PATTERN_B, $strElement['avatar'] )	
					)
				   )
				{
					echo '
						<div class="tweetSpace">
							<div class="tweet">
								<div class="tweetPic">
									<img src="'. $strElement['avatar'] .'"
										alt="avatar picture"/>
								</div>

								"'. make_clickable( $strElement['tweet'] ) .'"
								
								<br />
								
								<span class="screenName">
									
									<a href="http://twitter.com/'. $strElement['name'] .'">'
										. $strElement['name'] .'
									</a>
								</span>

								<br />
								<br />

								<div class="dateText">';

					if ( 0 != $strElement['date'] )
					{
						echo '--- '.strftime( "%a %d %b %Y %R", $strElement['date'] ).' ---';
					}
					else
					{
						echo 'Date not found';
					}
					echo '
								</div>
							</div>
						</div>';
				}
			}


			if ( $intCount == $intLimitNumber )
			{
				break;
			}

			$intCount++;
		}

		// end: display individual blogs

		if ( !is_page( $strUrl ) )
		{
			if ( null != $intAllTweetsPageId )
			{
				$strUrl = get_permalink( $intAllTweetsPageId );
	
				echo '
						<div class="viewAll">
							<a href="'.$strUrl.'">View All Tweets</a>
						</div>';
			}
		}

		// start: display "Created By" footer 

		if ( 'checked' == $strShowLogo )
		{
			echo '	
				<div class="builtByArea">
					Created by <a href="http://community.plus.net/opensource/" title="Plusnet Broadband">Plusnet</a>
				</div>';
		}

		// end: display "Created By" footer

		echo '
			</div>';
	}

}

add_action( 'admin_head','includeTwitterCSS' );
add_action( 'wp_head', 'includeTwitterComponents' );
add_action( 'admin_menu', 'twitterAddOptionPage' );
add_action( 'init','startTwitterWidget' );

add_filter( 'the_content', 'processKeyword', '124544774' );

function processKeyword( $strContent )
{
	$strContent = preg_replace( "/\[twitter\]/ise", "displayTwitterPlugin(true)", $strContent, -1, $intMatches );

	if ( $intMatches > 0 )
	{
		$strContent = '<script type="text/javascript">

							$(document).ready(function(){

							$(".tweetSpace").show();
							});
						</script>' . $strContent;
	}

	return $strContent;
}

function twitterAddOptionPage()
{
	add_submenu_page( 'plugins.php','Twitter Control', 'Twitter Control', 8, __FILE__, 'twitter_options_page' );
}

function includeTwitterCSS()
{
	echo '<link rel="stylesheet" type="text/css" href="'.get_option( 'siteurl' ).'/wp-content/plugins/twitter/twitterStyle.css"/>';
}

// start: jQuery function to control the expansion and collapsing of the main app window

function includeTwitterComponents()
{
	echo '<link rel="stylesheet" type="text/css" href="'.get_option( 'siteurl' ).'/wp-content/plugins/twitter/twitterStyle.css"/>';

	if ( is_front_page() )
	{
		echo '<script type="text/javascript">

				jQuery(document).ready(function(){

				jQuery(".tweetSpace").hide();
				jQuery(".builtByArea").hide();
				jQuery(".viewAll").hide();
				jQuery("#toggleSpace").html(\'<img id="toggleLink" src="'.get_option( 'siteurl' ).'/wp-content/plugins/twitter/arrow_down.gif" alt="down" title="down"/>\');

				// toggles the slickbox on clicking the noted link
				jQuery("#toggleLink").click(function()
				{
					if(jQuery(".tweetSpace").is(":visible"))
					{
						jQuery(".viewAll").toggle(400);
						jQuery(".builtByArea").toggle(400);
						jQuery(".tweetSpace").toggle(400);
						jQuery("#toggleLink").attr("src", "'.get_option( 'siteurl' ).'/wp-content/plugins/twitter/arrow_down.gif");
						jQuery("#toggleLink").attr("alt", "down");
						jQuery("#toggleLink").attr("title", "down");
					}
					else
					{
						jQuery(".viewAll").toggle(400);
						jQuery(".builtByArea").toggle(400);
						jQuery(".tweetSpace").toggle(400);
						jQuery("#toggleLink").attr("src", "'.get_option( 'siteurl' ).'/wp-content/plugins/twitter/arrow_up.gif");
						jQuery("#toggleLink").attr("alt", "up");
						jQuery("#toggleLink").attr("title", "up");
					}
					return false;
				});

			});

			</script>';
	}
}

// end: jQuery function to control the expansion and collapsing of the main app window

function twitter_options_page()
{
	$strUserIdsForScript = get_option( TWITTER_USER_IDS );

	$arrNames = explode( ',', $strUserIdsForScript );

	$arrUseNames = array();

	$strUserIdsValue = get_option( TWITTER_USER_IDS );

	$strIsShow = get_option( TWITTER_SHOW_STATUS );

	$strShowLogo = get_option( TWITTER_SHOW_LOGO );

	$strShowAtUsername = get_option( TWITTER_SHOW_AT_USERNAME );

	$intLimitNumber = (int) get_option( TWITTER_RESTRICT_TWEETS );

	$intAllTweetsPageId = (int) get_option( TWITTER_PAGE_ID );

	$arrPagesInfo = get_pages();

	$arrPageUrls = array();

	if ( false == empty( $arrPagesInfo ) )
	{
		foreach( $arrPagesInfo as $element )
		{
			$arrPageIds[] = $element->ID;
		}
		
	}

	if ( 'post' === strtolower( $_SERVER['REQUEST_METHOD'] ) )
	{
		if ( isset( $_POST['restrictupdate'] ) )
		{
			if ( isset( $_POST['restrictnumber'] ) )
			{
				$intLimitNumber = $_POST['restrictnumber'];
				update_option( TWITTER_RESTRICT_TWEETS, $intLimitNumber );
				echo '<div id="message" class="updated fade"><p><strong>Info updated.</strong></p></div>';
			}
		}

		if ( isset( $_POST['otheroptionsupdate'] ) )
		{
			// start: set to / not to display "@username" blogs

			if ( $_POST['at'] )
			{
				$strShowAtUsername = 'checked';
			}
			else
			{
				$strShowAtUsername = 'unchecked';
			}

			// end: set to / not to display "@username" blogs

			// start: set to / not to display "created by" footer

			if ( $_POST['logo'] )
			{
				$strShowLogo = 'checked';
			}
			else
			{
				$strShowLogo = 'unchecked';
			}

			// end: set to / not to display "created by" footer

			update_option( TWITTER_SHOW_LOGO, $strShowLogo );
			update_option( TWITTER_SHOW_AT_USERNAME, $strShowAtUsername );

			echo '<div id="message" class="updated fade"><p><strong>Info updated.</strong></p></div>';

		}

		if ( isset( $_POST['usernameupdate'] ) )
		{
			if ( isset( $_POST[TWITTER_USER_IDS] ) )
			{
				if ( !( empty( $_POST[TWITTER_USER_IDS] ) ) )
				{
					if ( ( empty( $strUserIdsValue ) ) )
					{
						$strUserIdsValue = $_POST[TWITTER_USER_IDS];
						$strIsShow = 'checked';
					}
					else
					{
						$strUserIdsValue = $strUserIdsValue.','.$_POST[TWITTER_USER_IDS];
						$strIsShow = $strIsShow.',checked';
					}
				}

				$arrRemoveValues = array();

				$arrNamesTemp = explode( ',', $strUserIdsValue );

				// start: collect the list of users to delete

				foreach ( $arrNamesTemp as $strElement )
				{
					if ( 'delete' == $_POST['delete-'.$strElement] )
					{
						$arrRemoveValues[] = $strElement;
					}
				}

				// end: collect the list of users to delete

				$arrResult = array_diff( $arrNamesTemp, $arrRemoveValues );

				if ( isset( $_POST['updateshownames'] ) )
				{
					// start: set to / not to display blogs by a specific user

					$arrShowValues = array();

					foreach ( $arrResult as $strElement )
					{
						if ( $strElement == $_POST['checkbox-'.$strElement] )
						{
							$arrShowValues[] = 'checked';
						}
						else
						{
							$arrShowValues[] = 'unchecked';
						}
					}

					// end: set to / not to display blogs by a specific user

					$strIsShow = implode( ",", $arrShowValues );

				}

				$strUserIdsValue = implode( ",", $arrResult );

				// start: Save the posted values in the database

				update_option( TWITTER_USER_IDS, $strUserIdsValue );
				update_option( TWITTER_SHOW_STATUS, $strIsShow );

				// end: Save the posted values in the database

				echo '<div id="message" class="updated fade"><p><strong>Info updated.</strong></p></div>';
			}
		}

		if ( isset( $_POST['url'] ) )
		{
			$intAllTweetsPageId = $_POST['urllist'];

			if ( -1 == $intAllTweetsPageId )
			{
				$intAllTweetsPageId = null;
			}

			update_option( TWITTER_PAGE_ID, $intAllTweetsPageId );

			echo '<div id="message" class="updated fade"><p><strong>URL Updated.</strong></p></div>';
		}

		if ( isset( $_POST['hidden'] ) )
		{
			$bolScriptHasRun = twitterScriptMain();

			if ( true == $bolScriptHasRun )
			{
				echo '<div id="message" class="updated fade"><p><strong>Blogs Updated.</strong></p></div>';
			}
			else
			{
				echo '<div id="message" class="updated fade"><p><strong>WARNING: Blogs have NOT been Updated.</strong></p></div>';
			}
		}

	}

	displayTwitterOptionsPage( $strUserIdsValue, $strIsShow, $strShowAtUsername, 
	                           $strShowLogo, $intLimitNumber, $arrPageIds, $intAllTweetsPageId );

}

function displayTwitterOptionsPage( $strUserIdsValue, $strIsShow, $strShowAtUsername, 
	                                $strShowLogo, $intLimitNumber, $arrPageIds, $intAllTweetsPageId )
{
	// start: setup default setting for showing the created by footer

	if ( null == $strShowLogo || '' == $strShowLogo )
	{
		$strShowLogo = "checked";
	}

	// end: setup default setting for showing the created by footer

	// start: display options on page

	$arrNames = explode( ',', $strUserIdsValue );

	$arrIsShow = explode( ',', $strIsShow );

	echo '
		<div class="wrap">

			<h2> Twitter Control </h2>

			<form name="usernameform" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] ).'">
				<div class="options">
					<table border="0" cellpadding="5">
					<tr>
					<td>
					<table border="0" cellpadding="0" cellspacing="0" class="usernametable">';

					if ( '' != $arrNames[0] )
					{
						echo '
							<caption><strong>--- List Of Usernames ---</strong></caption>
							<tr><th>Show</th><th class="twittername">Username</th><th>&nbsp;</th></tr>';

						$intCount = 0;

						foreach ( $arrNames as $strElement )
						{
							$strElement = htmlentities( $strElement );

							if ( '' != $strElement )
							{
								echo '
								    <tr><td class="twittercheck"><input type="checkbox" 
								            name="checkbox-'.$strElement.'" 
								            value="'.$strElement.'"'. 
								                     htmlentities( $arrIsShow[$intCount] ).'/></td>

								    <td class="twittername">'.$strElement.'</td>
								    <td class="twitterdelete">
								       <input type="submit" name="delete-'.$strElement.'" 
								           value="delete" style="cursor:pointer;"/></td></tr>';
							}

							$intCount++;
						}
						echo '
							<tr><td class="twitterupdate">
								<input type="hidden" name="updateshownames" value="true" />
								<p class="submit">
									<input type="submit" name="usernameupdate" value="update" />
									<br />
								</p>
								<td></td>
								<td></td>
							</tr>';
					}
					else
					{
						echo '
							<caption><strong>No Active Usernames</strong></caption>';
					}
				echo '
					<tr>
						<td><p>New Username</p></td>
						<td><input type="text" name="'.TWITTER_USER_IDS.'" value="" size="25"></td>
						<td class="twitteradd">
							<input type="hidden" name="usernameupdate" value="true" />
							<input type="submit" name="submit" value="add" style="cursor:pointer;"/></td>
					</tr>

					</table>
					</td>
			</form>

			<form name="otheroptionsform" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] ).'">
					<td valign="top">
					<table border="0" cellpadding="0" cellspacing="0" class="otheroptionstable">
						<caption><strong>--- Other Options ---</strong></caption>
						<tr>
							<th>Show</th>
							<th class="twittername">Option</th>

							</tr>

							<tr><td class="twittercheck"><input type="checkbox" name="at"
							value="at"'.htmlentities( $strShowAtUsername ).'/></td>
							
							<td class="twittername">"@username" tagged posts</td>
							</tr>

							<tr><td class="twittercheck"><input type="checkbox" name="logo" 
							value="logo"'.htmlentities( $strShowLogo ).'/></td>

							<td class="twittername">"Created by" text footer</td>

						</tr>

						<tr>
							<td class="twitterupdate">
								<input type="hidden" name="otheroptionsupdate" value="true" />
								<p class="submit">
									<input type="submit" name="updateotheroptions" value="update" />
									<br />
								</p></td>
							<td></td>
						</tr>

					</table>
					</td>
			</form>

			<form name="restricttweetsform" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] ).'">
					<td valign="top">
						<table border="0" cellpadding="0" cellspacing="0" class="restricttweetstable">
						<caption><strong>--- Restrict Tweets ---</strong></caption>
						<tr>
							<th class="twittername"> Restrict Number Of Tweets</th>
						</tr>

						<tr>
							<td><input type="text" name="restrictnumber" 
									   value="'.htmlentities( $intLimitNumber ).'" size="10">
							</td>
						</tr>

						<tr>
							<td class="twitterupdate">
								<input type="hidden" name="restrictupdate" value="true" />
								<p class="submit">
									<input type="submit" name="updaterestrict" value="update" />
									<br />
								</p></td>
							<td></td>
						</tr>

						</table>
					</td>

					</tr>
					</table>

				</div>
			</form>

			<h3>All Tweets URL</h3>

			<form name="alltweetsform" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] ).'">
				<p>If you have setup an "All Tweets" page then please select it from the following list so that the main Twitter app can link to the correct page. For information on setting up an "All Tweets" page please see the readme.txt file that accompanies this plugin.</p>
				<select name="urllist">
					<option value="-1">link disabled (default)</option>';

				foreach ( $arrPageIds as $element )
				{
					$strUrl = get_permalink( $element );

					if ( $intAllTweetsPageId == $element )
					{
						echo '<option
						          selected="yes"
						          value="'.htmlentities( $element ).'">
						          '.htmlentities( $strUrl ).
						     '</option>';
					}
					else
					{
						echo '<option value="'.htmlentities( $element ).'">'.htmlentities( $strUrl ).'</option>';
					}
				}
			echo '
				</select>
				<input type="hidden" name="url" value="true" />
				<p class="submit">
				<input type="submit" name="updateurl" value="Update URL" />
				<br />
				</p>
				<br />
				<br />
			</form>

			<h3>Update Blogs</h3>

			<form name="form2" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] ).'">

				<p>To populate the cache file with your current registered user\'s blogs, click on the button opposite.</p>
				<p><strong>Please note: </strong>you will need to perform this task to reflect any changes made on registered user\'s blogs.</p>

				<input type="hidden" name="hidden" value="true" />
				<p class="submit">
				<input type="submit" name="Refresh" value="Update Blogs" />
				<br />This process may take a few minutes to run,<br /> especially if you are collecting a large number of blogs.
				</p>

			</form>

		</div>';

	// end: display options on page
}
?>
