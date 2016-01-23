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
// end include class.secure.php
$module_directory = 'gsmofft';
$module_name = 'Office Tools';
$module_function = 'tool';
$module_version = '2.2.0';
$module_platform = '2.0.0';
$module_author = 'Gerard Smelt';
$module_license = 'All rights reserved';
$module_license_terms = 'All rights reserved';
$module_guid = '57122E77-9EFA-4271-B143-A59A00A6857C';
$module_description = 'This module provides basic functionality for the gsm office application.';
$module_home = 'http://www.contracthulp.nl';

/* guid via UUID-GUID Generator Portable 1.1. */
?>