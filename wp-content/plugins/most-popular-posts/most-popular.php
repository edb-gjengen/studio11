<?php
/*
Plugin Name: Most Popular Posts
Plugin URI: http://www.wesg.ca/2008/08/wordpress-widget-most-popular/
Description: Display a link to the most popular posts on your blog according to the number of comments.
Author: Wes Goodhoofd
Version: 1.6
Author URI: http://www.wesg.ca
*/

global $defaults;
add_action("widgets_init", create_function('', 'return register_widget("Most_Popular_Posts");'));

// default values
$defaults = array(
				'title' => 'Most Popular Posts',
				'number' => 5,
				'comment' => ' checked',
				'zero' => ' checked',
				'onlycheck' => ' checked',
				'only' => 1,
				'exclude' => 1,
				'excludecheck' => ' checked',
				'time' => '',
				'duration' => '',
				'parentclass' => '',
				'childclass' => '',
				'listclass' => '',
				'reverse' => '',
				);


class Most_Popular_Posts extends WP_Widget {
	function Most_Popular_Posts() {
		$widget_ops = array('description' => __( "Displays links to the posts with the most comments." ) );
		$this->WP_Widget('most_popular_posts', __('Most Popular Posts'), $widget_ops);
	}

	function form($instance) {
		global $defaults;
		//load translations
		$plugin_dir = basename(dirname(__FILE__));
		load_plugin_textdomain( 'most-popular-posts', null, $plugin_dir );

		// check if options are saved, otherwise use defaults
		if (mpm_isEmpty($instance))
			$instance = $defaults;
			
	$title = esc_attr($instance['title']);
	$number = esc_attr($instance['number']);
	$comment = esc_attr($instance['comment']);
	$zero = esc_attr($instance['zero']);
	$onlycheck = esc_attr($instance['onlycheck']);
	$only = esc_attr($instance['only']);
	$excludecheck = esc_attr($instance['excludecheck']);
	$exclude = esc_attr($instance['exclude']);
	$time = esc_attr($instance['time']);
	$timeunit = esc_attr($instance['duration']);
	$childclass = esc_attr($instance['childclass']);
	$reverse = esc_attr($instance['reverse']);
	
//create widget configuration panel
?>
	<p><label for="<?php echo $this->get_field_name('title'); ?>"><?php _e('Title: ', 'most-popular-posts'); ?> </label>
    <input type="text" size="30" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title;?>" id="widget-most_popular_posts" />
  </p>

	<p>
    <label for="<?php echo $this->get_field_name('number'); ?>"><?php _e('Number of posts to display: ', 'most-popular-posts'); ?></label>
    <input type="text" name="<?php echo $this->get_field_name('number'); ?>" value="<?php echo $number;?>" size="5" />
  </p>

<p>
	 <input type="checkbox" name="<?php echo $this->get_field_name('comment'); ?>" value="checked" <?php echo $comment;?> />
    <label for="<?php echo $this->get_field_name('comment'); ?>"><?php _e('Show comment count ', 'most-popular-posts'); ?></label>
</p>

<p>
	 <input type="checkbox" name="<?php echo $this->get_field_name('zero'); ?>" value="checked" <?php echo $zero;?> />
    <label for="<?php echo $this->get_field_name('zero'); ?>"><?php _e('Include zero comment posts ', 'most-popular-posts'); ?></label>
</p>

<p><?php _e('If both of the following options are enabled, the <em>only category</em> option is respected.', 'most-popular-posts'); ?></p>

<p> <input type="checkbox" name="<?php echo $this->get_field_name('onlycheck'); ?>" value="checked" <?php echo $onlycheck;?> />
    <label for="<?php echo $this->get_field_name('onlycheck'); ?>"><?php _e('Show comments from all categories ', 'most-popular-posts'); ?></label></p>

<p>
	<?php wp_dropdown_categories('hierarchical=1&selected=' . $only . '&orderby=name&show_count=1&name=' . $this->get_field_name('only') . '&hide_empty=0'); ?><br />
	<label for="<?php echo $this->get_field_name('only'); ?>"><?php _e('Only show posts in this category.', 'most-popular-posts'); ?></label>
</p>

<p> <input type="checkbox" name="<?php echo $this->get_field_name('excludecheck'); ?>" value="checked" <?php echo $excludecheck;?> />
    <label for="<?php echo $this->get_field_name('excludecheck'); ?>"><?php _e('Exclude posts from no categories ', 'most-popular-posts'); ?></label></p>

<p>
	<?php wp_dropdown_categories('hierarchical=1&selected=' . $exclude . '&orderby=name&show_count=1&name=' . $this->get_field_name('exclude') . '&hide_empty=0'); ?><br />
	<label for="<?php echo $this->get_field_name('exclude')?>"><?php _e('Exclude posts in this category.', 'most-popular-posts'); ?></label>
</p>

<p>
	<label for="<?php echo $this->get_field_name('time')?>"><?php _e('Only show posts in the last ', 'most-popular-posts'); ?></label>
	<br /><select name="<?php echo $this->get_field_name('time')?>">
	<option value="all"><?php _e('All', 'most-popular-posts'); ?></option>
	<?php 
	for ($i = 1; $i <= 12; $i++) {
		if ($time == $i)
			$selected = ' selected';
		else
			$selected = '';
		echo "<option value=" . $i . $selected . ">" . $i . "</option>\n";
	} ?>
</select>
<select name="<?php echo $this->get_field_name('duration')?>">
	<?php $duration = array('all', 'day', 'week', 'month');
		foreach ($duration as $select) {
			if ($timeunit == $select)
			$selected = ' selected';
		else
			$selected = '';
	echo "<option value=" . $select . $selected . ">" . $select . "</option>\n";
	} ?></select>
</p>

<p>
	 <input type="checkbox" name="<?php echo $this->get_field_name('reverse'); ?>" value="checked" <?php echo $reverse;?> />
    <label for="<?php echo $this->get_field_name('reverse'); ?>"><?php _e('Get least commented posts ', 'most-popular-posts'); ?></label>
</p>

<h3>Formatting</h3>
<p>
    <label for="<?php echo $this->get_field_name('childclass')?>"><?php _e('Child UL class ', 'most-popular-posts'); ?></label>
 <br />   <input type="text" name="<?php echo $this->get_field_name('childclass')?>" value="<?php echo $childclass;?>" />
  </p>
<?php
	}


	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = strip_tags($new_instance['number']);
		$instance['comment'] = strip_tags($new_instance['comment']);
		$instance['zero'] = strip_tags($new_instance['zero']);
		$instance['onlycheck'] = strip_tags($new_instance['onlycheck']);
		$instance['only'] = strip_tags($new_instance['only']);
		$instance['excludecheck'] = strip_tags($new_instance['excludecheck']);
		$instance['exclude'] = strip_tags($new_instance['exclude']);
		$instance['time'] = strip_tags($new_instance['time']);
		$instance['duration'] = strip_tags($new_instance['duration']);
		$instance['nestedlist'] = strip_tags($new_instance['nestedlist']);
		$instance['childclass'] = strip_tags($new_instance['childclass']);
		$instance['reverse'] = strip_tags($new_instance['reverse']);

        return $instance;
	}

	function widget($args, $instance) {
		global $wpdb;
		global $defaults;
		
		extract($args);
		
		if (mpm_isEmpty($instance))
			$instance = $defaults;

//check if zero comments are needed
if ($instance['zero'] != 'checked')
	$zero = '&& comment_count > 0 ';

//sort out the duration system
if (($instance['duration'] != 'all') && ($instance['time'] != 'all')) {
	//get current time of Wordpress
	$time = current_time('mysql', 1);
	$period = $instance['duration'];
	//new date to compare
	$new_date = date('Y-m-d H:i:s', strtotime($time . "-" . $instance['time'] . " " . $instance['duration']));
	$compare = " AND (post_date_gmt >= '" . $new_date . "')";
}

// determine count order
if ($instance['reverse'] == 'checked')
	$reverse = 'ASC';
else
	$reverse = 'DESC';

//get the post data from the database
if (($instance['excludecheck'] == 'checked') && ($instance['onlycheck'] == 'checked')) {
	$query = "SELECT ID, post_title, comment_count, post_date FROM " . $wpdb->posts . " WHERE post_type = 'post' && post_status = 'publish' " . $zero . $compare . " ORDER BY comment_count " . $reverse . " LIMIT " . $instance['number'];
	$posts = $wpdb->get_results($query);
	}
else if (($instance['excludecheck'] != 'checked') && ($instance['onlycheck'] == 'checked')) {
	$query = "select ID, post_title, comment_count, post_date from " . $wpdb->posts . " where ID in (select object_ID from " . $wpdb->term_relationships . " where " . $wpdb->term_relationships . ".term_taxonomy_id in (select term_taxonomy_id from " . $wpdb->term_taxonomy . " where term_id != " . $instance['exclude'] . " AND taxonomy = 'category')) AND post_type = 'post'" . $compare . " order by comment_count " . $reverse . " limit " . $instance['number'];
	$posts = $wpdb->get_results($query);
}
else {
	$query = "select ID, post_title, comment_count, post_date from " . $wpdb->posts . " where ID in (select object_ID from " . $wpdb->term_relationships . " where " . $wpdb->term_relationships . ".term_taxonomy_id = (select term_taxonomy_id from " . $wpdb->term_taxonomy . " where term_id = " . $instance['only'] . " AND taxonomy = 'category')) AND post_type = 'post'" . $compare . " order by comment_count " . $reverse . " limit " . $instance['number'];
	$posts = $wpdb->get_results($query);
}

// start widget output
echo $before_widget . "\n";
echo $before_title . $instance['title'] . $after_title . "\n";

	if ($instance['nestedlist'] == 'checked') {
		if ($instance['parentclass'] == '')
			echo '<li>';
		else
			echo '<li class="' . $instance['parentclass'] . '">';
}
	if ($instance['childclass'] == '')
			echo '<ul>';
		else
			echo '<ul class="' . $instance['childclass'] . '">' . "\n";

//display each page as a link
if (!empty($posts)) {
foreach ($posts as $links) {
	if ($instance['comment'] == 'checked')
		$comments = ' (' . $links->comment_count . ')';
	if ($instance['listclass'] != '')
			$li_class = ' class="' . $instance['listclass'] . '"';

	echo "\t" . '<li' . $li_class . '><a href="' . get_permalink($links->ID) . '">' . $links->post_title . '</a>' . $comments . '</li>' . "\n";
	}
}
else {
	echo "\n\t" . '<li>';
	_e('No posts to display', 'most-popular-posts');
	echo '</li>' . "\n";
	}
?>
</ul>
<?php
echo $after_widget . "\n";
	}
}

function mpm_isEmpty($array) {
	$i = 0;
	foreach ($array as $elements) {
		if (strlen($elements) == 0)
			$i++;
	}	
	if ($i == count($array))
		return true;
	else
		return false;
}
?>