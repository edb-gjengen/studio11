=== Most Popular Posts ===
Contributors: wesg
Donate link: http://www.wesg.ca/2008/08/wordpress-widget-most-popular/#donate
Tags: widget, most popular, comments, sidebar
Requires at least: 2.8
Tested up to: 3.0
Stable tag: 1.6

This is a very simple widget that displays a link to the top commented posts on your blog.

== Description ==

Most Popular Posts is a basic widget for your sidebar that creates a list of links to the top posts on your blog according to the number of comments on the post. You can customize many aspects of the plugin to fit in your blog.

Updates include including and excluding categories, reverse the order of comments and incorporation of WordPress widget standards.

For a complete list of the changes from each version, please visit <a href="http://www.wesg.ca/2008/08/wordpress-widget-most-popular/#changelog">the plugin homepage</a>.

For examples and tips on using the plugin, please check <a href="http://www.wesg.ca/2008/08/wordpress-widget-most-popular/#examples">the examples</a> on the plugin homepage.

Be sure to check out my other plugins at <a href="http://wordpress.org/extend/plugins/profile/wesg">my Wordpress profile</a>.

= Usage =

Used exclusively as a widget at the current time. 

== Installation ==

1. Upload the folder most-popular-posts to your Wordpress plugin directory.
1. Activate the plugin from the plugin panel.
1. Navigate to the widget configuration panel and customize the widget.

== FAQ ==

= What is the purpose of this widget? =

While I thought a widget like this was very common, I had trouble finding one for my blog. That led me to create this basic widget that simply displays a link to the top commented posts. You can customize the title, number of posts, and whether you want to display the comment count.

= Is it configurable? =

Sure. Have a look at the options in the widget configuration panel.

= Can I only show posts from a single category if I want? =

Yes. As of version 1.3, more control is available for categories. You can select a single category for the links, or exclude a single category. All options are available from the widget configuration panel.

= What do these options mean? =

**Show comment count**
Display the number of comments associated with the post beside the link.

**Include zero comment posts**
Show links to posts that do not yet have any comments.

**Show comments from all categories**
Check if you want links to be displayed for posts in any category.

**Only show posts in this category**
Show links to posts that are only in this category. The previous checkbox must be unchecked for this setting to take effect.

**Exclude posts from no categories**
Check if you don't want to exclude links from a specific category.

**Exclude posts in this category**
Skip posts that fall in this category. The previous checkbox must be unchecked for this setting to take effect.

**Nest widget inside list**
When this is checked, the widget will use an additional `<li>` element around it. Toggle this option to see how the widget best fits in with your theme.

**Classes**
This box includes options to style the list components of the plugin. To match the list with your theme, add a class here, then insert a class declaration in your style sheet.

**Show posts in the last x days/weeks/months**
By request, version 1.4.3 includes an option to only show links to posts from certain a certain time period. This is determined by the date the post was published, not the comments themselves.

**Get least commented posts**
Toggle the order in which posts are displayed.

== Changelog ==

= 1.0 =
* Initial release

= 1.1 =
* Added support for using the list as a function in a post

= 1.2 =
* Updated for WordPress 2.7
* Add localization capability

= 1.3 =
* Added categories to exclude
* Added categories to include
* Switch off nonzero comment posts

= 1.4 =
* Added exclude post checkbox
* Added include post checkbox
* Stopped drafts from being displayed

= 1.4.1 =
* Excludes posts with multiple categories

= 1.4.2 =
* Added control for formatting the list to match website theme

= 1.4.3 =
* Added ability to only display links to posts from a specific timeframe

= 1.5 =
* Rewritten to conform to WP 2.8+ widget standards
* Added reverse order option