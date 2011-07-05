=== Debug Queries ===
Contributors: Bueltge
Donate link: http://bueltge.de/wunschliste/
Tags: query, queries, database, performance, analyse, sql, debug, tuning
Requires at least: 1.5
Tested up to: 3.1
Stable tag: 1.0.1

List query-actions only for admins; for debug purposes

== Description ==
List query-actions only for admins; for debug purposes. See all queries on the frontend of the blog and find the slowest part.

Since version 1.0.0 the plugin disable the MySQL query cache for the current connected session. That will show you the real execution time for a query. To disable this behavior set the constant `QUERY_CACHE_TYPE_OFF` to `FALSE`.


The plugin is perfect for WordPress developers, plugin and theme developers and site administrators who are trying to find out why the blog is too slow.

Please visit [the official website](http://bueltge.de/wordpress-performance-analysieren-plugin/558/ "Debug Queries") for further details and the latest information on this plugin.

= More Plugins = 
Please see also my [Premium Plugins](http://wpplugins.com/author/malo.conny/). Maybe you find an solution for your requirement.

= Interested in WordPress tips and tricks =
You may also be interested in WordPress tips and tricks at [WP Engineer](http://wpengineer.com/) or for german people [bueltge.de](http://bueltge.de/) 

== Installation ==
1. Unpack the download-package
1. Upload all files to the `/wp-content/plugins/` directory, without folder
1. Activate the plugin through the 'Plugins' menu in WordPress
1. That's it! Now you are all set to go on to thr frontend of your weblog and see the list of queries. You find the querie-analysis on end of the blog. Only logged administrators can see the result.

See on [the official website](http://bueltge.de/wordpress-performance-analysieren-plugin/558/ "Debug Queries").

== Screenshots ==
1. List queries in frontend on a example blog

== Other Notes ==
= Params =
The plugin has since version 1.0.0 a constant to disable the mySQL Session cache. On defoult is the value on true and the cache is off for an better analyse! For change this, set the constant `QUERY_CACHE_TYPE_OFF` to `FALSE`.

= Example =
This is a example for analysis.

`# Time: 0.00198888778687
Query: SELECT option_name, option_value FROM wp_options WHERE autoload = 'yes'
Call from: require, require_once, require_once, require_once, is_blog_installed, wp_load_alloptions
# Time: 0.000695943832397
Query: UPDATE `wp_options` SET `option_value` = 'a:3:{i:0;b:0;s:25:\"adminimize/adminimize.php\";a:2:{i:0;O:10:\"adminimize\":1:{s:12:\"wp_filter_id\";i:0;}i:1;s:12:\"on_deinstall\";}s:31:\"debug_queries/debug_queries.php\";a:2:{i:0;O:12:\"DebugQueries\":1:{s:12:\"wp_filter_id\";i:0;}i:1;s:10:\"deactivate\";}}' WHERE `option_name` = 'uninstall_plugins'`
`Call from: require, require_once, require_once, require_once, include_once, adminimize->adminimize, register_uninstall_hook, update_option`
`
. . .
`
`   Total query time: 0.0155501365662 for 23 queries.
    Total num_query time: 0.392 for 23 num_queries.`

= Acknowledgements =
Thanks to Joost de Valk on [yoast.com](http://yoast.com/ "yoast.com") for small modifed on the plugin for some extra info.

= Licence =
Good news, this plugin is free for everyone! Since it's released under the GPL, you can use it free of charge on your personal or commercial blog. But if you enjoy this plugin, you can thank me and leave a [small donation](http://bueltge.de/wunschliste/ "Wishliste and Donate") for the time I've spent writing and supporting this plugin. And I really don't want to know how many hours of my life this plugin has already eaten ;)

== Frequently Asked Questions ==

= Where can I get more information? =
Please visit [the official website](http://bueltge.de/wordpress-performance-analysieren-plugin/558/ "Debug Queries") for the latest information on this plugin.

= I love this plugin! How can I show the developer how much I appreciate his work? =
Please visit [the official website](http://bueltge.de/wordpress-performance-analysieren-plugin/558/ "Debug Queries") and let him know your care or see the [wishlist](http://bueltge.de/wunschliste/ "Wishlist") of the author.

== Changelog ==
= v1.0.1 (03/23/2011) =
* fixes for changed functions of WP 3.1
* changes on markup for better use on frontend
* small changes for WP Codex

= v1.0.0 (12/01/2010) =
* Feature: add constant for disable mySQL Session Cache; The plugin has since version 1.0.0 a constant to disable the mySQL Session cache. On defoult is the value on true and the cache is off for an better analyse! For change this, set the constant `QUERY_CACHE_TYPE_OFF` to `FALSE`.

= v0.6 (01/19/2010) =
* Convert all applicable characters in queries to HTML entities 
