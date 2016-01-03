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
$regelsArr= array(
  'status' => 'v20141223',

  'module' => 'tbackup'
);
/******************
 * variable setting
 */
$regelsArr = array(
// voor routing
  'mode' => 9,
  'module' => 'backup',
// voor versie display
  'modulen' => 'tbackup',
  'versie' => ' v20150916 ',
// display fields
  'table' => '',
  'descr' => '',
  'head' => '',
  'kopregels' => '' ,	
);
$regelsArr['head'].=(isset ($MOD_GSMOFF['menu'][$regelsArr['modulen']])) ? $MOD_GSMOFF['menu'][$regelsArr['modulen']] :$regelsArr['module'];
/*
 * Lay-out strings
 */
$TEMPLATE[ 1 ]     = '	
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
$LINETEMP[ 2 ]   = '<tr %1$s><td>%2$s</td><td>%3$s</td></tr>';	
$regelsArr['kopregels'] = $LINETEMP[ 1 ] ;	
if ( $debug ) Gsm_debug( $regelsArr, __LINE__ );
if ( $debug ) Gsm_debug( $settingArr, __LINE__);
if ( $debug ) Gsm_debug( $_POST, __LINE__ );
if ( $debug ) Gsm_debug( $_GET, __LINE__ );
if ( $debug ) Gsm_debug( $place, __LINE__ );
/******************
 * Get tables
 */
$sql = "SHOW TABLE STATUS LIKE '" . CH_DBBASE . "%'";
$results = $database->query($sql);
if (!$results || $results && $results->numRows() == 0) {
	$regelsArr['head'].=$MOD_GSMOFF['TXT_ERROR_DATABASE'];
} else {
	$i = 0;
	while($row = $results->fetchRow()) {
		if ( $debug ) Gsm_debug( $row, __LINE__ );
/******************
 * Check table
 */
		$sql2 = "CHECK TABLE `" . $row[0]."`";
		$result2 = $database->query($sql2);
		$row2 = $result2->fetchRow();
		if ( $debug ) Gsm_debug( $row2, __LINE__ );
		$regelsArr['descr'].= sprintf ( $LINETEMP[ 2 ], 
			($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['2'], 
			$row[0], 
			$row2[1]." ".$row2[2]." : ".$row2[3]);
		if ($row2[3] != "OK") {
			$sql2 = "REPAIR TABLE `" . $row[0]."`";
			$result2 = $database->query($sql2);
			$row2 = $result2->fetchRow();
			$regelsArr['descr'].= sprintf ( $LINETEMP[ 2 ], 
			($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['2'], 
				"-- ".$row2[0], 
				$row2[1]." ".$row2[2]." : ".$row2[3]);
		} else {
			$regelsArr['descr'].= sprintf ( $LINETEMP[ 2 ], 
				($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['2'], 
				"", 
				"Engine : ".$row['Engine'].", Rows : ".$row['Rows']);
		}
/******************
 * Optimize table
 */
		if (!strstr($row[0], "_bck")) {
		
			if ($row['Engine']=="MyISAM") {
				$sql2 = "OPTIMIZE TABLE `" . $row[0]."`";
				if ($debug) $msg[ 'bug' ] .= __LINE__ . $sql2 . '</br>';
				$result2 = $database->query($sql2);
				$row2 = $result2->fetchRow();
				$regelsArr['descr'].= sprintf ( $LINETEMP[ 2 ], 
					($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['2'], 
					"-- ".$row2[0], 
					$row2[1].", ".$row2[2]." : ".$row2[3]);
			} elseif ($row['Engine']=="InnoDB") { 
				$sql2 = "OPTIMIZE TABLE `" . $row[0]."`";	
				$result2 = $database->query($sql2);
				$row2 = $result2->fetchRow();
				$regelsArr['descr'].= sprintf ( $LINETEMP[ 2 ], 
					($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['2'], 
					"-- ".$row2[0], 
					$row2[1].", ".$row2[2]." : ".$row2[3]);
			}
			$regelsArr['descr'].= sprintf ( $LINETEMP[ 2 ], 
				($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['2'], 
				"", 
				"To make backup with datastructure. Create Sql file using PHP admin Export");
/******************
 * Haal structuur op
 */
			$sql2 ="DESCRIBE `" . $row[0]."`";
			$result2 = $database->query( $sql2 );
			if ( $result2 || $result2->numRows() != 0 ) {
				$fieldArray = array ();
				while ( $row2 = $result2->fetchRow() ) { 
					$fieldArray[$row2['Field']]= $row2['Type']; 
				}
				unset ($fieldArray['id']);			
				unset ($fieldArray['zoek']);
				unset ($fieldArray['updated']);
				$csv_data = array ();
				$csv_out = "id";
				foreach ($fieldArray as $key => $value ) { $csv_out .= ";".$key; }
				$csv_data[] = explode(';',trim($csv_out));	
/******************
 * Haal data op
 */
				$sql2 = "SELECT * FROM `" . $row[0]."` ORDER BY `id`";			
				$result2 = $database->query( $sql2 );
				while ( $row2 = $result2->fetchRow() ) {
					$csv_out = $row2["id"];
					foreach ($fieldArray as $key  => $value ) { $csv_out .= ";".$row2[$key]; }
					$csv_data[] = explode(';',trim($csv_out));	
				}
/******************
 *
 * Sla data op
 * 
 */
				$run = date("Ymd_His");
				$filename_csv =  $place['backup'].str_replace(".", "_", $row[0])."_".$run."t.csv";
				$handle = fopen($filename_csv, 'w');
				foreach ( $csv_data as $key=> $value ) {
					fputcsv($handle, $value, ';', '"');
				}
				fclose($handle);
				$msg[ 'inf' ] .= ' csv created</br>';		
				$regelsArr['descr'].= sprintf ( $LINETEMP[ 2 ], 
					($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['2'], 
					" ", 
					"data backup created: <br>".$filename_csv);	
			}	
		}
		$i++;
	}
}
$parseViewArray = array(
	'header' => "<h1>".$regelsArr['head']."</h1>", 
  'message' 		=> message( $msg, $debug ),
	'kopregels' => $regelsArr['kopregels'], 
	'description' => $regelsArr['descr'],
	'mod'          => $regelsArr['module'],
	'sel'		=> ''
);
$prout .= $TEMPLATE[ 1 ] ;
foreach ( $parseViewArray as $key => $value ) { $prout = str_replace( "{" . $key . "}", $value, $prout ); }
if (strstr($set_mode, "vers")) {$prout .= "<small>".$regelsArr ['modulen'].$regelsArr ['versie']."</small>";}
?>