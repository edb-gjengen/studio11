=== Twitter Feed ===
Contributors: Plusnet
Tags: twitter, feed, blog, tweet, multiple
Requires at least: 2.5.1
Tested up to: 2.8.4
Stable tag: 2.0

The Twitter Feed plugin makes including and linking to tweets on multiple Twitter accounts simple and flexible.

== Description ==

Admin Control Panel

The control functions such as connecting to a Twitter account and storing information on the tweets/blogs, can be accessed through the "Twitter Control" GUI in the Wordpress admin panel.

Any changes in the currently active Twitter account tweets can be immediately updated by clicking on the "Update Blogs" button.

Order, Prioritisation and PM Filtering

The application will display the latest tweet from all of the active accounts and then displays those latest tweets in date order. So the newest tweet across all of the accounts will always be shown first. It is also possible to switch between allowing/disallowing PM (Private Message) tweets to be included in the display. 

Separate All Tweets Page

If a user has applied restrictions on the amount of Tweets displayed in the application then they can create a separate Wordpress page to hold the entire tweet output of all the active Twitter accounts.


Information Cache

Running the relevant Twitter API function calls can sometimes a few minutes to fully execute. All information is stored in a cache data held locally, so that it minimises the chance of performance loss on web pages. 

Cron Support

If users wish to have regular/automatic updates of their Twitter account tweets, then that is achievable through creating a crontab entry, or similar automation mechanism, to execute the supplied "runTwitterScript.php" file.

Specific styling using CSS

A CSS file is supplied to provide a starting point for displaying the tweets, but changes can be made to suit individual preference.


== Installation ==

1. Extract the "twitter-feed" folder from the downloaded file and put it in your Wordpress "/wp-content/plugins/" folder so that the resulting path is "/wp-content/plugins/twitter-feed/".

2. The plugin should now be viewable in the Wordpress admin panel under the "Plugins -> Plugins" menu option. The plugin is entitled "Twitter Feed" and can be activated in the normal way by clicking "Activate" in the far right column. 

3. First, click on the "Twitter Control" option found within the main "Plugin" menu, and you will be directed to the Twitter Control panel. From here you should be able to select which Twitter accounts to use, how many tweets you want to display, and various other options.

4. If you are restricting the amount of tweets on the front page, you can have a separate Wordpress page displaying all the tweets from currently active accounts. To do this simply create a new page, give it a title/content etc and then add

   [twitter]

 exactly as it is written above including the square brackets, to add the Twitter application to the page. You will also need to select the page from the drop down menu in the admin panel, so that the plugin knows which page to link to.

5. Depending on how you use and implement Wordpress/your website, you may have to use the widget control menu to position where the Twitter application will appear and be utilised. To do this simply visit the "Presentation -> Widgets" menu option, and position the plugin where ever you like. In some cases it will be necessary to perform this task for the plugin to appear on your web page.

== Frequently Asked Questions ==

= How does the plugin connect to Twitter? =

It uses the PHP cURL library to connect to Twitter's API.

= Can the supplied plugin be re-styled? =

Yes, as mentioned previously, a CSS style sheet is provided to give a basic layout for the plugin. Please feel free to modify this to your heart's content to get the presentation you are after. However, this may require you to have some prior knowledge of HTML and CSS.

= Is it possible to automate the tweet updates? =

Yes, we have provided a PHP file called "runTwitterScript.php" that is the file that should be run if you want to run an automated/scheduled update of your tweetss. Essentially it performs the same task as clicking the "Update Blogs" button but automatically. This process is recommended for the best results with this plugin.

= Will I experience any permission issues with the data cache storage/usage? =

No, the plugin has been designed to automatically save the data cache to the Wordpress database, this should prevent any file/folder permission issues.

== Screenshots ==

1. This is a screenshot of the application running on Wordpress' front page - screenshot-1.png
2. This is a screenshot of the Twitter Control screen in Wordpress' admin panel - screenshot-2.png

== File Listing ==

All files, both PHP and CSS, can be found within the main extracted plugin folder.

Within the folder "twitter-feed" you should find the following 13 files:-

1. twitterScript.php - the file that will interface with the Twitter API (required)
2. twitterPlugin.php - the display file that Wordpress will interface with (required)
3. inlcudeTwitter.php - used for commonly used PHP variables and functions (required)
4. runTwitterScript.php - used for cron or other photo data update automation control (optional usage but recommended)
5. twitterStyle.css - used for styling the presentation of the tweets (required, but can be modified)
6. arrow_down.gif - used to expand the main application
7. arrow_up.gif - used to collapse the main application
8. twitterImage.gif - used in the header
9. twitterpageheader.gif - used in the header
10. screenshot-1.png - image of the application running on a Wordpress front page
11. screenshot-2.png - image of the Twitter Control Panel in Wordpress
12. readme.txt - this information file
13. COPYING - a copy of the license terms for this release

For further support or information please visit: http://community.plus.net/opensource/
