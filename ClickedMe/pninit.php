<?php
/**
* @package      ClickedMe
* @version      $Id$
* @author       Florian Schie�l
* @link         http://www.ifs-net.de
* @copyright    Copyright (C) 2008
* @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
*/

/**
 * initialise the ClickedMe module
 *
 * @return       bool       true on success, false otherwise
 */
function ClickedMe_init()
{
    // Create the tables
    if (!DBUtil::createtable('clickedme_settings')) return false;
    if (!DBUtil::createtable('clickedme')) return false;

    // Initialisation successful
    return true;
}

/**
 * upgrade the module
 *
 * @return       bool       true on success, false otherwise
*/
function ClickedMe_upgrade($oldversion)
{
    switch($oldversion) {
        case '0.20':
	    // we need to add the settings table
	    if (!DBUTIL::createtable('clickedme_settings')) return false;
	default:
	    break;
    }
    return true;
}
		
/**
 * delete the ClickedMe module
 *
 * @return       bool       true on success, false otherwise
 */
function ClickedMe_delete()
{
    // Drop the tables
    if (!DBUtil::dropTable('clickedme')) return false;
    if (!DBUtil::dropTable('clickedme_settings')) return false;
    
    // Deletion successful
    return true;
}
?>