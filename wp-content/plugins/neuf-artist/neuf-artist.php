<?php
/*
  Plugin Name: studio-artist
  Plugin URI: http://www.studentersamfundet.no
  Description: Plugin to manage artists for studio11
  Version 0.1
  Author: Sjur Hernes
  Author URI: grey.sjux.net
  License: GPL v2 or later
*/
?>

<?php

if (!class_exists("StudioArtist")) {

  class StudioArtist{

    function StudioArtist(){

      /**
	 Create the fields the post type should have
      */
      function studio_artist_post_type() {
	register_post_type(
			   'artist',
			   array(
				 'labels' => array(
						   'name'                  =>      __( 'Artister'                       ),
						   'singular_name'         =>      __( 'Artist'                         ),
						   'add_new'               =>      __( 'Legg til ny artist'             ),
						   'add_new_item'          =>      __( 'Legg til ny artist'             ),
						   'edit_item'             =>      __( 'Rediger artist'                 ),
						   'new_item'              =>      __( 'Legg til ny artist'             ),
						   'view_item'             =>      __( 'Vis artist'                     ),
						   'search_items'          =>      __( 'Søk etter artist'               ),
						   'not_found'             =>      __( 'ingen artister funnet'          ),
						   'not_found_in_trash'    =>      __( 'ingen artister funnet i søppel' )
						   ),
				 'public'              =>  true,
				 'publicly_queryable'  =>  true,
				 'query_var'           =>  'artist',
				 'capability_type'     =>  'post',
				 
				 'supports'            =>  array(
								 'title',
								 'editor',
								 'thumbnail',
								 ),
				 'register_meta_box_cb' => 'add_studart_metaboxes',
				 )
			   );
      }

       
      /*******************************************************************************
      ********************************************************************************
      **  Add meta-boxes   ***********************************************************
      ********************************************************************************
      *******************************************************************************/

      function add_studart_metaboxes() {

	add_meta_box(
		     'studio_artist_pop',
		     __('Artiststørrelse'),
		     'studio_artist_custom_box',
		     'artist',
		     'side',
		     'high'
		     );
      }

      /*******************************************************************************
      ********************************************************************************
      **  Eventtype metabox   ********************************************************
      ********************************************************************************
      *******************************************************************************/

      function studio_artist_custom_box(){

	global $post;

	$artist_font = get_post_meta($post->ID, 'studio_artist_font' , true);
	$artist_link = get_post_meta($post->ID, 'studio_artist_link', true);

	echo "Fontstørrelse:";
	echo '<select name="studio_artist_font">';
	echo $artist_font;

	$types = array( array('name'  =>  'Flink i Guitarhero'   , 'size' => 12),
			array('name'  =>  'Liten Artist'         , 'size' => 15),
			array('name'  =>  'Mellomstor Artist'    , 'size' => 18),
			array('name'  =>  'Stor Artist'          , 'size' => 21),
			array('name'  =>  'Oh! Dette blir fest!' , 'size' => 24),
			array('name'  =>  'Hopalong Knut!!'      , 'size' => 44),
			);

	foreach($types as $type){
	  echo '<option value="' . $type['size'] . '"';
	  if($type['size'] == $artist_font)
	    echo ' selected="selected"';
	  echo '>' . $type['name'] . '</option>';
	}
	echo '</select>';

	echo '<br />Artisturl:<br /><input type="text" name="studio_artist_link" value="'.$artist_link.'" />';

	
      }

      /**
       *  When the post is saved, saves our custom data
       */ 


      function studio_artist_save_info( $post_id ) {

	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )  return $post_id;

	if ( !current_user_can( 'edit_post', $post_id )  ) return $post_id;
	
	// Get posted data
	$studart['studio_artist_font'] = $_POST['studio_artist_font' ];
	$studart['studio_artist_link'] = $_POST['studio_artist_link'];
	
	foreach($studart as $key=>$value)
	  if ( !update_post_meta($post_id, $key, $value))
	    add_post_meta($post_id, $key, $value, true);
	return $post_id;
    }

      /** View of the custom page */

      function studio_artist_list() {
	global $post, $wp_locale;
	
	$artister = new WP_Query( array('post_type' => 'artist','posts_per_page' => -1,'order' => 'ASC' ) );
	$html = '';
	
	if ( $artister->have_posts() ) {
	
	  $html .= '<ul class="artist-table">';
	  
	  while ( $artister->have_posts() ) {
	    $artister->the_post();
	    $size = get_post_meta($post->ID, 'studio_artist_font', true);
	    $html .= '  <li class="title" style="padding-right:10px; font-size:'.$size.'px"><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
	  }
	    
	  $html .= '</ul><!-- .artist-table -->';
	}

	return $html;
	
      }
    }
  }
}

if (class_exists("StudioArtist")) {
  $studio_artist_object = new StudioArtist();
}  

if ( isset($studio_artist_object)){
  add_action(    'init',                 'studio_artist_post_type', 0);
  add_action(    'save_post',            'studio_artist_save_info');
  add_shortcode( 'studio-artist-list',   'studio_artist_list'  );
}

?>
