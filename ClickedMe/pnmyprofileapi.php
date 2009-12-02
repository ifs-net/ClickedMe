<?php
/**
 * @package      ClickedMe
 * @version      $Id: pnmyprofileapi.php 1 2009-10-28 09:38:39Z gm_design $
 * @author       Markus Grφίing
 * @link         http://www.opelclub.at
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * on Load function
 *
 * @return void
 */
function ClickedMe_myprofileapi_onLoad()
{
  	PageUtil::AddVar('javascript','javascript/ajax/prototype.js');
  	PageUtil::AddVar('javascript','javascript/ajax/lightbox.js');
  	PageUtil::AddVar('stylesheet','javascript/ajax/lightbox/lightbox.css');
  	PageUtil::AddVar('javascript','javascript/overlib/overlib.js');
  	PageUtil::AddVar('javascript','javascript/ajax/scriptaculous.js');
}

/**
 * This function returns 1 if Ajax should not be used loading the plugin
 *
 * @return string
 */

function ClickedMe_myprofileapi_noAjax($args)
{
  	return true;
}

/**
 * This function returns the name of the tab
 *
 * @return string
 */
function ClickedMe_myprofileapi_getTitle($args)
{
    $uid = FormUtil::getPassedValue('uid');
    $ownid = pnUserGetVar('uid');
	
	/* if loggedin User is the Same, as the viewed one, show ClickedMe Tab */
	
	if ( $uid == $ownid) {
		pnModLangLoad('ClickedMe','myprofile');
		return _CLICKEDMYTABTITLE;
	} else {
		return "";
	}
}

/**
 * This function returns additional options that should be added to the plugin url
 *
 * @return string
 */
function ClickedMe_myprofileapi_getURLAddOn($args)
{
    return '';
}

/**
 * This function shows the content of the main MyProfile tab
 *
 * @return output
 */
function ClickedMe_myprofileapi_tab($args)
{
    $uid = FormUtil::getPassedValue('uid');
	$ownid = pnUserGetVar('uid');
	
	$output = pnModFunc('ClickedMe','user','history');
	return $output;
}
