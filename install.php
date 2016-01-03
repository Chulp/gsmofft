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

// function to remove old directories
function xrmdir( $dir )	{
	if ( is_dir( $dir ) )  {
 		$files = scandir( $dir );
  			foreach ( $files as $file ) { if ( $file != "." && $file != ".." ) xrmdir( "$dir/$file" ); }
    		rmdir( $dir );
 	} else if ( file_exists( $dir ) ) {
    	unlink( $dir );
}	}
// functions to copy files and non-empty directories  
function xcopy( $src, $dst ) {
// 	if ( file_exists( $dst ) ) { xrmdir( $dst ); }
 	if ( is_dir( $src ) )  {
   		mkdir( $dst, 0777);
    	$files = scandir( $src );
    	foreach ( $files as $file ) { if ( $file != "." && $file != ".." ) xcopy( "$src/$file", "$dst/$file" ); }
  	} else if ( file_exists( $src ) ) { copy( $src, $dst );}	
}

$hulp = explode("\\", str_replace (WB_PATH,"",dirname(__FILE__)));
if (isset($hulp[2])){  // stand alone
	define('CH_MODULE', substr($hulp[2], 0, -1));
	define('CH_SUFFIX', substr($hulp[2], -1));
} else { // on-line
	$hulp = explode("/", str_replace (WB_PATH,"",dirname(__FILE__)));
	define('CH_MODULE', substr($hulp[2], 0, -1));
	define('CH_SUFFIX', substr($hulp[2], -1));
}
define('CH_DBBASE', TABLE_PREFIX.'mod_go');

$place_in_delivery = WB_PATH . '/modules/' . CH_MODULE . 't/' . CH_MODULE;
// to Media directories
$place_intended    = WB_PATH . MEDIA_DIRECTORY . '/' . CH_MODULE;
xcopy($place_in_delivery, $place_intended );

$jobs = array();

$jobs[] = "DROP TABLE IF EXISTS `".CH_DBBASE."`";
$jobs[] = "CREATE TABLE `".CH_DBBASE."` (
	`id`		int(11) NOT NULL AUTO_INCREMENT,
	`section` 	int(11)  NOT NULL ,
	`table`		varchar(255) NOT NULL ,
	`name`		varchar(255) NOT NULL ,
	`value`		varchar(255) NOT NULL ,
	PRIMARY KEY (`id`)
)";
$jobs[] = "INSERT INTO `".CH_DBBASE."` (`section`, `table`, `name`, `value`) VALUES( '0', '','debug', 'no')";
$jobs[] = "INSERT INTO `".CH_DBBASE."` (`section`, `table`, `name`, `value`) VALUES( '0', '','company', 'Noname Ltd.')";
$jobs[] = "INSERT INTO `".CH_DBBASE."` (`section`, `table`, `name`, `value`) VALUES( '0', '','mode', 'file expl user apps')";
$jobs[] = "INSERT INTO `".CH_DBBASE."` (`section`, `table`, `name`, `value`) VALUES( '0', '','menu', 'dummy|settings|backup|reload|develop')";

$errors = array();

foreach($jobs as $query) {
	$database->query( $query );
	if ( $database->is_error() ) $errors[] = $database->get_error();
}
/** 
 *	Any errors to display?
 *
 */
if (count($errors) > 0) $admin->print_error( implode("<br />\n", $errors), 'javascript: history.go(-1);');
?>