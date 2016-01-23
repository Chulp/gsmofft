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
/******************
 *
 * variable setting
 *
 */

$regelsArr = array(
// voor routing
  'mode' => 9,
  'module' => 'settings',
// voor versie display
  'modulen' => 'tsettings',
  'versie' => ' v20150916 ',
// display fields
  'table' => '',
  'descr' => '',
  'head' => '',
  'kopregels' => '' ,	
  'selection' => (isset($_POST['selection'])) ? $_POST['selection'] : '',
);
$prout='';
$regelsArr ['head'].=(isset ($MOD_GSMOFF['menu'][$regelsArr ['modulen']])) ? $MOD_GSMOFF['menu'][$regelsArr ['modulen']] : $regelsArr ['module'];
if ( $debug ) Gsm_debug( $regelsArr, __LINE__ );
if ( $debug ) Gsm_debug( $settingArr, __LINE__);
if ( $debug ) Gsm_debug( $_POST, __LINE__ );
if ( $debug ) Gsm_debug( $_GET, __LINE__ );
if ( $debug ) Gsm_debug( $place, __LINE__ );
/******************
 *
 * Functions
 *
 */
function process_row(  $func=1 , $input ) {
	//-------------------------------------------
	// func = 	1= input row processing
	//			2= add empty line
	// input is the row from Query
	// return format returnvalue to be displayed
	//-------------------------------------------
  global $MOD_GSMOFF;
  $oke         = true;
  $returnvalue = '';
  $TEMPLATE[ 11 ]  = '
	<tr><td><input maxlength="3" size="3" type="text" name="%1$s!%2$s!1!%3$s" value="%3$s" width="6" /> </td>
	<td><input maxlength="12" size="12" type="text" name="%1$s!%2$s!2!%4$s" value="%4$s" width="18" /> </td>
	<td><input maxlength="12" size="12" type="text" name="%1$s!%2$s!3!%5$s" value="%5$s" width="18" /> </td>
	<td><input maxlength="255" size="60" type="text" name="%1$s!%2$s!4!%6$s" value="%6$s" width="80" /></td></tr>';
  switch ( $func ) {
		case 1:
			// add line to edit
			$returnvalue .= sprintf( $TEMPLATE[11], 
				$func, 
				$input['id'],
				$input['section'],
				$input['table'],
				$input['name'],
				$input['value']);
			break;
		case 2:
			// add line to add
			$returnvalue .= '<tr> <td colspan="4">'.$MOD_GSMOFF['TH_ADD'].'</td></tr>';
			for ($i=1;$i<5;$i++) {
			$returnvalue .= sprintf( $TEMPLATE[11], 
				$func, 
				$i,
				'',
				'',
				'',
				'');
			}
			break;
		default:
			$oke = false;
			break;
	}
	if ($oke) return $returnvalue; return '' ;
}
/******************
 *
 * Lay-out strings
 * 
 */	
$TEMPLATE[ 1 ]   = '	
	<div class="container">
		{header}
		{message}
	</div>
	<div class="container">
	<form name="view" method="post" action="{return}">
	<table>
	<colgroup><col width="15%"><col width="20%"><col width="20%"><col width="45%"></colgroup>
	<thead><tr><th>{section}</th><th>{file}</th><th>{name}</th><th>{value}</th></tr></thead>
	{description}
	<tr><td></td><td colspan="3"><input type="submit" name="command" value="{wijzig}" />&nbsp;<input type="submit" name="submit" value="{terug}" /></td><tr>
		</form>
	</table>
	</div>';

/******************
 *
 * some job to do
 * standard parameters 
 * command = command
 * recid = record ID
 * sel + selection string
 */
 if ( isset( $_POST[ 'command' ] ) && $_POST[ 'command' ] == $MOD_GSMOFF ['edit'] ) {	
 // iets te wijzigen
 // install level break
  $L0='';	
  $Ldata=false;
  $hulpArre= array();
  $hulpArra= array();
  foreach ($_POST as $key => $value) {
  $hlp= explode ('!', $key, 4);
  if ( $debug ) Gsm_debug( $hlp, __LINE__ );		
    if (isset($hlp[3]) && str_replace('_',' ',$hlp[3]) != $value) {
  $msg[ 'bug' ] .= __LINE__.$key."|".$value."<br/>";
      switch ( $hlp[0] ) {
        case 1:
          // edit data
          if ($hlp[2]==1) $hulpArre ['section']=$value;
          if ($hlp[2]==2) $hulpArre ['table']=$value;
          if ($hlp[2]==3) $hulpArre ['name']=$value;
          if ($hlp[2]==4) $hulpArre ['value']=$value;
          $query = "UPDATE `" . CH_DBBASE ."` SET ".Gsm_parse (2,$hulpArre)."  WHERE `id` = '".$hlp[1]."'";
          unset($hulpArre);
          $msg[ 'bug' ] .= '<br/>'.__LINE__." ".$query.'<br/>';
          $results = $database->query( $query ); 
          $msg[ 'inf' ] .= 'data update</br>';
          break;
        case 2:
          // add data
          if ($hlp[1]!=$L0) {			
            // to save			
            if ($Ldata) {
              $query = "INSERT INTO `" . CH_DBBASE ."` ". Gsm_parse (1,$hulpArra);
              $msg[ 'bug' ] .= '<br/>'.__LINE__." ".$query.'<br/>';
              $results = $database->query( $query );
              $msg[ 'inf' ] .= 'data insert</br>';
            }
            $L0=$hlp[1];
            $Ldata=false;
            $hulpArra= array();
		  }
		  if ($hlp[2]==1) {$hulpArra ['section']=$value; $Ldata=true;}
		  if ($hlp[2]==2) {$hulpArra ['table']=$value; $Ldata=true;}
		  if ($hlp[2]==3) {$hulpArra ['name']=$value; $Ldata=true;}
		  if ($hlp[2]==4) {$hulpArra ['value']=$value; $Ldata=true;}
          break;
        default:
          // not for the update
          break;
	  }
    }
  }
  if ($Ldata) {
    $query = "INSERT INTO `" . CH_DBBASE ."` ". Gsm_parse (1,$hulpArra);
    $msg[ 'bug' ] .= '<br/>id '.__LINE__." ".$query.'<br/>';
    $results = $database->query( $query );
    $msg[ 'inf' ] .= 'data insert</br>';
  }
}
 /*
 * read all the records and display for edit
 */ 
$query = "SELECT * FROM `" . CH_DBBASE ."`  ORDER BY `section`, `table`, `name`";
$message = __LINE__.$MOD_GSMOFF['TXT_ERROR_DATABASE']. $query . "</br>";
$results = $database->query( $query ); 
if ( !$results || $results->numRows() == 0 ) die( $message );
while ( $row = $results->fetchRow() ) { 
  if ($debug) Gsm_debug($row, __LINE__);
  $regelsArr ['descr'] .= process_row (1, $row);
  }
  $regelsArr ['descr'] .= process_row (2, '');	
  $parseViewArray = array(
    'header' => "<h1>".$regelsArr ['head']."</h1>", 
    'kopregels' => $regelsArr ['kopregels'], 
	  'description' => $regelsArr ['descr'],
	  'terug' => $MOD_GSMOFF ['cancel'],			
	  'wijzig' => $MOD_GSMOFF ['edit'],	
	  'message' =>message( $msg, $debug ),
	  'return' => CH_RETURN,
	  'section' => $MOD_GSMOFF['TH_SECTION'],
	  'file' => $MOD_GSMOFF['TH_FILE'],
	  'name' => $MOD_GSMOFF['TH_NAME'],
	  'value' => $MOD_GSMOFF['TH_VALUE'],
	  'mod' => $regelsArr ['module'],
	  'sel' => $regelsArr ['selection']);
$prout .= $TEMPLATE[ 1 ];
foreach ( $parseViewArray as $key => $value ) {	$prout = str_replace( "{" . $key . "}", $value, $prout );}
if (strstr($set_mode, "vers")) {$prout .= "<small>".$regelsArr ['modulen'].$regelsArr ['versie']."</small>";}
?>