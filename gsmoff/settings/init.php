<?php
/*
 *  @module         Office toolset
 *  @version        see info.php of this module
 *  @author         Gerard Smelt
 *  @copyright      2010-2016, Gerard Smelt
 *  @license        see info.php of this module
 *  @platform       see info.php of this module
 * this module is providing a number of application settings / file location
 * the intention is that this file is copied to the media directory
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
 * functions 
 *
 * start with functions which are needed before includes are loaded
 */
// none
/*
 * fixed values and settings
 */
define( 'CH_DBBASE', TABLE_PREFIX . 'mod_go' );
// $ref_table = CH_DBBASE; // legacy
define( 'CH_PATH', dirname( __FILE__ ) );
date_default_timezone_set( 'Europe/Paris' );
define( "CH_CR", "\n" );
define( 'CH_LV', '../' );
define( 'NL', '</br>' );
/*
 * return path 
 */
$hulp = str_replace( WB_PATH, "", $_SERVER[ 'SCRIPT_NAME' ] );
if ( !isset( $section_id ) ) {
  define( "CH_LOC", "tools" );
  define( "CH_RETURN", "tool.php?tool=gsmofft&module={mod}&selection={sel}" );
} //!isset( $section_id )
elseif ( strstr( $hulp, "/admins" ) ) {
  //} elseif (substr("/admins",0, 7) == substr($hulp,0, 7)) { 
  define( "CH_LOC", "back" );
  define( "CH_RETURN", "modify.php?section_id=" . $section_id . "&page_id=" . $page_id );
} //strstr( $hulp, "/admins" )
else {
  define( "CH_LOC", "front" );
  define( "CH_RETURN", substr( htmlspecialchars( strip_tags( $_SERVER[ 'SCRIPT_NAME' ] ) ) . "?section_id=" . $section_id, 0 ) );
}
$page_id    = ( isset( $page_id ) ) ? $page_id : 0;
$section_id = ( isset( $section_id ) ) ? $section_id : 0;
define( "CH_LOGIN", WB_URL . PAGES_DIRECTORY . "/mydata.php?section_id=" . $section_id );
define( "CH_ROOT", str_replace( "park", "", WB_URL ) );
/*
 * directories
 */
global $place;
$place         = array(
  'upload' => WB_PATH . MEDIA_DIRECTORY . '/' . CH_MODULE . '/uploads/',
//  'upload1' => WB_PATH . MEDIA_DIRECTORY, 
  'upload2' => CH_LV . CH_LV . 'media/' . CH_MODULE . '/pdf/',
  'pdf' => WB_PATH . MEDIA_DIRECTORY . '/' . CH_MODULE . '/pdf/',
  'pdf1' => CH_LV . 'media/' . CH_MODULE . '/pdf/',
  'pdf2' => CH_LV . CH_LV . 'media/' . CH_MODULE . '/pdf/',
  'pdf3' => CH_LV . CH_LV,
  'img' => WB_URL . '/modules/' . CH_MODULE . CH_SUFFIX.'/img/',
  'imgm' => WB_URL . '/modules/' . CH_MODULE . CH_SUFFIX . '/img/',
  'backup' => WB_PATH . MEDIA_DIRECTORY . '/' . CH_MODULE . '/backup/',
  'backup2' => CH_LV . CH_LV . 'media/' . CH_MODULE . '/backup/',
  'document' => WB_PATH . MEDIA_DIRECTORY . '/' . CH_MODULE . '/documents/',
  'document1' => CH_LV . 'media/' . CH_MODULE . '/documents/' 
);
?>