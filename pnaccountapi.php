<?php
/**
* @package      ClickedMe
* @version      $Id$
* @author       Florian Schießl
* @link         http://www.ifs-net.de
* @copyright    Copyright (C) 2008
* @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
*/

/**
 * Return an array of items that should be shown on the user's settings pannel
 *
 * @param	uname	string
 * @return	array
 */
function ClickedMe_accountapi_getall($args)
{
    $items = null;

    // only show the options to logged in users of course!
    if (!pnUserLoggedIn()) return $items;

    $dom = ZLanguage::getModuleDomain('ClickedMe');
    
    if (SecurityUtil::checkPermission('ClickedMe::','::', ACCESS_COMMENT)) {
	$items = array(array(
			'url'	=> pnModURL('ClickedMe','user','main'),
			'title'	=> __('Privacy Settings',$dom),
			'icon'	=> 'icon_clickedme.gif'		));
    }
    
    return $items;
}