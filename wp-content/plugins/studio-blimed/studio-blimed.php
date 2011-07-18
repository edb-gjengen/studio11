<?php

/*
Plugin Name: Blimed form
Plugin URI: 
Description: form-shortcode
Version: 1
Author: Sjur Hernes
Author URI: 
*/


register_activation_hook( __FILE__,  'studio_blimed_install' );
register_deactivation_hook( __FILE__,  'studio_blimed_uninstall' );

function studio_blimed_uninstall() {
  global $wpdb;
  $studio_blimed_table = $wpdb->prefix . "studio_blimed_table";
  $wpdb->query("DROP TABLE IF EXISTS $studio_blimed_table ;");
}


function studio_blimed_install() {
  global $wpdb;
  $studio_blimed_table = $wpdb->prefix . 'studio_blimed_table';

  if ( $wpdb->get_var( "show tables like 'studio_blimed_table'" ) != $studio_blimed_table ) {

    $sql = "CREATE TABLE $studio_blimed_table ( 
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      name tinytext NOT NULL,
      studsted tinytext NOT NULL,
      mail tinytext NOT NULL,
      tlf tinytext NOT NULL,
      teknisk tinyint(1) NOT NULL,
      trivsel tinyint(1) NOT NULL,
      transport tinyint(1) NOT NULL,
      bar tinyint(1) NOT NULL,
      konsert tinyint(1) NOT NULL,
      artist tinyint(1) NOT NULL,
      morgen tinyint(1) NOT NULL,
      dag tinyint(1) NOT NULL,
      kveld tinyint(1) NOT NULL,
      natt tinyint(1) NOT NULL,

      man1 tinyint(1), tir1 tinyint(1), ons1 tinyint(1), tor1 tinyint(1), fre1 tinyint(1), lor1 tinyint(1), son1 tinyint(1),
      man2 tinyint(1), tir2 tinyint(1), ons2 tinyint(1), tor2 tinyint(1), fre2 tinyint(1), lor2 tinyint(1), son2 tinyint(1),
      man3 tinyint(1), tir3 tinyint(1), ons3 tinyint(1), tor3 tinyint(1), fre3 tinyint(1), lor3 tinyint(1), son3 tinyint(1),

      kommentar text,
      UNIQUE KEY id (id)
      )";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

  }
}

function blimed_form( ) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'studio_blimed_table';
  $backspinn = '';

  if (isset($_POST['save'])) {

    if ($_POST['funk_name'] != '' && 
	$_POST['funk_mail'] != '' &&
	$_POST['funk_tlf'] != '' &&
	($_POST['funk_artist'] == 't' || 
	 $_POST['funk_bar'] == 't' || 
	 $_POST['funk_teknisk'] == 't' || 
	 $_POST['funk_transport'] == 't' || 
	 $_POST['funk_konsert'] == 't' ||
	 $_POST['funk_trivsel'] == 't')) {
      
      $settinnidb = $wpdb->insert( $table_name, array( 'name'        => mysql_real_escape_string($_POST['funk_name'])                   ,
						       'tlf'         => mysql_real_escape_string($_POST['funk_tlf'])                    ,
						       'mail'        => mysql_real_escape_string($_POST['funk_mail'])                   ,
						       'studsted'    => mysql_real_escape_string($_POST['funk_studsted'])               ,
						       'artist'      => $_POST['funk_artist'] == "t"                                    ,
						       'konsert'     => $_POST['funk_konsert'] == "t"                                   ,
						       'bar'         => $_POST['funk_bar'] == "t"                                       ,
						       'teknisk'     => $_POST['funk_teknisk'] == "t"                                   ,
						       'transport'   => $_POST['funk_transport'] == "t"                                 ,
						       'trivsel'     => $_POST['funk_trivsel'] == "t"                                   ,
						       'morgen'      => $_POST['funk_morgen'] == "t"                                    ,
						       'dag'         => $_POST['funk_dag'] == "t"                                       ,
						       'kveld'       => $_POST['funk_kveld'] == "t"                                     ,
						       'natt'        => $_POST['funk_natt'] == "t"                                      ,

						       'man1'        => $_POST['funk_1man'] == "t"                                      ,
						       'tir1'        => $_POST['funk_1tir'] == "t"                                      ,
						       'ons1'        => $_POST['funk_1ons'] == "t"                                      ,
						       'tor1'        => $_POST['funk_1tor'] == "t"                                      ,
						       'fre1'        => $_POST['funk_1fre'] == "t"                                      ,
						       'lor1'        => $_POST['funk_1lør'] == "t"                                      ,
						       'son1'        => $_POST['funk_1søn'] == "t"                                      ,
						       'man2'        => $_POST['funk_2man'] == "t"                                      ,
						       'tir2'        => $_POST['funk_2tir'] == "t"                                      ,
						       'ons2'        => $_POST['funk_2ons'] == "t"                                      ,
						       'tor2'        => $_POST['funk_2tor'] == "t"                                      ,
						       'fre2'        => $_POST['funk_2fre'] == "t"                                      ,
						       'lor2'        => $_POST['funk_2lør'] == "t"                                      ,
						       'son2'        => $_POST['funk_2søn'] == "t"                                      ,
						       'man3'        => $_POST['funk_3man'] == "t"                                      ,
						       'tir3'        => $_POST['funk_3tir'] == "t"                                      ,
						       'ons3'        => $_POST['funk_3ons'] == "t"                                      ,
						       'tor3'        => $_POST['funk_3tor'] == "t"                                      ,
						       'fre3'        => $_POST['funk_3fre'] == "t"                                      ,
						       'lor3'        => $_POST['funk_3lør'] == "t"                                      ,
						       'son3'        => $_POST['funk_3søn'] == "t"                                      ,
						       'kommentar' => mysql_real_escape_string($_POST['funk_kommentar'])
						       )
				   );
      
      if ($settinnidb) {
	$backspinn = 'Du er nå registrert';
	
	// TODO MAILER!!

      } else $backspinn = 'Beklager teknisk feil, sjekk verdiene og prøv på nytt';
    } else $backspinn = 'Du må oppgi navn, epost, tlf og minst et arbeidsområde';    

  } 
  echo '    <div id="studioform" style="display:none">
   <form id="funk_data_form" method="post" action="">
     <table>
       <tr>
       <td>Navn:</td> 
       <td><input type="text" name="funk_name" id="funk_name" /></td>
       </tr>
       <tr>
       <td>Tlf:</td>
       <td><input type="text" name="funk_tlf" id="funk_tlf" /></td>
       </tr><tr>
       <td>Mail: </td> 
       <td><input type="text" name="funk_mail" id="funk_mail" /></td>
       </tr><tr>
       <td>Studiested: </td> 
       <td><input type="text" name="funk_studsted" id="funk_studsted" /></td>
       </tr>
       </table>
       <br />
       Når vil du jobbe?       
       <table>
       <tr>

       <td> morgen&nbsp;&nbsp; </td><td> dag&nbsp;&nbsp;</td><td> kveld&nbsp;&nbsp; </td><td> sent </td>
        </tr>
        <tr>
            <td><input id="funk_morgen" name="funk_morgen" type="checkbox" value="t" /></td>
            <td><input id="funk_dag" name="funk_dag" type="checkbox" value="t" /></td>
            <td><input id="funk_kveld" name="funk_kveld" type="checkbox" value="t" /></td>
            <td><input id="funk_natt" name="funk_natt" type="checkbox" value="t" /></td>
        </tr>
</table><br />
Hva vil du gjøre?
<table>
        <tr>
            <td> Artist&nbsp;&nbsp; </td><td> Bar&nbsp;&nbsp; </td><td> Teknisk&nbsp;&nbsp; </td><td> Transport&nbsp;&nbsp; </td><td> Konsert&nbsp;&nbsp; </td><td> Trivsel&nbsp;&nbsp; </td>
                              </tr>
                              <tr>
                                  <td><input id="funk_artist" name="funk_artist" type="checkbox" value="t" /></td>
                                  <td><input id="funk_bar" name="funk_bar" type="checkbox" value="t" /></td>
                                  <td><input id="funk_teknisk" name="funk_teknisk" type="checkbox" value="t" /></td>
                                  <td><input id="funk_transport" name="funk_transport" type="checkbox" value="t" /></td>
                                  <td><input id="funk_konsert" name="funk_konsert" type="checkbox" value="t" /></td>
                                  <td><input id="funk_trivsel" name="funk_trivsel" type="checkbox" value="t" /></td>
                              </tr>
                            </table>

                            <table>
                              <tr><td>Uke</td><td>man&nbsp;</td><td>&nbsp;tir&nbsp;</td><td>&nbsp;ons&nbsp;</td><td>&nbsp;tor&nbsp;</td><td>&nbsp;fre&nbsp;</td><td>&nbsp;lør&nbsp;</td><td>&nbsp;søn&nbsp;</td></tr>
                              <tr><td>uke 1:&nbsp;</td><td>
                                 <input id="funk_1man" name="funk_1man" type="checkbox" value="t" />
                              </td><td>
                                 <input id="funk_1tir" name="funk_1tir" type="checkbox" value="t" />
                              </td><td>
                                 <input id="funk_1ons" name="funk_1ons" type="checkbox" value="t" />
                              </td><td>
                                 <input id="funk_1tor" name="funk_1tor" type="checkbox" value="t" />
                              </td><td>
                                 <input id="funk_1fre" name="funk_1fre" type="checkbox" value="t" />
                              </td><td>
                                 <input id="funk_1lør" name="funk_1lør" type="checkbox" value="t" />
                              </td><td>
                                 <input id="funk_1søn" name="funk_1søn" type="checkbox" value="t" />
                              </td></tr>

                              <tr><td>uke 2: &nbsp;</td><td>
                                 <input id="funk_2man" name="funk_2man" type="checkbox" value="t" />
                              </td><td>
                                 <input id="funk_2tir" name="funk_2tir" type="checkbox" value="t" />
                              </td><td>
                                 <input id="funk_2ons" name="funk_2ons" type="checkbox" value="t" />
                              </td><td>
                                 <input id="funk_2tor" name="funk_2tor" type="checkbox" value="t" />
                              </td><td>
                                 <input id="funk_2fre" name="funk_2fre" type="checkbox" value="t" />
                              </td><td>
                                 <input id="funk_2lør" name="funk_2lør" type="checkbox" value="t" />
                              </td><td>
                                 <input id="funk_2søn" name="funk_2søn" type="checkbox" value="t" />
                              </td></tr>
                              <tr>

                                 <td>uke 3: &nbsp;</td><td>
                                   <input id="funk_3man" name="funk_3man" type="checkbox" value="t" />
                                 </td><td>
                                   <input id="funk_3tir" name="funk_3tir" type="checkbox" value="t" />
                                 </td><td>
                                   <input id="funk_3ons" name="funk_3ons" type="checkbox" value="t" />
                                 </td><td>
                                   <input id="funk_3tor" name="funk_3tor" type="checkbox" value="t" />
                                 </td><td>
                                   <input id="funk_3fre" name="funk_3fre" type="checkbox" value="t" />
                                 </td><td>
                                   <input id="funk_3lør" name="funk_3lør" type="checkbox" value="t" />
                                 </td><td>
                                   <input id="funk_3søn" name="funk_3søn" type="checkbox" value="t" />
                                 </td></tr>

                            </table>

       Andre bemerkelser: <textarea id="funk_kommentar" name="funk_kommentar"></textarea>
       <input id="saveForm" class="submitButton" type="submit" name="save" value="Submit Form" />
       </form>';
  echo '</div>';
    
  if ($backspinn != '') echo $backspinn;
  else echo '<a href="#"><span class="frontbox" src="#studioform" height="420" title="blimed"></span>Bli med</a>';
}

add_shortcode('blimed_form', 'blimed_form');
