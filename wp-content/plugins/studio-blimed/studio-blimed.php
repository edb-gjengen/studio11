<?php

/*
Plugin Name: Blimed form
Plugin URI: 
Description: form-shortcode
Version: 1
Author: Sjur Hernes
Author URI: 
*/

//include_once './studio-blimed-admin.php';
register_activation_hook( __FILE__,  'studio_blimed_install' );
//register_deactivation_hook( __FILE__,  'studio_blimed_uninstall' );

/** drops table on deactivation TODO : Get backup on deactivation */

function studio_blimed_uninstall() {
  global $wpdb;
  $studio_blimed_table = $wpdb->prefix . "studio_blimed_table";
  $wpdb->query("DROP TABLE IF EXISTS $studio_blimed_table ;");
}
/** Activation of plugin, creates the tables */

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
      valg1 tinytext NOT NULL,
      valg2 tinytext NOT NULL,
      valg3 tinytext NOT NULL,
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


/** 
 * Shows form and inserts on _POST
 */
function blimed_form( ) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'studio_blimed_table';
  $backspinn = '';

  if (isset($_POST['save'])) {

    // Some values should allways be there
    if ($_POST['funk_name']  != '' && 
	$_POST['funk_mail']  != '' &&
	$_POST['funk_tlf']   != '' &&
	$_POST['funk_valg1'] != '') {      

      // Insert into db. $settinnidb will have value if successfull :)
      $settinnidb = $wpdb->insert( $table_name, array( 'name'        => mysql_real_escape_string($_POST['funk_name'])                   ,
						       'tlf'         => mysql_real_escape_string($_POST['funk_tlf'])                    ,
						       'mail'        => mysql_real_escape_string($_POST['funk_mail'])                   ,
						       'studsted'    => mysql_real_escape_string($_POST['funk_studsted'])               ,

						       'valg1'       => mysql_real_escape_string($_POST['funk_valg1'])                  ,
						       'valg2'       => mysql_real_escape_string($_POST['funk_valg2'])                  ,
						       'valg3'       => mysql_real_escape_string($_POST['funk_valg3'])                  ,
						       
						       'morgen'      => $_POST['funk_morgen'] == "t"                                    ,
						       'dag'         => $_POST['funk_dag']    == "t"                                    ,
						       'kveld'       => $_POST['funk_kveld']  == "t"                                    ,
						       'natt'        => $_POST['funk_natt']   == "t"                                    ,
						       
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
  echo '    <div id="studioform">
   <form id="funk_data_form" method="post" action="">
     <table>
       <tr>
       <td>Navn:</td> 
       <td><input type="text" name="funk_name" id="funk_name" /></td>
	   <td> 1.valg oppgaver </td>
       <td><select id="funk_valg1" name="funk_valg1">
              <option value=""></option>
              <option value="artist">Artist</option>
              <option value="konsert">Konsert</option>
              <option value="bar">Bar</option>
              <option value="teknisk">Teknisk</option>
              <option value="transport">Transport</option>
              <option value="trivsel">Trivsel</option>
       </select></td>	
       </tr>
       <tr>
       <td>Tlf:</td>
       <td><input type="text" name="funk_tlf" id="funk_tlf" /></td>
       <td> 2.valg oppgaver </td>
       <td><select id="funk_valg2" name="funk_valg2">
              <option value=""></option>
              <option value="artist">Artist</option>
              <option value="konsert">Konsert</option>
              <option value="bar">Bar</option>
              <option value="teknisk">Teknisk</option>
              <option value="transport">Transport</option>
              <option value="trivsel">Trivsel</option>
       </select></td>	
       </tr><tr>
       <td>Mail: </td> 
       <td><input type="text" name="funk_mail" id="funk_mail" /></td>
       <td> 3.valg oppgaver </td>
       <td><select id="funk_valg3" name="funk_valg3">
              <option value=""></option>
              <option value="artist">Artist</option>
              <option value="konsert">Konsert</option>
              <option value="bar">Bar</option>
              <option value="teknisk">Teknisk</option>
              <option value="transport">Transport</option>
              <option value="trivsel">Trivsel</option>
       </select></td>	
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
  else echo '<a href="#"><span class="frontbox" src="#studioform" height="450" title="blimed"></span>Bli med</a>';
}

add_shortcode('blimed_form', 'blimed_form');

function blimed_admin_content($param, $date){

    global $wpdb;
        
    $show_query = "SELECT * FROM ".$wpdb->prefix."studio_blimed_table";
    $show_query2 = "SELECT * FROM ".$wpdb->prefix."studio_blimed_table";
    $show_query3 = "SELECT * FROM ".$wpdb->prefix."studio_blimed_table";

    if ($param == 'all' && $date == 'all') {
        $show_query .= "";
    } elseif ($param == 'all' && $date != 'all') {
        $show_query .= " WHERE ".$date." = 1;";
    } elseif ($param != 'all' && $date == 'all') {
        $show_query .= " WHERE valg1 = '".$param."'";
        $show_query2 .= " WHERE valg2 = '".$param."'";
        $show_query3 .= " WHERE valg3 = '".$param."'";
    } elseif ($param != 'all' && $date != 'all') {
        $show_query .= " WHERE valg1 = '".$param."' AND '".$date."' = 1";
        $show_query2 .= " WHERE valg2 = '".$param."' AND '".$date."' = 1";
        $show_query3 .= " WHERE valg3 = '".$param."' AND '".$date."' = 1";
    }

    if ($param == 'all') {
        echo "Søket som er utført er: " . $show_query . "<br /><br />";
        $funks = $wpdb->get_results($show_query);

        blimed_print_table($funks);

    } elseif ($param != 'all') {

        $funks = $wpdb->get_results($show_query);
        $funks2 = $wpdb->get_results($show_query2);
        $funks3 = $wpdb->get_results($show_query3);

        echo "<h3>Førstevalget: " . $param . "</h3><br /><br />";

        echo "Søket som er utført er: " . $show_query . "<br /><br />";

        blimed_print_table($funks);

        echo "<h3>Andrevalget: " . $param . "</h3> <br /><br />";

        echo "Søket som er utført er: " . $show_query2 . "<br /><br />";

        blimed_print_table($funks2);

        echo "<h3>Tredjeevalget: ".$param."</h3> <br />";

        echo "Søket som er utført er: ".$show_query3 . "<br /><br />";

        blimed_print_table($funks3);


    }

}

function blimed_print_table( $funks ){
        
        $dagvariabler = array('man1', 'tir1', 'ons1', 'tor1', 'fre1', 'lor1', 'son1',
                              'man2', 'tir2', 'ons2', 'tor2', 'fre2', 'lor2', 'son2',
                              'man3', 'tir3', 'ons3', 'tor3', 'fre3', 'lor3', 'son3');

        $tidvar = array('morgen', 'dag', 'kveld', 'natt');
        echo "<table style='border:1px;'>
            <tr>
                <th>Navn&nbsp;&nbsp;</th>
                <th>Epost&nbsp;&nbsp;</th>
                <th>Telefon&nbsp;&nbsp;</th>
                <th>Studiested&nbsp;&nbsp;</th>
                <th>Førstevalg&nbsp;&nbsp;</th>
                <th>Andrevalg&nbsp;&nbsp;</th>
                <th>Tredjevalg&nbsp;&nbsp;</th>
                <th>Dager&nbsp;&nbsp;</th>
                <th>Tider&nbsp;&nbsp;</th>
                <th>Kommentar&nbsp;&nbsp;</th>
            </tr>
        ";
        foreach ($funks as $funk) {
            $dager = ""; $tider = "";
            foreach ($dagvariabler as $dag) {
                if ($funk->$dag == 1) $dager .= $dag . ", ";
            }
            
            foreach ($tidvar as $tid) {
                if ($funk->$tid == 1) $tider .= $tid . ", ";
            }
    
            echo "<tr>
                    <td>$funk->name&nbsp;&nbsp;</td>
                    <td>$funk->mail&nbsp;&nbsp;</td>
                    <td>$funk->tlf&nbsp;&nbsp;</td>
                    <td>$funk->studsted&nbsp;&nbsp;</td>
                    <td>$funk->valg1&nbsp;&nbsp;</td>
                    <td>$funk->valg2&nbsp;&nbsp;</td>
                    <td>$funk->valg3&nbsp;&nbsp;</td>
                    <td>$dager&nbsp;&nbsp;</td>
                    <td>$tider&nbsp;&nbsp;</td>
                    <td>$funk->kommentar</td>
                  </tr>" ;
        }
        echo "</table><br /><br />";
}


function blimed_admin() {
	echo '
<div class="wrap">
    <h1>test</h1>

    <form id="select stuff" method="post" action="">
        hvem skal vises:
        <select id="blimed_admin_sort" name="blimed_admin_sort">
            <option value="all">all</option>
            <option value="artist">Artist</option>
            <option value="konsert">Konsert</option>
            <option value="bar">Bar</option>
            <option value="teknisk">Teknisk</option>
            <option value="transport">Transport</option>
            <option value="trivsel">Trivsel</option>
        </select>
        <select id="blimed_admin_date" name="blimed_admin_date">
            <option value="all">all</option>
            <option value="man1">Mandag uke 1</option>
            <option value="tir1">Tirsdag uke 1</option>
            <option value="ons1">Onsdag uke 1</option>
            <option value="tor1">Torsdag uke 1</option>
            <option value="fre1">Fredag uke 1</option>
            <option value="lor1">Lørdag uke 1</option>
            <option value="son1">Søndag uke 1</option>

            <option value="man2">Mandag uke 2</option>
            <option value="tir2">Tirsdag uke 2</option>
            <option value="ons2">Onsdag uke 2</option>
            <option value="tor2">Torsdag uke 2</option>
            <option value="fre2">Fredag uke 2</option>
            <option value="lor2">Lørdag uke 2</option>
            <option value="son2">Søndag uke 2</option>

            <option value="man3">Mandag uke 3</option>
            <option value="tir3">Tirsdag uke 3</option>
            <option value="ons3">Onsdag uke 3</option>
            <option value="tor3">Torsdag uke 3</option>
            <option value="fre3">Fredag uke 3</option>
            <option value="lor3">Lørdag uke 3</option>
            <option value="son3">Søndag uke 3</option>
        </select>
        <input id="saveForm" class="submitButton" type="submit" name="sort" value="Sorter" />
    </form>
    '; 

    if($_POST['sort'])
        blimed_admin_content($_POST['blimed_admin_sort'], $_POST['blimed_admin_date']);
    else
        blimed_admin_content('all', 'all'); 
    echo '
</div>';
}

function blimed_admin_test() {
	add_menu_page('blimed', 'blimed', 'manage_options', 'blimed-admin-slug', 'blimed_admin');
}

add_action('admin_menu', 'blimed_admin_test');

