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
/*
 * initial settings 
 */
$hulp = explode("\\", str_replace (WB_PATH,"",dirname(__FILE__)));
if (isset($hulp[2])){  // stand alone
	define('CH_MODULE', substr($hulp[2], 0, -1));
	define('CH_SUFFIX', substr($hulp[2], -1));
} else { // on-line
	$hulp = explode("/", str_replace (WB_PATH,"",dirname(__FILE__)));
	define('CH_MODULE', substr($hulp[2], 0, -1));
	define('CH_SUFFIX', substr($hulp[2], -1));
}
//location of the settings data
include (WB_PATH . MEDIA_DIRECTORY . '/'.CH_MODULE.'/settings/init.php');
/******************
 * variable setting
 */
$toolArr = array(
// voor versie display
  'version' => ' v20150916 '
);
/*
 * application settings load 
 */
$query = "SELECT * FROM `" . CH_DBBASE ."` WHERE `section`='0' AND `table`='' ORDER BY `id`";
$message = __LINE__." EN : Oeps unexpected case" . $query . "</br>";
$results = $database->query( $query ); 
if ( !$results || $results->numRows() == 0 ) die( $message );
$settingArr = array ();
while ( $row = $results->fetchRow() ) { 
  if (strlen($row['table'])<1) $settingArr[$row['name']]= $row['value']; 
}
$debug=false;
if (isset($settingArr['debug']) && $settingArr['debug']=="yes") $debug=true;
/*
 * Location of files
 */
//places of the includes
$place_incl = (dirname( __FILE__ )).'/';
//places of the language file 
$place_lang  = ( dirname( __FILE__ ) ) . '/languages/' . LANGUAGE . '.php'; 
// load module language file
require_once(!file_exists($place_lang) ? (dirname(__FILE__)) . '/languages/EN.php' : $place_lang );
// load includes
require_once($place_incl.'includes.php' );

$set_mode = (isset($settingArr['mode'])) ? $settingArr['mode'] : "file";
if (isset($settingArr['function'])) { 
	$hulp= explode ("|", $settingArr['function']); 
	foreach ($hulp as $key => $value) { $set_menu['t'.$value]= strtolower($value); }
} elseif (isset($settingArr['menu'])) {   // depricated function only for compatibility with older versions
	$hulp= explode ("|", $settingArr['menu']); 
	foreach ($hulp as $key => $value) { $set_menu['t'.$value]= strtolower($value); }
} else { 
	$set_menu['tdummy']=" none"; 
}
foreach ($set_menu as $key => $value) { if (isset ($MOD_GSMOFF['menu'][$key])) { $set_menu[$key]= $MOD_GSMOFF['menu'][$key]; } }
/*
 * debug
 */
if ( $debug ) {
	echo Gsm_post( 1 );
	echo Gsm_post( 2 );
	echo Gsm_post( 4 );
    Gsm_debug($settingArr, __LINE__, 2);
    Gsm_debug($set_menu, __LINE__, 2);
}	
/*
 * Layout template
 */
$TEMPLATE[ 0 ]   = '
	<div class="container">
	<form name="menu" method="post" action="{return}">
	<table>
		<colgroup><col width="15%"><col width="25%"><col width="20%"><col width="20%"><col width="20%"></colgroup>
		<tr><td>' . $MOD_GSMOFF[ 'module' ] . '</td>
		<td><SELECT name="module" >{module}</SELECT></td>
		<td><input type="text" name="selection" value="{parameter}" placeholder="Parameter" /></td>
		<td><input class="modules" type="submit" value="' . $MOD_GSMOFF[ 'go' ] . '" /></td>
		<td>{add_needed}</td></tr>
	</table>
	</form>
	</div>';
$TEMPLATE[ 3 ]    = '	
	<div class="container">
		{header}
	<table class="inhoud" width="100%">
		{kopregels}
		{description}
	</table>
	</div>';
/*
 * which function include to load by the scheduler
 * and which parameters
 */
$prout="";
$print="";
$tmodule='';
$tselection='';
$tsetting=false;
/*
 * Get menu input if any
 */
if ( isset( $_GET[ 'module' ] ) ) $tmodule = strtolower( $_GET[ 'module' ] );
if ( isset( $_POST[ 'module' ] ) ) $tmodule = strtolower( $_POST[ 'module' ] );
if (substr($tmodule, 0, 1)!="t")  $tmodule="t".$tmodule;
if ( isset( $_GET[ 'selection' ] ) ) $tselection = strtolower( $_GET[ 'selection' ]);
if ( isset( $_POST[ 'selection' ] ) ) $tselection = strtolower( $_POST[ 'selection' ] );
if ( isset( $_POST[ 'setting' ] ) ) $tsetting=true;
$parseViewArray = array(
	'return' => CH_RETURN, 
	'parameter' => $tselection,
	'module' => Gsm_option( $set_menu, $tmodule ),
	'add_needed' => '',
	'mod' => $tmodule,
	'sel' => $tselection
);
$print = $TEMPLATE[ 0 ];
foreach ( $parseViewArray as $key => $value ) {
	$print = str_replace( "{" . $key . "}", $value, $print );
}
if ( strlen ($tmodule)>3 && $tmodule!='tdummy') {
	if ( $debug ) {
		$msg[ 'bug' ] .= __LINE__.' => ' . $tmodule . '.php<br/>';
		if ($tselection)  $msg[ 'bug' ] .= __LINE__. ' selection: '.$tselection.'<br/>';
		if ($tsetting)  $msg[ 'bug' ] .= __LINE__. '   settings<br/>'; 
	}
	unset ($query);
	require_once($place_incl.$tmodule.'.php' );
} else {
	if (strstr($set_mode, "user")) {
/*
 * Users
 */
		$TEMPLATEL[ 1 ] = '
			<thead>
			<tr '.$MOD_GSMOFF['line_color']['4'] .' >
				<th width="15%">'.$MOD_GSMOFF['TH_USER'].'</th>
				<th width="25%">'.$MOD_GSMOFF['TH_LAST_LOGIN'].'</th>
				<th width="10%">'.$MOD_GSMOFF['TH_DAYS_INACTIVE'].'</th>
				<th width="20%">'.$MOD_GSMOFF['TH_LAST_IP'].'</th>
				<th width="30%">'.$MOD_GSMOFF['TH_EMAIL'].'</th>
			</tr>
			</thead>';
		$TEMPLATEL[ 2 ]   = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td><td>%6$s</td></tr>';	
		$descr_kop="";
		$descr_kopregels='';
		$descr_description='';
		$descr_kop="";	
		$descr_kop.='<h2>'.$MOD_GSMOFF['TXT_HEADING_USERS'].'</h2>';
		$descr_kop.=$MOD_GSMOFF['TXT_DESCRIPTION_USERS'];
		$descr_kopregels.=$TEMPLATEL[ 1 ];
		// access database and obtain users table
		$sql = "SELECT * FROM `" . TABLE_PREFIX . "users`";
		$results = $database->query($sql);
		if (!$results || $results && $results->numRows() == 0) {
			$descr_kop.=$MOD_GSMOFF['TXT_ERROR_DATABASE'];
		} else {
			// loop over all users and add one row per user to the template
			$i = 0;
			while($row = $results->fetchRow()) {
				$descr_description.= sprintf ( $TEMPLATEL[ 2 ], // '', 'aa', 'aa', 'aa', 'aa', 'aa', 'aa');
					($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['1'], 
					htmlentities($row['username']), 
					date((($row['login_when'] == 0) ? '-' : $MOD_GSMOFF['date_format']), (int) $row['login_when']),
					($row['login_when'] == 0) ? '-' : round((time() - (int) $row['login_when']) / (3600 * 24)),
					htmlentities($row['login_ip']),
					htmlentities($row['email']));
					$i++;
			}
		}
		$parseViewArray = array(
			'header' => $descr_kop, 
			'kopregels' => $descr_kopregels, 
			'description' => $descr_description
		);
		$prout .= $TEMPLATE[ 3 ];
		foreach ( $parseViewArray as $key => $value ) {
		$prout = str_replace( "{" . $key . "}", $value, $prout );
		}
	}
	if (strstr($set_mode, "file")) {
/*
 * Files
 */
		$TEMPLATEL[ 3 ] = '
			<thead>
			<tr '.$MOD_GSMOFF['line_color']['4'] .' >
				<th width="25%">'.$MOD_GSMOFF['TH_FILE'].'</th>
				<th width="75%">&nbsp;</th>
			</tr>
			</thead>';
		$TEMPLATEL[ 4 ]   = '<tr %1$s><td>%2$s</td><td>%3$s</td></tr>';	
		$descr_kop="";
		$descr_kopregels='';
		$descr_description='';
		$descr_kop="";	
		$descr_kop.='<h2>'.$MOD_GSMOFF['TXT_HEADING_FILES'].'</h2>';
		$descr_kop.=$MOD_GSMOFF['TXT_DESCRIPTION_FILES'];
		$descr_kopregels.=$TEMPLATEL[ 3 ];
		// access database and obtain users table
		$sql = "SHOW TABLES LIKE '" . CH_DBBASE . "%'";
		$results = $database->query($sql);
		if (!$results || $results && $results->numRows() == 0) {
			$descr_kop.=$MOD_GSMOFF['TXT_ERROR_DATABASE'];
		} else {
			$i = 0;
			while($row = $results->fetchRow()) {
				$sql2 = "CHECK TABLE `" . $row[0]."`";
				$result2 = $database->query($sql2);
				$row2 = $result2->fetchRow();
				$descr_description.= sprintf ( $TEMPLATEL[ 4 ], 
					($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['2'], 
					$row2[0], 
					$row2[1]." ".$row2[2]." : ".$row2[3]);
					$i++;
			}
		}
		$parseViewArray = array(
			'header' => $descr_kop, 
			'kopregels' => $descr_kopregels, 
			'description' => $descr_description
		);
		$prout .= $TEMPLATE[ 3 ];
		foreach ( $parseViewArray as $key => $value ) {
			$prout = str_replace( "{" . $key . "}", $value, $prout );
		}
	}
	if (strstr($set_mode, "apps")) {
/*
 * applications
 */
		$TEMPLATEL[ 5 ] = '
			<thead>
			<tr '.$MOD_GSMOFF['line_color']['4'] .' >
				<th width="25%">'.$MOD_GSMOFF['TH_APP'].'</th>
				<th width="70%">'.$MOD_GSMOFF['TH_DESCRIPTION'].'</th>
				<th width="5%">&nbsp;</th>
			</tr>
			</thead>';
		$TEMPLATEL[ 6 ]   = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td></tr>';	
		$descr_kop="";
		$descr_kopregels='';
		$descr_description='';
		$descr_kop="";	
		$descr_kop.='<h2>'.$MOD_GSMOFF['TXT_HEADING_APPS'].'</h2>';
		$descr_kop.=$MOD_GSMOFF['TXT_DESCRIPTION_APPS'];
				$descr_kopregels.=$TEMPLATEL[ 5 ];
		$i=0;
		foreach ($set_menu as $key=>$value ) {
		$descr_description.= sprintf ( $TEMPLATEL[ 6 ], 
			($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['2'], 
			$key, 
			$value,
			'',
			'');
			$i++;
		}
		$parseViewArray = array(
			'header' => $descr_kop, 
			'kopregels' => $descr_kopregels, 
			'description' => $descr_description
		);
		$prout .= $TEMPLATE[ 3 ];
		foreach ( $parseViewArray as $key => $value ) {
			$prout = str_replace( "{" . $key . "}", $value, $prout );
		}
	}
/*
 * Setting information
 */
	$TEMPLATEL[ 7 ] = '
		<thead>
		<tr '.$MOD_GSMOFF['line_color']['4'] .' >
			<th width="25%">'.$MOD_GSMOFF['TH_PARAMETER'].'</th>
			<th width="70%">'.$MOD_GSMOFF['TH_VALUE'].'</th>
			<th width="5%">&nbsp;</th>
		</tr>
		</thead>';
	$TEMPLATEL[ 8 ]   = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td></tr>';	
	$descr_kop="";
	$descr_kopregels='';
	$descr_description='';
	$descr_kop="";	
	$descr_kop.='<h2>'.$MOD_GSMOFF['TXT_HEADING_SETS'].'</h2>';
	$descr_kop.=$MOD_GSMOFF['TXT_DESCRIPTION_SETS'];
	$descr_kopregels.=$TEMPLATEL[ 7 ];
	$i = 0;
	$descr_description.= sprintf ( $TEMPLATEL[ 8 ], 
		($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['3'], 
		'DEBUG', 
		($debug == 0) ? '<b>'.$MOD_GSMOFF['no'].'</b>' : '<b>'.$MOD_GSMOFF['yes'].'</b>',
		'');
	$i ++;
	$descr_description.= sprintf ( $TEMPLATEL[ 8 ], 
		($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['2'], 
		'MODE', 
		'<b>'.$set_mode.'</b>',
		'');
	if (strstr($set_mode, "expl") || $debug ) {		
		foreach ( $MOD_GSMOFF [ 'expl' ] as $key => $value) { $i++;
			$descr_description.= sprintf ( $TEMPLATEL[ 8 ],	
			($i % 2 == 0) ? '' : $MOD_GSMOFF['line_color']['2'], 
			'', 
			$value, 
			'');
		}
	}	
	$parseViewArray = array(
		'header' => $descr_kop, 
		'kopregels' => $descr_kopregels, 
		'description' => $descr_description
	);
	$prout .= $TEMPLATE[ 3 ];
	foreach ( $parseViewArray as $key => $value ) {
		$prout = str_replace( "{" . $key . "}", $value, $prout );
	}
}
/*
 * output the screen 
 */
echo $print;
if (strstr($set_mode, "vers")) {$prout .= "<small>".$toolArr ['version']."</small>";}
echo $prout;
?>