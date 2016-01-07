<?php
/*
 *  @template       Office toolset
 *  @version        see info.php of this template
 *  @author         Gerard Smelt
 *  @copyright      2010-2014 Contracthulp B.V.
 *  @license        see info.php of this module
 *  @platform       see info.php of this module
 *
 * version 1 of the language module
 */
// include class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {  
  include(WB_PATH.'/framework/class.secure.php'); 
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php')) {
  include($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php'); 
} else {
  $subs = explode('/', dirname($_SERVER['SCRIPT_NAME']));  $dir = $_SERVER['DOCUMENT_ROOT'];
  $inc = false;
  foreach ($subs as $sub) {
    if (empty($sub)) continue; $dir .= '/'.$sub;
    if (file_exists($dir.'/framework/class.secure.php')) { 
  include($dir.'/framework/class.secure.php'); $inc = true;  break; 
    } 
  }
  if (!$inc) trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
}
// end include class.secure.php

// module description
$module_description = 'Basic Office Tools functionality (NL).';

// declare module language array

if (!defined('CH_RETURN')) {
  define( "CH_RETURN", "tool.php?tool=gsmofft&module={mod}&selection={sel}" );
  define( "CH_CR", "\n" );
  define( 'CH_LV', '../' );
  define( 'CH_MODULE', '' );
  define( 'CH_SUFFIX', '' );
  $place = array(
    'pdf1' => CH_LV . 'media/' . CH_MODULE . '/pdf/',
    'pdf2' => CH_LV . CH_LV . 'media/' . CH_MODULE . '/pdf/',
    'imgm' => WB_URL . '/modules/' . CH_MODULE . CH_SUFFIX . '/img/');
}
$MOD_GSMOFF [ 'add' ] = 'Nieuw';
?>