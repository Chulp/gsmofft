<?php
/*
 *  @module         Office toolset
 *  @version        see info.php of this module
 *  @author         Gerard Smelt
 *  @copyright      2015, Gerard Smelt
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
  'module' => 'reload',
// voor versie display
  'modulen' => 'treload',
  'versie' => ' v20150916 ',
// display fields
  'table' => '',
  'descr' => '',
  'head' => '',
  'kopregels' => '' ,	
);
/*
 * Lay-out strings
 */
$TEMPLATE[ 1 ]   = '	
  <div class="container">
    {header}
    {message}
    <table class="inhoud" width="100%">
      {kopregels}
      {description}
     </table>
  </div>';
$LINETEMP[ 1 ]  = '
	<thead>
	<tr '.$MOD_GSMOFF['line_color']['4'] .' >
	<th width="25%">'.$MOD_GSMOFF['TH_FILE'].'</th>
	<th width="75%">&nbsp;</th>
	</tr>
	</thead>';
$LINETEMP[ 2 ]    = '<tr %1$s><td>%2$s</td><td>%3$s</td></tr>';	
$LINETEMP[ 3 ]    = '<a href="' . CH_RETURN . '&command=reload&fileid=%1$s">%2$s</a>';	
$regelsArr['kopregels'] = $LINETEMP[ 1 ] ;	
/*
 * pick up settings, create certain tables / data if not existing 
 */
$query = "SELECT * FROM `" . CH_DBBASE ."` WHERE `name`='opzoek' ORDER BY `table`";
$results = $database->query( $query ); 
if ( !$results || $results->numRows() != 0 ) {
	while ( $row = $results->fetchRow() ) { 
		$settingArr[$row['table']]= $row['value']; 
	}
}
$results = $database->query( $query ); 
if ( !$results || $results->numRows() != 0 ) {
	while ( $row = $results->fetchRow() ) { 
		$settingArr[$row['table']]= $row['value']; 
	}
}
$regelsArr ['head'].='<h2>'.(isset ($MOD_GSMOFF['menu'][$regelsArr ['modulen']])) ? $MOD_GSMOFF['menu'][$regelsArr ['modulen']] :$regelsArr ['module'].'</h2>';
if ( $debug ) Gsm_debug( $regelsArr, __LINE__ );
if ( $debug ) Gsm_debug( $settingArr, __LINE__);
if ( $debug ) Gsm_debug( $_POST, __LINE__ );
if ( $debug ) Gsm_debug( $_GET, __LINE__ );
if ( $debug ) Gsm_debug( $place, __LINE__ );
 /******************
 *
 * some job to do
 * standard parameters 
 * com = command
 * recid = record ID
 * sel + selection string
 */
 $mode_display = 0;
 if ( isset( $_POST[ 'command' ] ) ) {
	switch ( $_POST[ 'command' ] ) {
		default:
			$mode_display = 9;
			break;
	}
} elseif ( isset( $_GET[ 'command' ] ) ) {
	switch ( $_GET[ 'command' ] ) {
		case 'reload':
			$backup=$_GET[ 'fileid' ];
			$regelsArr[ 'table' ] = substr($_GET[ 'fileid' ], 0, -21);
			if (strlen($regelsArr[ 'table' ]) > strlen(CH_DBBASE)+1) {
				$table_name= str_replace ( CH_DBBASE."_", "", $regelsArr[ 'table' ]);
			} else { $table_name='';
			}
			$query = "SHOW TABLES LIKE '" . $regelsArr[ 'table' ]."_bck'";
			$results = $database->query( $query );
			if ( $results && $results->numRows() != 0 ) {
// remove if existing	
				$query ="DROP TABLE ".$regelsArr[ 'table' ]."_bck";
				$results = $database->query($query);
			}
// move old one and create new one
			$query = "RENAME TABLE " . $regelsArr[ 'table' ]." TO " . $regelsArr[ 'table' ]."_bck";
			$results = $database->query($query);
			$query = "CREATE TABLE " . $regelsArr[ 'table' ]." LIKE " . $regelsArr[ 'table' ]."_bck";
			$results = $database->query($query);
// Fields
			$query ="DESCRIBE ". $regelsArr[ 'table' ];
			$results = $database->query( $query );
			if ( isset($results) && $results->numRows() != 0 ) {
				$fieldArray = array ();
				while ( $row = $results->fetchRow() ) { $fieldArray[$row['Field']]= $row['Type']; }
// all fields collected now remove the standard fields except name
				unset ($fieldArray['id']);
				unset ($fieldArray['zoek']);
				unset ($fieldArray['updated']);
			}
// zoek structuur
			if (!isset($settingArr[$table_name])) $settingArr[$table_name]='';
			if ($debug) $msg[ 'bug' ] .= __LINE__." table: ".$table_name." zoek: ".$settingArr[$table_name]."<br/>";
//  load file with csv data
			$filename_csv =  $place['backup2'].$backup;
			if (file_exists($filename_csv)) {
				if ($debug) $msg[ 'bug' ] .= __LINE__." ".'start load '.$filename_csv . ' into '.$regelsArr[ 'table' ].'</br>';
				$handle = fopen($filename_csv, "r");
				$header_now=true;
				while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
					if ( $header_now ) {
						$header_now=false;
						$insert ="`".implode("`,`",$data)."`";
// hoe ziet zoek eruit
						if (isset($settingArr[$table_name]) && strlen($settingArr[$table_name])>1) {
							$insert .=",zoek";
							$hulp=explode ("|", $settingArr[$table_name]);
							foreach ($hulp as $key=> $value) { foreach ($data as $key2=>$value2) { if ($value==$value2) { $zk[$key2]=$value2;} } }	
						}
					} else {
						if (isset($zk)){
							foreach ($zk as $key=>$value) { $zk[$key]=$data[$key];}
						}
						$data_insert = "'".implode("','",$data)."'";
						if (isset($settingArr[$table_name]) && strlen($settingArr[$table_name])>1) $data_insert .= ", '".implode("|",$zk)."'";
						$in_query = "INSERT INTO " .$regelsArr[ 'table' ] . "(".$insert.") values(".$data_insert.")";
						if ($debug) $msg[ 'bug' ] .= __LINE__." ".$in_query."<br/>";
						$in_results = $database->query( $in_query );	
					}
				}
				fclose($handle);
				$msg[ 'inf' ] .= 'load '.$filename_csv . ' into '.$regelsArr[ 'table' ].' completed</br>';
			}		
			break;
		default:
			$mode_display = 9;
			break;
	}
} 
/******************
 * Get tables
 */
$query = "SHOW TABLE STATUS LIKE '" . CH_DBBASE . "%'";
$results = $database->query($query);
if (!$results || $results && $results->numRows() == 0) {
	$descr_kop.=$MOD_GSMOFF['TXT_ERROR_DATABASE'];
} else {
	$i = 0;
	while($row = $results->fetchRow()) {
        if ( $debug ) Gsm_debug( $row, __LINE__ );
/******************
 * Check table
 */
		$query2 = "CHECK TABLE `" . $row[0]."`";
		$result2 = $database->query($query2);
		$row2 = $result2->fetchRow();
        if ( $debug ) Gsm_debug( $row2, __LINE__ );
		$regelsArr ['descr'].= sprintf ( $LINETEMP[ 2 ] , 
			($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['2'], 
			$row[0], 
			$row2[1]." ".$row2[2]." : ".$row2[3]);
		$regelsArr ['descr'].= sprintf ( $LINETEMP[ 2 ] , 
			($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['2'], 
			"", 
			"Engine : ".$row['Engine'].", Rows : ".$row['Rows']);
		$files = scandir( substr($place['backup2'], 0, -1), 1 );		
		$help="-- backup files";
		$r=0;
		foreach ($files as $key) {
			if ( $key != "." && $key != ".." ) {
				if (substr($key, 0, -21)==$row[0]) {
					if ($r>2) {
						unlink($place['backup2'].$key);
					} else {
						if (strstr($key, "_bck")) {
							$regelsArr ['descr'].= sprintf ( $LINETEMP[ 2 ] , 
								($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['2'], 
								$help, 
								"existing");
						} else {
							$regelsArr ['descr'].= sprintf ( $LINETEMP[ 2 ] , 
								($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['2'], 
								$help, 
								sprintf( $LINETEMP[ 3 ] , 
									$key ,
									htmlspecialchars($key)));
						}
						$help="";
						$r++;
						
					}
				}		
			}
		}
		$i++;
	}
}
$parseViewArray = array(
	'header' => $regelsArr ['head'], 
	'message' 		=> message( $msg, $debug ),
	'kopregels' => $regelsArr ['kopregels'], 
	'description' => $regelsArr ['descr'],
	'mod'          => $regelsArr ['module'],
	'sel'		=> ''
);
$prout .= $TEMPLATE[ 1 ];
foreach ( $parseViewArray as $key => $value ) { $prout = str_replace( "{" . $key . "}", $value, $prout ); }
if (strstr($set_mode, "vers")) {$prout .= "<small>".$regelsArr ['modulen'].$regelsArr ['versie']."</small>";}
?>