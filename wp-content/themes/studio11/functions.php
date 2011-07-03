<?php

function studio11_widgets_init() {
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Left side widget area', 'Studio11' ),
		'id' => 'left-primary-widget-area',
		'description' => __( 'The primary left widget area', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

}
/** Register sidebars by running twentyten_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'studio11_widgets_init' );



class Program_Widget extends WP_Widget {
  
  function Program_Widget() { parent::WP_Widget(false, 'Program'); }
  function form($instance) {}
  function update($new_instance, $old_instance) { return $new_instance; }
 
  function widget($args, $instance) {
    global $post, $wp_locale;
    $args['title'] = 'Program';
    $events = new WP_Query( array('post_type' => 'event','posts_per_page' => -1,'meta_key' => 'neuf_events_starttime','orderby' => 'meta_value','order' => 'ASC') );
    echo $args['before_widget'] . $args['before_title'] . $args['title'] . $args['after_title'];
    if ( $events->have_posts() ) {
      echo '<ul id="event-widget">';
      while ( $events->have_posts() ) {
	$events->the_post();
	echo '<li>' . date("d/m", get_post_meta($post->ID, 'neuf_events_starttime', true)) . ' |  <a href="' . get_permalink() . '">' . get_the_title() . '</a>';
      } echo '</ul>';
      wp_reset_query();
    } echo $args['after_widget'];
  }
}

class Artist_Widget extends WP_Widget {
  function Artist_Widget() { parent::WP_Widget(false, 'Artister'); }
  function form($instance) {}
  function update($new_instance, $old_instance) { return $new_instance; }
  
  function widget($args, $instance) {
    global $post, $wp_locale;
    $args['title'] = 'Artister';
    $studart = new WP_Query( array('post_type' => 'artist', 'posts_per_page' => -1, 'orderby' => 'rand'));
    echo $args['before_widget'] . $args['before_title'] . $args['title'] . $args['after_title'];
    if ( $studart->have_posts() ) {
      while ( $studart->have_posts() ) {
	$studart->the_post();
	$link = get_post_meta($post->ID, 'studio_artist_link', true) ? get_post_meta($post->ID, 'studio_artist_link', true) : get_permalink();
	echo '<p style="font-size:'.get_post_meta($post->ID, 'studio_artist_font', true).'px"><a href="' . $link . '">' . get_the_title() . '</a></p>';
      } 
    } echo $args['after_widget'];
  }
}

register_widget('Program_Widget'); 
register_widget('Artist_Widget'); 