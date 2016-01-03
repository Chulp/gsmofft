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
$module_description = 'Basic Office Tools functionality (EN).';

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

$MOD_GSMOFF = array(
  'add'  => 'Nieuw',
  'added' => 'Toegevoegd ',
  'addn'  => 'Toevoegen(nieuw)',
  'addv'  => 'Veld Toevoegen',
  'autocomplete' => array (
    '1' => 'Text',
    '2' => 'Value',
    '3' => 'Amount',
    '4' => 'E-mail',
    '5' => 'Text',
    '6' => 'yyyy-mm-dd',
    '7' => 'Value 1/0'),
  'cancel'  => 'Afbreken',
  'change'  => 'Wijzigen',
  'confirm'  => 'Uitgevoerd',
  'date_format'  => 'Y-m-j | H:i:s',
  'del'  => 'Verwijderen',
  'edit'  => 'Aanpassen',
  'error0' => ' Oeps unexpected case : ', //replaced 'TXT_ERROR_DATABASE' 
  'error1' => 'error1DEPR Rekening ?',
  'error2' => ' Oeps missing data : ',  
  'error4' => ' Oeps sips case',
  'expl' => array (
    '<u>Function parameters</u>',
    'user : displays user data',
    'file : displays file data',
    'apps : displays application data',
    'expl or debug=yes : displays this explenation data',
    'vers : display version of the functions'),
  'file_type'  => array(
    'varchar(255)' => '1',
    'int(11)' => '2',
    'decimal(9,2)' => '3',
    'varchar(63)' => '4', 
    'varchar(127)' => '5',
    'date' => '6',
    'int(7)' => '7'),
  'friendly' => array('&lt;', '&gt;', '?php'),
  'go'  => 'Select',
  'line_color' => array( 
    '0' => '',
    '1' => 'bgcolor="#eeeeee"',
    '2' => 'bgcolor="#dddddd"',
    '3' => 'bgcolor="#cccccc"',
    '4' => 'bgcolor="#bbbbbb"' ),
  'menu'  => array(  
    'tbackup'=> 'Backup service',
    'tdevelop'=> 'Reserved',
    'tdummy'=> '------',
    'treload'=> 'Reload service',
    'tsettings'=> 'Onderhoud Settings', 
    'tafgesloten'=> 'Jaarafsluiting', 
    'trekening'=> 'Rekening Schema'),
  'module' => 'module :',
  'no'  => 'no',
  'nodata'  => 'Geen informatie',
  'raw' => array('<', '>', ''),
  'save'  => 'Opslaan',
  'tbl_aantal' => 'Aantal Records : ', 
  'tbl_volgorde' => 'Volgorde : ',
   'tbl_icon' => array( 
    0=>'Select', 
    1=>'Wijzigen', 
    2=>'Terug', 
    3=>'Toevoegen', 
    4=>'Opslaan', 
    5=>'Opslaan (als nieuw)', 
    6=>'Verwijderen', 
    7=>'Bereken',
    8=>'Controle', 
    9=>'Select', 
    10=>'+', 
    11=>'Print Model', 
    12=>'ok',  
    13=>'reserve',  
    14=>'reserve',  
    15=>'reserve',  
    16=>'reserve'
    ),
  'tbl_icon2' => array( 
    '0' => 'cancel',
    '1' => 'add',
    '2' => 'advanced',
    '3' => 'back',
    '4' => 'backup',
    '5' => 'groups',
    '6' => 'help',
    '7' => 'infobtn',
    '8' => 'languages',
    '9' => 'modify',
    '10' => 'modules',
    '11' => 'newfolder',
    '12' => 'reload',
    '13' => 'sections',
    '14' => 'search',
    '15' => 'settings',
    '16' => 'templates',
    '17' => 'upload',
    '18' => 'users',
    '19' => 'warn',
    '20' => 'submit',
    ), 
  'rek_type' => array(
	  '1' => 'Activa',
	  '2' => 'Passiva',
	  '4' => 'Uitgaven',
	  '5' => 'Inkomsten',
	  '7' => 'Tussen rekening'),
  'rek_type_sign'=> array(
    '1' => 1,
    '2' => -1,
    '4' => 1,
    '5' => -1,
    '7' => 1 ),
  'grootboek' => array(
	  '1' => 'Kl 1: Eigen vermogen en langlopende schulden',
	  '2' => 'Kl 2: Vaste activa en langlopende vorderingen ',
	  '3' => 'Kl 3: Voorraden en bestellingen ',
	  '4' => 'Kl 4: Kortlopende schulden en vorderingen ',
	  '5' => 'Kl 5: Liquide middelen en opvraagbare beleggingen',
	  '6' => 'Kl 6: Kosten',
	  '7' => 'Kl 7: Opbrengsten',
	  '8' => 'Kl 8: Tussen rekeningen',
	  '9' => 'kl 0: Niet in de balans opgenomen rechten en verplichtingen' ),
  'tbl_overzicht' => 'Overzicht gemaakt : ', 
  'tbl_label' => 'veld', 
  'tbl_selectie' => 'Selectie : ',
  'tbl_value' => 'inhoud', 
  'tbl_next' => 'resultaten %s-%s van %s ',  
  'TH_ADD'  =>'Toevoegingen',
  'TH_APP'  => 'Module',
  'TH_DAYS_INACTIVE'  => 'In days',
  'TH_DESCRIPTION'  => 'Title',  
  'TH_EMAIL'  => 'Email',
  'TH_FILE'  => 'Table',
  'TH_LAST_IP'  => 'Last IP',
  'TH_LAST_LOGIN'  => 'Last login',
  'TH_NAME'  =>'Name',
  'TH_PARAMETER'  => 'Parameter',
  'TH_SECTION'  => 'section',
  'TH_USER'  =>'Gebruker',
  'TH_VALUE'  => 'Value',
  'TXT_DESCRIPTION_APPS'  => 'The table provides a list of applications active in this application.',  
  'TXT_DESCRIPTION_FILES'  => 'The table provides a list of files in this application.',
  'TXT_DESCRIPTION_SETS'  => 'The table provides a list of settings for this application.',  
  'TXT_DESCRIPTION_USERS'  => 'The table provides a list of registered users, and the date of their last login.',
  'TXT_ERROR_DATABASE'  => 'Oeps Unexpected case',
  'TXT_HEADING_APPS'  => 'Application Information',
  'TXT_HEADING_FILES'  => 'File Information',
  'TXT_HEADING_SETS'  => 'Setting Information',  
  'TXT_HEADING_USERS'  => 'User Statistics',
  'yes'=> 'yes');
$TEMPLATE[ 1 ] = '
  <h2>{header}</h2>
    {message}
  <div class="container">
  <table class="inhoud" width="100%">
    {kopregels}
    {description}
  </table>
  </div>
  <div class="container">
  <form name="view" method="post" action="{return}">
  <input type="hidden" name="sh" value="{hash}" />
  <input type="hidden" name="page_id" value="{page_id}" />
  <input type="hidden" name="section_id" value="{section_id}" />
  <input type="hidden" name="update_verif" value="{update}" />
  <input type="hidden" name="recid" value="{recid}" />
  <table class="footer" width="100%">
    {selection}
  </table>
  </form>
  </div>';
$LINETEMP[ 1 ] = '<colgroup><col width="20%"><col width="20%"><col width="20%"><col width="20%"><col width="20%"></colgroup>'.CH_CR;
$LINETEMP[ 2 ] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td><td>%6$s</td></tr>'.CH_CR;
$LINETEMP[ 3 ] = '<tr %1$s><td><a href="' . CH_RETURN . '&command=view&recid=%2$s">%3$s</a></td><td>%4$s</td><td>%5$s</td><td>%6$s</td><td>%7$s</td></tr>'.CH_CR;
$LINETEMP[ 4 ] = '<tr %1$s>%2$s %3$s<td>%4$s</td></tr>'.CH_CR;
$LINETEMP[ 5 ] = '<tr %1$s><td>%2$s %3$s<td>%4$s</td></tr>'.CH_CR;
$LINETEMP[ 6 ] = '<tr><td colspan="2">%1$s</td><td colspan="3">%2$s</td></tr>'.CH_CR;
$LINETEMP[ 7 ] = '<tr %1$s><td colspan="%2$s">%4$s</td><td colspan="%3$s">%5$s</td></tr>'.CH_CR;
$LINETEMP[11] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td><td>%6$s</td><td>%7$s</td></tr>'.CH_CR;
$LINETEMP[12] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td align="right">%5$s</td><td align="right">%6$s</td><td align="right">%7$s</td></tr>'.CH_CR;

$ICONTEMP[ 1 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][9].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][1].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 2 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][3].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][2].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 3 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][1].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][3].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 4 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][20].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][4].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 5 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][19].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][5].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 6 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][9].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][6].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 7 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][9].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][7].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 8 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][2].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][8].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 9 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][15].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][9].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 10 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][18].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][10].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 11 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][18].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][11].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 12 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][19].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][12].'" style="width: 100%;" />'.CH_CR;
//$ICONTEMP[ 13 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][19].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][13].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 14 ]  = '<input maxlength="12" size="12" type="text" name="select_van" value="%1$s" width="30" />%2$s'.CH_CR;
$ICONTEMP[ 15 ]  = '<input maxlength="12" size="12" type="text" name="select_tot" value="%1$s" width="30" />'.CH_CR;
//$ICONTEMP[ 16 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][19].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][14].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 17 ] = '<a target="_blank" href="' . $place['pdf1'] . '%2$s"><img src="' . $place['imgm'] . 'pdf_16.png" alt="pdf document">%1$s</a>'.CH_CR;
$ICONTEMP[ 18 ] = '<a target="_blank" href="' . $place['pdf2'] . '%2$s"><img src="' . $place['imgm'] . 'pdf_16.png" alt="pdf document">%1$s</a>'.CH_CR;
$ICONTEMP[ 19 ]  = '';
$ICONTEMP[ 20 ]  = '&nbsp;<input type="checkbox" name="confirm" value="yes" />'.CH_CR;
?>