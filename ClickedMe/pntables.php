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
 * Populate tables array for ClickedMe module
 * @return       array       The table information.
 */
function ClickedMe_pntables()
{
    // Initialise table array
    $table = array();

    // Get the name for the tables
    $ClickedMe = DBUtil::getLimitedTableName('clickedme');
    $ClickedMe_Settings = DBUtil::getLimitedTableName('clickedme_settings');

    // Set the table name
    $table['clickedme'] = $ClickedMe;
    $table['clickedme_settings'] = $ClickedMe_Settings;

    // Set the column names. 
    $table['clickedme_column'] = array(	
        'id'            => 'pn_id',
        'uid'           => 'pn_uid',
        'clicked_uid'   => 'pn_clicked_uid',
        'timestamp'     => 'pn_timestamp'
        );

    $table['clickedme_column_def'] = array(
        'id'            => "I NOTNULL AUTO PRIMARY",
        'uid'           => "I NOTNULL DEFAULT 0",
        'clicked_uid'   => "I NOTNULL DEFAULT 0",
        'timestamp'     => "C(11) NOTNULL DEFAULT 0"
        );
   
    $table['clickedme_settings_column'] = array(	
        'uid'           => 'pn_uid'
        );
    
    $table['clickedme_settings_column_def'] = array(	
        'uid'           => "I NOTNULL PRIMARY"
        );

    // Return the table information
    return $table;
}