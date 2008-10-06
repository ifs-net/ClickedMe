<?php
/**
 * Populate pntables array for ClickedMe module
 * @return       array       The table information.
 */
function ClickedMe_pntables()
{
    // Initialise table array
    $pntable = array();

    // Get the name for the ClickedMe item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $ClickedMe = pnConfigGetVar('prefix') . '_clickedme';
    $ClickedMe_Settings = pnConfigGetVar('prefix') . '_clickedme_settings';

    // Set the table name
    $pntable['clickedme'] = $ClickedMe;
    $pntable['clickedme_settings'] = $ClickedMe_Settings;

    // Set the column names.  Note that the array has been formatted
    // on-screen to be very easy to read by a user.
    $pntable['clickedme_column'] = array(	'id'	=> 'pn_id',
						'uid'	=> 'pn_uid',
						'clicked_uid'	=> 'pn_clicked_uid',
						'timestamp'	=> 'pn_timestamp'	);

    $pntable['clickedme_settings_column'] = array(
						'uid'	=> 'pn_uid'
						);

    // Return the table information
    return $pntable;
}

?>