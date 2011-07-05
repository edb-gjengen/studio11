<?php
/*
Plugin Name: Custom Posts Relationships (CPR)
Plugin URI: http://webtoolkit4.me/2010/06/10/wordpress-plugin-custom-post-relationships-cpr-v1-0/
Description: An easy way to create post relationships in Wordpress
Version: 1.01
Author: Gerasimos Tsiamalos
Author URI: http://webtoolkit4.me/


Copyright 2010  Gerasimos Tsiamalos  (email : tsiger@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



define('CPR_VERSION','1.01');

function cpr_scripts_styles() {
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-sortable');		
	wp_enqueue_style('cpr-css', plugin_dir_url( __FILE__ ) . 'cpr.css', true, CPR_VERSION , 'all' );
	wp_enqueue_script('category-ajax-request', plugin_dir_url( __FILE__ ) . 'cpr.js', array( 'jquery' ) );
	wp_localize_script('category-ajax-request', 'AjaxHandler', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

function cpr_box() {
 add_meta_box( 'post-meta-boxes', 'Custom Post Relationships (CPR)', 'cpr_category_selector', 'post', 'normal','high' );
}

function cpr_category_selector() {
	
	global $post_ID;
	
	echo "<div id='cat-selector'>";
	echo "<select id='howmany' name='howmany'><option value='10'>10</option><option value='50'>50</option><option value='100'>100</option><option value='-1'>All</option></select> posts from ";
 wp_dropdown_categories('hide_empty=0&hierarchical=1&name=cpr_filters&show_count=1&show_option_all=All categories');
 echo " ordered by <select id='orderby' name='orderby'><option value='title'>Title</option><option value='date'>Date</option></select> in "; 
 echo " <select id='orderin' name='orderin'><option value='ASC'>Ascending</option><option value='DESC'>Descending</option></select> order &nbsp;";
 echo " <input type='button' class='cpr_button button' value='Search posts' />";
 echo " &nbsp; Filter results: <input type='text' id='filtered' name='filtered' /><input type='hidden' id='h_pid' name='h_pid' value='". $post_ID ."'/>";
 echo "</div>";
 echo '<div class="postbox">
 							<h3>Available Posts</h3>
 							<div id="available-posts">Please select a category</div>
 							<h3>Related Posts (Drag to reorder)</h3>
 							<div id="related-posts">';

 							$relations = get_post_meta($post_ID, 'cpr_related', true);

 							if (!empty($relations)) :
									foreach($relations as $relation) :
										$post = get_post($relation);
										echo "<div title='" . $post->post_title . "' class='thepost' id='post-".$post->ID ."'>
		 													<a href='#' class='removeme'>Remove</a>
								 							<p><strong>" . $post->post_title . "</strong></p>
								 							<input type='hidden' name='reladded[]' value='" . $post->ID . "' />
								 							</div>";
									endforeach;	
								endif;
 							echo '<input type="hidden" name="myplugin_noncename" id="myplugin_noncename" value="' .  wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
								echo "</div></div>";
} 

// Where's Dukey? Wa zaaaaaaaaaaaa (the call)
function cpr_cats() {
	$catID   = $_POST['catID'];
	$postID  = $_POST['postID'];
	$howMany = $_POST['howMany'];
	$orderBy = $_POST['orderBy'];
	$orderIn = $_POST['orderIn'];

	if ($catID >= 0) {
		
		// if all categories selected
		if ($catID == 0) {
 		$args = array(
 			'post_type' => 'post',
 			'numberposts' => $howMany,
 			'post_status' => 'publish',
 			'orderby' => $orderBy,
 			'order' => $orderIn,
 			'post__not_in' => array($postID)
 		);
 		$catPosts = get_posts($args);
 	}
 	else {
 	 $args = array(
 			'post_type' => 'post',
 			'numberposts' => $howMany,
 			'post_status' => 'publish',
 			'orderby' => $orderBy,
 			'order' => $orderIn,
 			'category__in' => array($catID),
 			'post__not_in' => array($postID)
 		);
 	 $catPosts = get_posts($args);
 	}					 
		
		if (!empty($catPosts)) {
 		 foreach ( $catPosts as $catPost ) {
  		 setup_postdata($post);
  		 echo "<div title='" . $catPost->post_title . "' class='thepost' id='post-".$catPost->ID ."'>
  		 						<a href='#' class='addme'>Add</a>
  		 						<p><strong>" . $catPost->post_title . "</strong></p>
  		 						<input type='hidden' name='related[]' value='" . $catPost->ID . "' />
  		 						</div>";
    }// foreach
  }// if !empty  
  else { echo "<div class='thepost'>This category is empty</div>"; }
	}//if	
	else { echo "<div class='thepost'>Please select a category</div>";	}
	exit;
}// function cpr_cats

function cpr_save() {	
	global $post_ID;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
	if (!wp_verify_nonce( $_POST['myplugin_noncename'], plugin_basename(__FILE__))) return $post_id;
	if (!current_user_can( 'edit_post', $post_id ) ) return $post_id;

	$id = $_POST['post_ID'];
	$related = $_POST['reladded'];
	update_post_meta($id, 'cpr_related', $related);
}

function cpr_populate($id) {
	global $wpdb;
	$related_meta = get_post_meta($id, 'cpr_related', true);
	$related_posts = array();
	if (!empty($related_meta)) {
 	foreach ($related_meta as $related) {
 		$post = get_post($related);
 		$related_posts[] = $post;
  }
 return $related_posts; 
 }
}

// oi! wait! where are you going? are you sure? 100%? a second thought? come on let's talk about it. oh well.
function cpr_uninstall() {
			global $wpdb;	
			$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE meta_key = 'cpr_related'"));
}

add_action('admin_menu', 'cpr_box');
add_action('wp_ajax_cpr-cats', 'cpr_cats');
add_action('admin_menu', 'cpr_scripts_styles');
add_action('save_post', 'cpr_save');
add_action('wt4_show','cpr_populate');
register_uninstall_hook(__FILE__, 'cpr_uninstall');
?>