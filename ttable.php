<?php
/*
 *  @module         Office toolset
 *  @version        see info.php of this module
 *  @author         Gerard Smelt
 *  @copyright      2010-2016, Gerard Smelt
 *  @license        see info.php of this module
 *  @platform       see info.php of this module
 */
// include class.secure.php to protect this file and the whole CMS!
if ( defined( 'LEPTON_PATH' ) ) {
  include( LEPTON_PATH . '/framework/class.secure.php' );
} else {
  $oneback = "../";
  $root = $oneback;
  $level = 1;
  while ( ( $level < 10 ) && ( !file_exists( $root . '/framework/class.secure.php' ) ) ) {
    $root .= $oneback;
    $level += 1;
    } 
  if ( file_exists( $root . '/framework/class.secure.php' ) ) {
    include( $root . '/framework/class.secure.php' );
  } else {
    trigger_error( sprintf( "[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER[ 'SCRIPT_NAME' ] ), E_USER_ERROR );
  }
}
// end include class.secure.php
/*
 * variables
 */
$regelsArr = array(
// voor routing
  'mode' => 9,
  'module' => 'table',  // ** This is the name of the module **
// voor versie display
  'modulen' => 'ttable',
  'versie' => ' v20150916 ', // ** This is the version of the module **
// general parameters
  'app' => (isset($settingArr[ 'table'])) ? $settingArr['table'] : "noname",  // ** This is the table name the impact of $settingArr can be removed **
  'table' =>  (isset($settingArr[ 'table'])) ? CH_DBBASE."_".$settingArr['table'] : CH_DBBASE."_noname", // ** This is the table name  the impact of $settingArr can be removed **
  'owner' => (isset($settingArr[ 'logo'])) ? $settingArr[ 'logo'] : '', // ** This is the logo on the pdf **
// for display en pdf output 
  'seq' => (isset($_POST[ 'next' ])) ?  $regelsArr[ 'seq']= $_POST[ 'next' ]: 0,
  'n' => 0,
  'qty' => (isset($settingArr['oplines'])) ? $settingArr['oplines'] : 30,  
  'project' => '',
// search
  'search' => '',
  'search_mysql' => '', 
  'volgorde' => 'name', 
  'opzoek'=> 'name',  
//display
  'descr' => '',
  'head' => '',
  'select' => '', 
  'update' => '',
  'toegift' => '',
  'rapport' => '',
  'memory' => '',
  'hash' => '', 
  'recid' => '',
  'record_update' => false,
  'pdf_ok' => false,
  'edit_ok' => false,
  'add_ok' => false,
  'today' => date( "Y-m-d" ),
// pdf
  'cols'=> array(55, 35, 35, 20, 20, 20),
  'today_pf' => date( "_Ymd_His" ),
);
$regelsArr['project'] = $regelsArr[ 'app' ] . ' - Overzicht';
/*
 * Initial file data
 */
$jobs = array();
$jobs[] = "CREATE TABLE IF NOT EXISTS `" . $regelsArr ['table'] . "` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `zoek` varchar(255) NOT NULL default '',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
  ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
 // ** addtional fields can be added do not remove these fields ** 
$jobs[] = "INSERT INTO `".CH_DBBASE."` (`section`, `table`, `name`, `value`) VALUES( '0', '".$regelsArr ['app']."','opzoek', 'name')";
 // ** addtional fields/records  can be added  ** 
/*
 * Lay-out strings
 */
// $TEMPLATE[ 0 ] is defined in scheduler
// $TEMPLATE[ 1 ] is defined in language module
  $TEMPLATE[ 71 ] = '<td colspan="2" >%1$s</td>';
  $TEMPLATE[ 72 ] = '%1$s&nbsp;:';
  $TEMPLATE[ 74 ] = '<input maxlength="%2$s" size="%3$s" type="text" name="%1$s" value="%4$s" placeholder="%5$s" />';
//  $TEMPLATE[ 86 ] = '<textarea rows="%2$s" cols="35" name="%1$s" placeholder="%4$s" >%3$s</textarea>';
//  $TEMPLATE[ 87 ] = '<select name="%1$s">%3$s</select>';
//  $TEMPLATE[ 93 ] = '<input type="checkbox" name="vink[]" value="%2$s">&nbsp;%1$s';
//  $TEMPLATE[ 94 ] = '<a href="' . CH_RETURN . '&command=view&module={module}&recid=%2$s">%1$s</a>';
//  $TEMPLATE[ 95 ] = '<a href="' . CH_RETURN . '&command=select&module={module}&recid=%2$s">%1$s</a>';
//  $TEMPLATE[ 88 ] = '<input type="text" name="%1$s" size="%2$s"  value="%3$s" placeholder="%4$s" autocomplete="off" />';
//  $TEMPLATE[ 89 ] = '<input type="date" name="%1$s" size="%2$s" value="%3$s" placeholder="%4$s" autocomplete="off" />';
  $TEMPLATE[ 90 ]  = '<thead><tr><th>%1$s</th><th>%2$s</th><th>%3$s</th><th>%4$s</th><th>%5$s</th></tr></thead>';
  $TEMPLATE[ 91 ] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td><td>%6$s</td></tr>';
  $TEMPLATE[ 92 ] = '<tr %1$s><td colspan="2">%2$s</td><td colspan="2">%3$s</td><td>%4$s</td><td>%5$s</td></tr>';
  $TEMPLATE[ 93 ] = '<tr><td colspan="2">%1$s</td><td colspan="3">%2$s</td></tr>';
  $TEMPLATE[ 96 ]  = '<colgroup><col width="20%"><col width="20%"><col width="20%"><col width="20%"><col width="20%"></colgroup>';
  $TEMPLATE[ 97 ] = '<tr %1$s><td>%2$s %3$s<td>%4$s</td></tr>';
  $TEMPLATE[ 98 ] = '<tr %1$s><td><a href="' . CH_RETURN . '&command=view&recid=%2$s">%3$s</a></td><td>%4$s</td><td>%5$s</td><td>%6$s</td><td>%7$s</td></tr>';
//  $TEMPLATE[ 99 ] = '<tr %1$s>%2$s %3$s<td>%4$s</td></tr>';
// icontem 1-19 is defined in language module
/*
 * get the fields
 */

// table exists ?
$query = "SHOW TABLES LIKE '".$regelsArr[ 'table' ]."'";
if ($debug) $msg[ 'bug' ] .= __LINE__.' '.$query . '</br>';
$results = $database->query( $query );
if ( !$results || $results->numRows() == 0 ) {
  $msg[ 'inf' ] .= 'table creation attempt<br/>';
  $errors = array();
  foreach($jobs as $query) {$database->query( $query ); if ( $database->is_error() ) $errors[] = $database->get_error();}
  if (count($errors) > 0) $admin->print_error( implode("<br />n", $errors), 'javascript: history.go(-1);');
  // data creatie uitgevoerd 
}
// pickup settings if any
$query = "SELECT * FROM `" . CH_DBBASE ."` WHERE `section`='0' AND `table`='".$regelsArr ['app']."' ORDER BY `id`";
if ($debug) $msg[ 'bug' ] .= __LINE__.' '.$query . '</br>';
$results = $database->query( $query ); 
if ( $results && $results->numRows() > 0 ) {
  while ( $row = $results->fetchRow() ) { $settingArr[$row['name']]= $row['value']; }
}
if (!isset($settingArr['opzoek'])) $settingArr['opzoek']='name';
if ( $debug ) Gsm_debug( $settingArr, __LINE__);
/*
 * processing settings array
 */
if (!isset( $settingArr ['mode'])) $settingArr ['mode']= ""; 
if (CH_LOC == "front") {
  if (strstr($settingArr ['mode'], "lis")) $regelsArr ['pdf_ok'] = true;
  if (strstr($settingArr ['mode'], "cha")) {
    $regelsArr ['edit_ok'] = true;
    if (strstr($settingArr ['mode'], "add")) $regelsArr ['add_ok'] = true;
  }
} elseif (CH_LOC == "tools"){
  $regelsArr ['pdf_ok'] = true;
  $regelsArr ['edit_ok'] = true;
  $regelsArr ['add_ok'] = true;
} else {
  $regelsArr ['pdf_ok'] = true;
  $regelsArr ['add_ok'] = true;
  $regelsArr ['edit_ok'] = true;
  if (strstr($settingArr ['mode'], "alter")) {
   $regelsArr[ 'mode'] = 1;
   $regelsArr ['pdf_ok'] = false;
  }
}
if ( $debug ) {
  Gsm_debug( $settingArr, __LINE__);
  Gsm_debug( $regelsArr, __LINE__ );
  Gsm_debug( $_POST, __LINE__ );
  Gsm_debug( $_GET, __LINE__ );
  Gsm_debug( $place, __LINE__ );
  }
/*
 * get the fields
 */
$query ="DESCRIBE ". $regelsArr[ 'table' ];
$message = __LINE__.$MOD_GSMOFFT['TXT_ERROR_DATABASE'] . $query . "</br>";
if ($debug) $msg[ 'bug' ] .= __LINE__.' '.$query . '</br>';
$results = $database->query( $query );
if ( !$results || $results->numRows() == 0 ) die( $message );
$fieldArr= array ();
while ( $row = $results->fetchRow() ) { 
  $fieldArr[$row['Field']]= $row['Type']; 
}
if ( $debug ) Gsm_debug( $fieldArr, __LINE__ );
// all fields collected now remove the standard fields except name
unset ($fieldArr['id']);
unset ($fieldArr['zoek']);
unset ($fieldArr['updated']);
unset( $query );
// Koppen

if (isset( $settingArr ['head'])) {$regelsArr ['veldhead']= explode ("|",$settingArr ['head']); } 
else { 
  $i=1; 
  foreach ( $fieldArr as $key => $value) { $regelsArr ['veldhead'][$i] = $key; $i++; }
  $regelsArr ['veldhead'][0]=$i-1;
  $regelsArr['print_regels'] = ($regelsArr ['veldhead'][0]>3) ? $regelsArr ['veldhead'][0] : 1;
}
//  $regelsArr ['veldhead'][0]=''; //  ** heading names to be listed can be inserted, relevance of settings can be removed ** 
// Velden
if (isset( $settingArr ['field'])) { $regelsArr ['veldname']=explode ("|",$settingArr ['field']);} 
else { 
  $i=1; 
  foreach ( $fieldArr as $key => $value) { $regelsArr ['veldname'][$i] =$key; $i++; }
  $regelsArr ['veldname'][0]=$i-1;
  $regelsArr['print_regels'] = ($regelsArr ['veldhead'][0]>3) ? $regelsArr ['veldhead'][0] : 1;
}
//  $regelsArr ['veldname'][0]=''; //  ** heading names to be listed can be inserted, relevance of settings can be removed ** 
if ($debug) {
  Gsm_debug($fieldArr, __LINE__);
  Gsm_debug($regelsArr, __LINE__);
}
/*
 * selection ?
 */
if ( isset( $_POST[ 'selection' ] ) && strlen( $_POST[ 'selection' ] ) >= 2 ) {
    $regelsArr[ 'search' ] = trim( $_POST[ 'selection' ] );
    $help  = "%" . str_replace( ' ', '%', $regelsArr[ 'search' ] ) . "%";
    $regelsArr[ 'search_mysql' ] = " WHERE  `zoek` LIKE '" . $help . "'";
  }
/*
 * is a record selected
 */
if (isset($_POST['vink'][0]) && $_POST['vink'][0] > 0) $regelsArr['recid'] = $_POST['vink'][0];
if ($debug) $msg['bug'] .= __LINE__ . ' '.$regelsArr['recid'].' access <br/>';
if ($regelsArr['recid'] <1 && isset($_POST['recid']) && $_POST['recid'] > 0 ) $regelsArr['recid'] = $_POST['recid'];
if ($debug) $msg['bug'] .= __LINE__ . ' '.$regelsArr['recid'].' access <br/>';
if ($regelsArr['recid'] <1 && isset($_GET['recid']) && $_GET['recid'] > 0 ) $regelsArr['recid'] = $_GET['recid'];
if ($debug) $msg['bug'] .= __LINE__ . ' '.$regelsArr['recid'].' access <br/>';
if ($regelsArr['recid'] >0) {
  $regelsArr['memory']=$regelsArr['recid'];
  if ($debug) $msg['bug'] .= __LINE__ . ' '.$regelsArr['recid'].' access <br/>';
// if data is available get it from the database
  $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` WHERE `id`= '". $regelsArr['recid']."'";
  if ($debug) $msg['bug'] .= __LINE__.' '.$query.' <br/>';
  $results = $database->query($query);
  if (!$results || $results->numRows() >= 1) {
    $row = $results->fetchRow();
    foreach ($fieldArr as $key => $value) {
      $regelsArr['x' . $key] = $row[$key];
    } //$fieldArr as $key => $value
  } //!$results || $results->numRows() >= 1
  if ($debug) Gsm_debug($regelsArr, __LINE__);
} //$regelsArr['recid'] >0
unset( $query );
/*
 * some job to do ?
 */
if (isset($_POST['command'])) {
/*
 * process the input 
 */ 
  foreach ($fieldArr as $key => $value) {
    switch ($MOD_GSMOFFT['file_type'][$value]){
      case 1: //    'varchar(255)' 
        if (isset($_POST[$key])) $regelsArr['x' . $key]  = Gsm_eval($_POST[$key], 1, 255,0);   
        break;
      case 3: //    'decimal(9,2)' 
        if (isset($_POST[$key])) $regelsArr['x' . $key]  = Gsm_eval($_POST[$key], 8, 20000, 0);
        break;
      case 4: //    'varchar(63)' 
        if (isset($_POST[$key])) $regelsArr['x' . $key]  = Gsm_eval($_POST[$key], 1, 63, 0);  
        break;
      case 5: //    'varchar(127)' 
        if (isset($_POST[$key])) $regelsArr['x' . $key]  = Gsm_eval($_POST[$key], 1, 127, 0); 
        break;
      case 6: //    'date' 
        if (isset($_POST[$key])) $regelsArr['x' . $key] = Gsm_eval($_POST[$key], 9, '2020-01-01', '1970-01-01');
        break;
      case 2: //    'int(11)' 
      case 7: //    'int(7)'   
      default:
        if (isset($_POST[$key])) $regelsArr['x' . $key] = $_POST[$key];
        break;
    }
  } //$fieldArr as $key => $value
if ($debug) {
  Gsm_debug($fieldArr, __LINE__);
  Gsm_debug($regelsArr, __LINE__);
}

 switch ( $_POST[ 'command' ] ) {
    case $MOD_GSMOFFT[ 'tbl_icon' ][0]:
      // check selection
      if ( isset( $_POST[ 'sel' ] ) && strlen( $_POST[ 'sel' ] ) >= 1 ) {
        $regelsArr[ 'search' ] = trim( $_POST[ 'sel' ] );
        $help = "%" . str_replace( ' ', '%', $regelsArr[ 'search' ] ) . "%";
        $regelsArr[ 'search_mysql' ] .= "WHERE `zoek` LIKE '" . $help . "'";
      }
      // volgende pagina ?
      if ( isset( $_POST[ 'nxt' ] ) ) {
        $har = explode ("|", trim($_POST[ 'nxt' ]));
        if (isset ($har[1]) && $har[1]==$regelsArr[ 'search' ] ){
          $regelsArr[ 'seq' ] = $har[0];
        }
      }
      $regelsArr[ 'mode'] = 9;
      break; 
    case $MOD_GSMOFFT[ 'tbl_icon' ][1]: // wijzigen/edit
      $regelsArr[ 'recid' ] = $_POST[ 'recid' ];
      $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`WHERE `id`='" . $regelsArr[ 'recid' ] . "'";
      $regelsArr[ 'mode'] = 7;
      break;
    case $MOD_GSMOFFT[ 'tbl_icon' ][3]: //Toevoegen/add
      $regelsArr[ 'qty' ]= 3;
      $regelsArr[ 'mode'] = 8;
      break;
    case $MOD_GSMOFFT[ 'tbl_icon' ][4]: // opslaan 
      if (isset ($_POST[ 'recid' ]) &&  $_POST[ 'recid' ] >=1 ) {
        $regelsArr[ 'recid' ] = $_POST[ 'recid' ];
        $regelsArr ['record_update'] = true;
      }
    case $MOD_GSMOFFT[ 'tbl_icon' ][5]: 
      if ( !isset( $_SESSION[ 'page_h' ] ) || ( $_SESSION[ 'page_h' ] <> $_POST[ 'sh' ] ) ) {
        $msg[ 'err' ] .= $MOD_GSMOFFT[ 'error4' ] . '</br>';
        unset ($_POST);
        break;}
      $hulpArr = array(); // array field 
      foreach ($fieldArr as $key => $value ){
        if (isset($_POST[ $key ])) { 
          $hulpArr[$key]=stripslashes(htmlspecialchars($_POST[ $key ])); 
        } else { 
          $hulpArr[$key]="";
        }
      }
      $hulpArr['zoek']=""; // voeg zoek toe
      $hulp=explode ("|", $settingArr['opzoek']);
      foreach ($hulp as $key=> $value) { 
        if (isset($hulpArr[$value])) $hulpArr['zoek'].=$hulpArr[$value]."|";
      }
      $hulpArr['zoek'].=$regelsArr[ 'recid' ];
      if ($regelsArr ['record_update']) {
        $query = "UPDATE `".$regelsArr ['table']."` SET ".Gsm_parse (2,$hulpArr)."  WHERE  `id`= '".$regelsArr ['recid']."'";
        $msg[ 'bug' ] .= '<br/>id '.__LINE__." ".$query.'<br/>';
        $results = $database->query( $query );
        $msg[ 'inf' ] .= 'Updated'.'<br/>';
      } else {
        $query = "INSERT INTO `".$regelsArr[ 'table' ]."` ". Gsm_parse (1,$hulpArr);
        $msg[ 'bug' ] .= __LINE__." ".$query.'<br/>';
        $results = $database->query( $query );
        $msg[ 'inf' ] .= $MOD_GSMOFFT['added'].'<br/>'; 
      }
      unset( $query );
      $regelsArr[ 'mode'] = 9;
      break;
    case $MOD_GSMOFFT[ 'tbl_icon' ][6]: // delete
      $regelsArr[ 'recid' ] = $_POST[ 'recid' ];
      if ( !isset( $_SESSION[ 'page_h' ] ) || ( $_SESSION[ 'page_h' ] <> $_POST[ 'sh' ] ) ) {
        $msg[ 'err' ] .= $MOD_GSMOFFT[ 'ERR0R4' ] . '</br>';
        unset ($_POST);
        break;}
      unset( $_SESSION[ 'page_h' ] );
      $query = "DELETE FROM `". $regelsArr[ 'table' ] . "` WHERE `id`='". $regelsArr[ 'recid' ] . "'";
      $results = $database->query( $query );
      $msg[ 'inf' ] .= $MOD_GSMOFFT['deleted'].'<br/>'; 
      unset( $query );
      $regelsArr[ 'mode'] = 9;
      break;
    case $MOD_GSMOFFT[ 'tbl_icon' ][10]: // veld toevoegen
      if ($regelsArr[ 'mode'] == 1 ) {
        if(isset ($_POST[ 'veld' ]) && isset ($_POST[ 'veld_type' ])) {
          $query = "ALTER TABLE `". $regelsArr[ 'table' ] . "` ADD `".str_replace(" ", "_", trim($_POST[ 'veld' ]))."` ";
          $i=1;
          foreach ($MOD_GSMOFFT['file_type'] as $key => $value) {
            if ($_POST[ 'veld_type' ] == $value) { $i=$key;}
            }
          $query .= $i;
          $query .= " NOT NULL";
          $results = $database->query( $query );
          $query ="DESCRIBE ". $regelsArr[ 'table' ];
          $message = __LINE__.$MOD_GSMOFFT['error0'] . $query . "</br>";
          if ($debug) $msg[ 'bug' ] .= __LINE__.' '.$query . '</br>';
          $results = $database->query( $query );
          if ( !$results || $results->numRows() == 0 ) die( $message );
          $fieldArr= array ();
          while ( $row = $results->fetchRow() ) { 
            $fieldArr[$row['Field']]= $row['Type']; 
          }
          if ( $debug ) Gsm_debug( $fieldArr, __LINE__ );
          // all fields collected now remove the standard fields except name
          unset ($fieldArr['id']);
          unset ($fieldArr['zoek']);
          unset ($fieldArr['updated']);
          unset( $query );
        }
        $regelsArr[ 'mode'] = 1;
      } else { 
        $regelsArr[ 'mode'] = 9; 
      }
      break;
    case $MOD_GSMOFFT['tbl_icon'][11]: //print
	    $regelsArr['filename_pdf'] = strtolower( $regelsArr[ 'project' ] . $regelsArr[ 'today_pf' ] ) . '.pdf';
      $regelsArr ['mode'] = 9;
      break;
    default:
      $regelsArr[ 'recid' ] = "";
      $regelsArr[ 'mode'] = 9;
      break;
  }
} elseif ( isset( $_GET[ 'command' ] ) ) {
  switch ( $_GET[ 'command' ] ) {
    case 'view':
      $regelsArr[ 'recid' ] = $_GET[ 'recid' ];
      $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`WHERE `id`='" . $regelsArr[ 'recid' ] . "'";
      $regelsArr[ 'mode'] = 6;
      break;
    default:
      $regelsArr[ 'mode'] = 9;
      break;
  }
} else { // so standard display
  if ($debug) $msg[ 'bug' ] .= __LINE__.' access <br/>';
  /*
   * standard display job with or without search
   */
}
if (!isset($query) && $regelsArr ['mode']==9 ) {
  // loop through the records
  $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`" . $regelsArr[ 'search_mysql' ] . " ORDER BY `".$regelsArr['volgorde']."`";
  if ($debug) $msg[ 'bug' ] .= __LINE__.' '.$query.' <br/>';
  $results = $database->query( $query );
  $regelsArr ['n'] = $results->numRows();
  if (isset($regelsArr['filename_pdf'])) {
    /*
     * pdf process initiation
     */
    $pdf = new PDF();
    global $title;    
    global $owner;
    $owner = $regelsArr[ 'owner' ];          
    $title = ucfirst($regelsArr['app']);
    $run = date("Ymd_His");
    $pdf->AliasNbPages();
    $pdf_text='';
    $pdf_data = array();
    $pdf->AddPage();
    $pdf->ChapterTitle(1,ucfirst($regelsArr['app']));
    $pdf->SetFont('Arial','',8);
    $pdf_text .= CH_CR. $settingArr['company'].CH_CR;
    if (strlen($regelsArr[ 'search' ]) >1) $pdf_text .=CH_CR."Selection : " . $regelsArr[ 'search' ]; 
    $pdf_text .= $regelsArr['filename_pdf'].CH_CR ;
    $pdf_text .= "Document created on : " . $run . CH_CR;
// header
    if ($regelsArr['print_regels'] >1 ) { 
      $pdf_header = array('id', ucfirst($MOD_GSMOFFT['tbl_label']), ucfirst($MOD_GSMOFFT['tbl_value']));
    } else {
      $pdf_header = array();
        for ($i=1;$i<=6;$i++) { 
        if (isset ($regelsArr[ 'veldhead' ][$i]) && strlen($regelsArr[ 'veldhead' ][$i])>1) {
          $pdf_header[]=ucfirst($regelsArr[ 'veldhead' ][$i]); 
        }
      }
// $pdf_header = array(ucfirst($regelsArr[ 'veldhead' ][1]), ucfirst($regelsArr[ 'veldhead' ][2]),  ucfirst($regelsArr[ 'veldhead' ][3]), ucfirst($regelsArr[ 'veldhead' ][4]), ucfirst($regelsArr[ 'veldhead' ][5]),ucfirst($regelsArr[ 'veldhead' ][6]));
    }
// loop through records
    while ( $row = $results->fetchRow() ) {
      if ($regelsArr['print_regels'] >1 ) { 
        for($i=1;$i<=$regelsArr['print_regels'];$i++) {
          if ($i==1) {
            $line = sprintf("%s;%s;%s;;;",
               $row[$regelsArr[ 'veldname' ][$i]]." (".$row['id'].")",
               ucfirst($regelsArr[ 'veldhead' ][$i]),
               "",
               "");
              $pdf_data[] = explode(';',trim($line)); 
          } else {
            $line = sprintf("%s;%s;%s;;;",
              '',
              (isset($regelsArr[ 'veldname' ][$i])) ? ucfirst($regelsArr[ 'veldhead' ][$i]) : '',
              (isset($regelsArr[ 'veldname' ][$i]) && isset($row[$regelsArr[ 'veldname' ][$i]])) ? $row[$regelsArr[ 'veldname' ][$i]] : '');
              $pdf_data[] = explode(';',trim($line)); 
          }
        }
        $line = ";;;;;";
        $pdf_data[] = explode(';',trim($line));
      } else {
        $line = sprintf("%s;%s;%s;%s;%s;%s",
          (isset($regelsArr[ 'veldname' ][1]) && isset($row[$regelsArr[ 'veldname' ][1]])) ? $row[$regelsArr[ 'veldname' ][1]]." (".$row['id'].")" : '',
          (isset($regelsArr[ 'veldname' ][2]) && isset($row[$regelsArr[ 'veldname' ][2]])) ? $row[$regelsArr[ 'veldname' ][2]] : '',
          (isset($regelsArr[ 'veldname' ][3]) && isset($row[$regelsArr[ 'veldname' ][3]])) ? $row[$regelsArr[ 'veldname' ][3]] : '',
          (isset($regelsArr[ 'veldname' ][4]) && isset($row[$regelsArr[ 'veldname' ][4]])) ? $row[$regelsArr[ 'veldname' ][4]] : '',
          (isset($regelsArr[ 'veldname' ][5]) && isset($row[$regelsArr[ 'veldname' ][5]])) ? $row[$regelsArr[ 'veldname' ][5]] : '',
          (isset($regelsArr[ 'veldname' ][6]) && isset($row[$regelsArr[ 'veldname' ][6]])) ? $row[$regelsArr[ 'veldname' ][6]] : ''
        );
        $pdf_data[] = explode(';',trim($line)); 
      }
    }
    $pdf->DataTable( $pdf_header, $pdf_data, $regelsArr[ 'cols' ] );
    if (strlen($regelsArr[ 'search' ]) >= 2) $pdf_text .=CH_CR.$MOD_GSMOFFT['tbl_selectie'] . $regelsArr[ 'search' ];
    if ($regelsArr[ 'volgorde' ] != 'name' ) $pdf_text .=CH_CR.$MOD_GSMOFFT['tbl_volgorde'] . $regelsArr[ 'volgorde' ];
    $pdf_text .=CH_CR.$MOD_GSMOFFT['tbl_aantal'] . $regelsArr ['n'];
    $pdf->ChapterBody( $pdf_text );
    $pdf->Output($place['pdf'].$regelsArr['filename_pdf'], 'F');
    $msg[ 'inf' ] .= $MOD_GSMOFFT['tbl_overzicht'] .$regelsArr['filename_pdf'].'</br>';
  }
  $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` " . $regelsArr[ 'search_mysql' ] . " ORDER BY `".$regelsArr['volgorde']."` LIMIT " . $regelsArr[ 'seq' ] . ", " . $regelsArr[ 'qty' ];
} 
if (!isset ($query))  $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` " . $regelsArr[ 'search_mysql' ] . " ORDER BY `".$regelsArr['volgorde']."` LIMIT " . $regelsArr[ 'seq' ] . ", " . $regelsArr[ 'qty' ];
// at this point the update is done and the mode and databse query are prepared database query for the relevant records prepared
if ($debug) $msg[ 'bug' ] .= __LINE__ ." mode: ". $regelsArr[ 'mode']. ' query: ' . $query . '</br>';
// at this point the database query for the relevant records is prepared
/*
 * display preparation
 */
switch ( $regelsArr[ 'mode'] ) {
  case 1: //alter
    $tint=false;
    foreach ( $fieldArr as $key => $value) { 
      if ($tint) {$hulp = $MOD_GSMOFFT['line_color'] [2]; $tint=false;} else { $hulp=""; $tint=true;}
      $regelsArr[ 'descr' ] .= sprintf( $TEMPLATE[ 91 ],$hulp,    
        $key,
        $value,
        '','','','',''
      );
    }
    $tint=false;
    if ($tint) {$hulp = $MOD_GSMOFFT['line_color'] [2]; $tint=false;} else { $hulp=""; $tint=true;}
    $regelsArr[ 'descr' ] .= sprintf( $TEMPLATE[ 91 ], $hulp, 
        '<input maxlength="20" size="20" type="text" name="veld" value="" placeholder="veld" />', 
        '<SELECT name="veld_type" >'.Gsm_option( $MOD_GSMOFFT['autocomplete'], $MOD_GSMOFFT['file_type'][1] ).'</SELECT>',
        '','','','',''
    );
    break;
  case 7: // Update
    $results = $database->query( $query );
    $row = $results->fetchRow();
    $regelsArr[ 'update' ] = $row['updated'];
  case 8: // invoer
    $regelsArr[ 'head' ]  .= sprintf( $TEMPLATE[ 90 ], "", ucfirst($MOD_GSMOFFT['tbl_label']), ucfirst($MOD_GSMOFFT['tbl_value']),'','','','','');
    foreach ($regelsArr ['veldname'] as $key=>$value) {
      $h1 = (isset($row[$value])) ? $row[$value] : "";
      $h2 = $key;
      if ($h2!=0) { 
      if (isset($regelsArr ['veldhead'][$h2])) {
        switch ( $MOD_GSMOFFT['file_type'][$fieldArr[$value]] ) {
          case 1: //Text veld varchar(255)
            $regelsArr[ 'descr' ] .=sprintf($TEMPLATE[ 92 ], "", sprintf($TEMPLATE[ 72 ], ucfirst($regelsArr ['veldhead'][$h2])), 
              sprintf($TEMPLATE[ 74 ], $regelsArr ['veldhead'][$h2], 255, 55, $h1, ucfirst($MOD_GSMOFFT['autocomplete'][1])), "", "");
            break;
          case 2: //Veld met int(11)
            $regelsArr[ 'descr' ] .=sprintf($TEMPLATE[ 92 ], "", sprintf($TEMPLATE[ 72 ], ucfirst($regelsArr ['veldhead'][$h2])), 
              sprintf($TEMPLATE[ 74 ], $regelsArr ['veldhead'][$h2], 12, 15, $h1, ucfirst($MOD_GSMOFFT['autocomplete'][2])), "", ""); 
            break;
          case 3: //Veld met amount)
            $regelsArr[ 'descr' ] .=sprintf($TEMPLATE[ 92 ], "", sprintf($TEMPLATE[ 72 ], ucfirst($regelsArr ['veldhead'][$h2])), 
              sprintf($TEMPLATE[ 74 ], $regelsArr ['veldhead'][$h2], 12, 15, $h1, ucfirst($MOD_GSMOFFT['autocomplete'][3])), "", ""); 
            break;
          case 4: //Veld met E-mail address varchar(63)
            $regelsArr[ 'descr' ] .=sprintf($TEMPLATE[ 92 ], "", sprintf($TEMPLATE[ 72 ], ucfirst($regelsArr ['veldhead'][$h2])), 
              sprintf($TEMPLATE[ 74 ], $regelsArr ['veldhead'][$h2], 63, 55, $h1, ucfirst($MOD_GSMOFFT['autocomplete'][4])), "", "");
            break;
          case 5: //Veld met een URL varchar(127)
            $regelsArr[ 'descr' ] .=sprintf($TEMPLATE[ 92 ], "", sprintf($TEMPLATE[ 72 ], ucfirst($regelsArr ['veldhead'][$h2])), 
              sprintf($TEMPLATE[ 74 ], $regelsArr ['veldhead'][$h2], 127, 55, $h1, ucfirst($MOD_GSMOFFT['autocomplete'][5])), "", "");
            break;
          case 6: //Veld met int(11)
            $regelsArr[ 'descr' ] .=sprintf($TEMPLATE[ 92 ], "", sprintf($TEMPLATE[ 72 ], ucfirst($regelsArr ['veldhead'][$h2])), 
              sprintf($TEMPLATE[ 74 ], $regelsArr ['veldhead'][$h2], 12, 15, $h1, $MOD_GSMOFFT['autocomplete'][6]), "", "");
            break;
          case 7: //Veld met Ja/Nee flag int(7)
            $regelsArr[ 'descr' ] .=sprintf($TEMPLATE[ 92 ], "", sprintf($TEMPLATE[ 72 ], ucfirst($regelsArr ['veldhead'][$h2])), 
              sprintf($TEMPLATE[ 74 ], $regelsArr ['veldhead'][$h2], 12, 15, $h1, ucfirst($MOD_GSMOFFT['autocomplete'][7])), "", "");
            break;
          default: // new list
            $regelsArr[ 'descr' ] .=sprintf($TEMPLATE[ 92 ], "", sprintf($TEMPLATE[ 72 ], ucfirst($h3)), 
              sprintf($TEMPLATE[ 74 ], $regelsArr ['veldhead'][$h2], 12, 55, $h1, "" ), "", "");  
            break;
        }
      }
      }
    }
    break;
  default: // default list 
    $results = $database->query( $query );
    if ( $results && $results->numRows() > 0 ) {
      if ($regelsArr[ 'mode'] == 6) {
        $regelsArr[ 'head' ]  .= sprintf( $TEMPLATE[ 90 ],ucfirst($regelsArr ['veldhead'][1]), ucfirst($MOD_GSMOFFT['tbl_label']), ucfirst($MOD_GSMOFFT['tbl_value']),'','','','','');
      } else {
        $regelsArr[ 'head' ]  .= sprintf( $TEMPLATE[ 90 ],
        (isset($regelsArr[ 'veldhead' ][1])) ? ucfirst($regelsArr[ 'veldhead' ][1]) : '', 
        (isset($regelsArr[ 'veldhead' ][2])) ? ucfirst($regelsArr[ 'veldhead' ][2]) : '', 
        (isset($regelsArr[ 'veldhead' ][3])) ? ucfirst($regelsArr[ 'veldhead' ][3]) : '',
        (CH_LOC == "front" && isset($regelsArr[ 'veldhead' ][4])) ? ucfirst($regelsArr[ 'veldhead' ][4]) : '',
        (CH_LOC == "front" && isset($regelsArr[ 'veldhead' ][5])) ? ucfirst($regelsArr[ 'veldhead' ][5]) : ''); 
      }
      $tint=true;
      while ( $row = $results->fetchRow() ) {
        if ($regelsArr[ 'mode']== 6) {
          $tint=true;
          foreach ($regelsArr ['veldname'] as $key => $value) {
            if ($key >0 ) {
              if ($tint) {$hulp = $MOD_GSMOFFT['line_color'] [2]; $tint=false;} else { $hulp=""; $tint=true;}
              if ($key ==1 ) {
                 if (isset($regelsArr ['veldhead'][$key])) {
                  $regelsArr[ 'descr' ] .= sprintf( $TEMPLATE[ 91 ],$hulp, 
//                    ucfirst($regelsArr ['veldhead'][$key]),
                    (isset($row[$value])) ? $row[$value] : "",
                    '','','','',''); }
              } else {
                if (isset($regelsArr ['veldhead'][$key])) {
                  $regelsArr[ 'descr' ] .= sprintf( $TEMPLATE[ 91 ],$hulp, 
                    '',
                    ucfirst($regelsArr ['veldhead'][$key]),
                    (isset($row[$value])) ? $row[$value] : "",
                    '','','','',''); }
              }
            }
          }
          if (CH_LOC != "front" ) {
            $regelsArr[ 'descr' ] .=sprintf($TEMPLATE[ 91 ], "", "", "Id", $row['id'], '','','','',''); 
            $regelsArr[ 'descr' ] .=sprintf($TEMPLATE[ 91 ], "", "", "Zoek", $row['zoek'], '','','','',''); 
            $regelsArr[ 'descr' ] .=sprintf($TEMPLATE[ 91 ], "", "", "Updated", $row['updated'],'','','','',''); 
          }          
        } else {
          if ($tint) {$hulp = $MOD_GSMOFFT['line_color'] [2]; $tint=false;} else { $hulp=""; $tint=true;}
          $regelsArr[ 'descr' ] .= sprintf( $TEMPLATE[ 98 ],$hulp, $row['id'], 
            (isset($regelsArr[ 'veldname' ][1]) && isset($row[$regelsArr[ 'veldname' ][1]]) && strlen($row[$regelsArr[ 'veldname' ][1]]) >0 ) ? $row[$regelsArr[ 'veldname' ][1]] : '--',
            (isset($regelsArr[ 'veldname' ][2]) && isset($row[$regelsArr[ 'veldname' ][2]])) ? $row[$regelsArr[ 'veldname' ][2]] : '',
            (isset($regelsArr[ 'veldname' ][3]) && isset($row[$regelsArr[ 'veldname' ][3]])) ? $row[$regelsArr[ 'veldname' ][3]] : '',
            (CH_LOC == "front" && isset($regelsArr[ 'veldname' ][4]) && isset($row[$regelsArr[ 'veldname' ][4]])) ? $row[$regelsArr[ 'veldname' ][4]] : '',
            (CH_LOC == "front" && isset($regelsArr[ 'veldname' ][5]) && isset($row[$regelsArr[ 'veldname' ][5]])) ? $row[$regelsArr[ 'veldname' ][5]] : ''
          );
        } 
      }
    } else {
      $regelsArr[ 'descr' ] .= $MOD_GSMOFFT[ 'nodata' ];
    }
    break;
}
/*
 * Selection
 */
switch ( $regelsArr[ 'mode'] ) {
  case 6: // detail
    $regelsArr[ 'select' ] .=$TEMPLATE[ 96 ];  
    if (isset($settingArr['opinfo'])) {$regelsArr[ 'select' ] .=sprintf( $TEMPLATE[ 93 ], "", stripslashes(htmlspecialchars($settingArr['opinfo'])));}
    $regelsArr[ 'select' ] .=sprintf( $TEMPLATE[ 91 ], "", 
      ($regelsArr ['edit_ok']) ? $ICONTEMP[ 1 ] : "", 
      $ICONTEMP[ 2 ], "" ,"" , 
      ($regelsArr ['add_ok']) ?$ICONTEMP[ 6 ] : "" );
    break;  
  case 7: // Update
    $regelsArr[ 'select' ] .=$TEMPLATE[ 96 ];  
    if (isset($settingArr['opinfo'])) {$regelsArr[ 'select' ] .=sprintf( $TEMPLATE[ 93 ], "", stripslashes(htmlspecialchars($settingArr['opinfo'])));}
    $regelsArr[ 'select' ] .=sprintf( $TEMPLATE[ 91 ], "", 
      ($regelsArr ['edit_ok']) ?$ICONTEMP[ 4 ] : "", 
      $ICONTEMP[ 2 ],"","", 
      ($regelsArr ['add_ok']) ?$ICONTEMP[ 5 ] : "");
    break;
  case 8: // Nieuw
    $regelsArr[ 'select' ] .=$TEMPLATE[ 96 ];  
    if (isset($settingArr['opinfo'])) {$regelsArr[ 'select' ] .=sprintf( $TEMPLATE[ 93 ], "", stripslashes(htmlspecialchars($settingArr['opinfo'])));}
    $regelsArr[ 'select' ] .=sprintf( $TEMPLATE[ 91 ], "", 
      ($regelsArr ['edit_ok']) ? $ICONTEMP[ 4 ] : "", 
      $ICONTEMP[ 2 ] ,"","", "");
    break;  
  default: // new list
    $regelsArr[ 'select' ] .=$TEMPLATE[ 96 ];
    if (isset($settingArr['opinfo'])) {$regelsArr[ 'select' ] .= sprintf( $TEMPLATE[ 93 ], "", stripslashes(htmlspecialchars($settingArr['opinfo']))); }
    $regelsArr[ 'select' ] .=sprintf( $TEMPLATE[ 97 ], "", "", Gsm_next ($regelsArr[ 'search' ], $regelsArr[ 'n' ] ,$regelsArr[ 'seq' ], $regelsArr[ 'qty' ] ), "", "");
    $regelsArr[ 'select' ] .=sprintf( $TEMPLATE[ 91 ], 
      "", 
      ($regelsArr ['add_ok']&& $regelsArr[ 'mode'] !=1) ? $ICONTEMP[ 3 ] : "",
      ($regelsArr[ 'mode'] ==1) ? $ICONTEMP[ 10 ] : "",
      ($regelsArr ['pdf_ok']) ? $ICONTEMP[ 11 ]: "",
      (isset($regelsArr['filename_pdf'])) ? sprintf($ICONTEMP[18], "", $regelsArr['filename_pdf']) : "",
      "");
    break;
}  
/*
 * display
 */
switch ( $regelsArr ['mode'] ) {
  case 9: // display
  default: // default
    $_SESSION[ 'page_h' ] = sha1( MICROTIME() . $_SERVER[ 'HTTP_USER_AGENT' ] );
    $parseViewArray = array(
      'header' => strtoupper ($regelsArr ['project']),
      'page_id' => $page_id,	
      'section_id' => $section_id,
      'kopregels' => $regelsArr[ 'head' ],
      'description' => $regelsArr[ 'descr' ],
      'message' => message( $msg, $debug ),
      'selection' => $regelsArr[ 'select' ],
      'return' => CH_RETURN,
      'parameter' => $regelsArr[ 'search' ],
      'sel' => $regelsArr[ 'search' ],
      'module' => $regelsArr ['module'],
      'mod' => $regelsArr ['modulen'],
      'memory' => $regelsArr[ 'memory' ]."|",   
      'toegift' => $regelsArr[ 'toegift' ],        
      'recid' => $regelsArr[ 'recid' ],
      'rapportage' => $regelsArr[ 'rapport' ],
      'hash' => $_SESSION[ 'page_h' ]);
    $prout .= $TEMPLATE[ 1 ];
    foreach ( $parseViewArray as $key => $value ) { $prout = str_replace( "{" . $key . "}", $value, $prout );}
    break;
}
if (strstr($set_mode, "vers")) {$prout .= "<small>".$regelsArr ['modulen'].$regelsArr ['versie']."</small>";}
?> 