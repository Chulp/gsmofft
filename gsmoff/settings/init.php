<?php
/*
 *  @module         Office toolset
 *  @version        see info.php of this module
 *  @author         Gerard Smelt
 *  @copyright      2015, Gerard Smelt
 *  @license        see info.php of this module
 *  @platform       see info.php of this module
 * this module is providing a number of application settings / file location
 * the intention is that these files are copied to the media directory
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
/*
// location of the upload files
// $place_upload = WB_PATH . MEDIA_DIRECTORY . '/'.CH_MODULE.'/uploads/';  
// location of the backup
$place_backup  = WB_PATH . MEDIA_DIRECTORY . '/' . CH_MODULE . '/backup/';
$place_backupe = CH_LV . CH_LV . 'media/' . CH_MODULE . '/backup';
// location of the image directory
// afhankelijk van de server een van de twee volgende regels is van toepassing
//$place_img = (dirname( __FILE__ )).'/img/'; 
$place_img     = WB_URL . '/modules/' . CH_MODULE . 't/img/';
$place_imge    = WB_URL . '/modules/' . CH_MODULE . 't/img/';
// location of the pdf files
$place_upload  = WB_PATH . MEDIA_DIRECTORY . '/' . CH_MODULE . '/pdf/';
$place_uploade = CH_LV . CH_LV . 'media/' . CH_MODULE . '/pdf/';
// location of the image directory
// afhankelijk van de server een van de twee volgende regels is van toepassing
global $place_imgx;
$place_imgx = ( dirname( __FILE__ ) ) . '/img/';
global $place_imgm;
$place_imgm      = WB_URL . '/modules/' . CH_MODULE . CH_SUFFIX . '/img/';
// userfile directory backup
$place_documents = WB_PATH . MEDIA_DIRECTORY . '/' . CH_MODULE . '/documents';
//$place_documente = CH_LV.CH_LV.'media/'.CH_MODULE.'/documents'; 
$place_documente = CH_LV . 'media/' . CH_MODULE . '/documents';
/******************
 *
 * debug
 *
 
 if ($debug) {
 echo NL.__LINE__.'BASE=>'.CH_DBBASE;
 echo NL.__LINE__.'PATH=>'.CH_PATH;
 echo NL.__LINE__.'LOC=>'.CH_LOC;
 echo NL.__LINE__.'=>'.'CH_CR';
 echo NL.__LINE__.'=>'.'CH_LV';
 echo NL.__LINE__.'=>'.'NL';
 echo NL.__LINE__.'RETURN=>'.CH_RETURN;
 echo NL.__LINE__.'=>'.$page_id;
 echo NL.__LINE__.'=>'.$section_id;
 echo NL.__LINE__.'LOGIN=>'.CH_LOGIN;
 echo NL.__LINE__.'ROOT=>'.CH_ROOT;
 echo NL.__LINE__.'=>'.$place_upload;
 echo NL.__LINE__.'=>'.$place_backup;
 echo NL.__LINE__.'=>'.$place_backupe;
 echo NL.__LINE__.'=>'.$place_img;
 echo NL.__LINE__.'=>'.$place_imge;
 echo NL.__LINE__.'=>'.$place_imgm;
 echo NL.__LINE__.'=>'.$place_imgx;
 echo NL.__LINE__.'=>'.$place_upload;
 echo NL.__LINE__.'=>'.$place_uploade;
 echo NL.__LINE__.'=>'.$place_documents;
 echo NL.__LINE__.'=>'.$place_documente.NL;
 foreach ($place as $key => $value) { echo NL.__LINE__.' '.$key.'=>'.$value;}
 echo NL;
 }
 /******************
 * end debug
 */
?>