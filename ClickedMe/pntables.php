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
 * Populate pntables array for ClickedMe module
 * @return       array       The table information.
 */
function ClickedMe_pntables()
{
    // Initialise table array
    $pntable = array();

    // Get the name for the tables
    $ClickedMe = DBUtil::getLimitedTableName('clickedme');
    $ClickedMe_Settings = DBUtil::getLimitedTableName('clickedme_settings');

    // Set the table name
    $pntable['clickedme'] = $ClickedMe;
    $pntable['clickedme_settings'] = $ClickedMe_Settings;

    // Set the column names. 
    $pntable['clickedme_column'] = array(	'id'		=> 'pn_id',
						'uid'		=> 'pn_uid',
						'clicked_uid'	=> 'pn_clicked_uid',
						'timestamp'	=> 'pn_timestamp'	);

    $pntable['clickedme_column_def'] = array(	'id'		=> "I NOTNULL AUTO PRIMARY",
						'uid'		=> "I NOTNULL DEFAULT 0",
						'clicked_uid'	=> "I NOTNULL DEFAULT 0",
						'timestamp'	=> "C(11) NOTNULL DEFAULT 0");

   
    $pntable['clickedme_settings_column'] = array(	'uid'	=> 'pn_uid');
    
    $pntable['clickedme_settings_column_def'] = array(	'uid'	=> "I NOTNULL PRIMARY");
						

    // Return the table information
    return $pntable;
}