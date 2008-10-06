<?php

/**
 * initialise the ClickedMe module
 * @return       bool       true on success, false otherwise
 */
function ClickedMe_init()
{
    // Get datbase setup - note that both pnDBGetConn() and pnDBGetTables()
    // return arrays but we handle them differently.  For pnDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For pnDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    $dbconn  =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    $ClickedMetable  = &$pntable['clickedme'];
    $ClickedMecolumn = &$pntable['clickedme_column'];

    $ClickedMe_Settingstable  = &$pntable['clickedme_settings'];
    $ClickedMe_Settingscolumn = &$pntable['clickedme_settings_column'];

    // Create the table - the formatting here is not mandatory, but it does
    // make the SQL statement relatively easy to read.
    // Create a new data dictionary object
    $dict = &NewDataDictionary($dbconn);

    // Define array containing specific table options
	// This variable only need populating once as the same table options will
	// apply for all tables to be created.
    $taboptarray =& pnDBGetTableOptions();

    // Define the fields in the form:
    // $fieldname $type $colsize $otheroptions
    $flds = "
        $ClickedMecolumn[id]		I	AUTOINCREMENT PRIMARY,
        $ClickedMecolumn[uid]		I	NOTNULL DEFAULT 0,
        $ClickedMecolumn[clicked_uid]	I	NOTNULL DEFAULT 0,
        $ClickedMecolumn[timestamp]	C(11)	NOTNULL DEFAULT '0'
    ";

    $flds_settings = "
        $ClickedMe_Settingscolumn[uid]	I	NOTNULL PRIMARY
    ";

    // Creating the table
    $sqlarray = $dict->CreateTableSQL($ClickedMetable, $flds, $taboptarray);

    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dict->ExecuteSQLArray($sqlarray) != 2) {
        pnSessionSetVar('errormsg', _CLICKEDMECREATETABLEFAILED);
        return false;
    }

    // Creating the table
    $sqlarray = $dict->CreateTableSQL($ClickedMe_Settingstable, $flds_settings, $taboptarray);

    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dict->ExecuteSQLArray($sqlarray) != 2) {
        pnSessionSetVar('errormsg', _CLICKEDMECREATETABLEFORSETTINGSFAILED);
        return false;
    }

    // Initialisation successful
    return true;
}

/**
 * upgrade the pnWebLog module from an old version
 *
 * This function can be called multiple times
 * This function MUST exist in the pninit file for a module
 *
 * @author       Jim McDonald
 * @return       bool       true on success, false otherwise
*/
function ClickedMe_upgrade($oldversion)
{
    switch($oldversion) {
        case '0.20':
	    // we need to add the settings table
	    $dbconn  =& pnDBGetConn(true);
	    $pntable =& pnDBGetTables();

	    $ClickedMe_Settingstable  = &$pntable['clickedme_settings'];
	    $ClickedMe_Settingscolumn = &$pntable['clickedme_settings_column'];

	    $dict = &NewDataDictionary($dbconn);

	    $taboptarray =& pnDBGetTableOptions();

	    $flds_settings = "
	        $ClickedMe_Settingscolumn[uid]		I	NOTNULL PRIMARY
	        ";

	    $sqlarray = $dict->CreateTableSQL($ClickedMe_Settingstable, $flds_settings, $taboptarray);

	    // Check for an error with the database code, and if so set an
	    // appropriate error message and return
	    if ($dict->ExecuteSQLArray($sqlarray) != 2) {
	        pnSessionSetVar('errormsg', _CLICKEDMECREATETABLEFORSETTINGSFAILED);
	        return false;
	    }
	    break;
	default:
	    break;
    }
    return true;
}
		

/**
 * delete the ClickedMe module
 * @return       bool       true on success, false otherwise
 */
function ClickedMe_delete()
{
    // Get datbase setup - note that both pnDBGetConn() and pnDBGetTables()
    // return arrays but we handle them differently.  For pnDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For pnDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    $dbconn  =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $ClickedMetable  = &$pntable['clickedme'];
    $ClickedMe_Settingstable  = &$pntable['clickedme_settings'];

    // New Object DataDictionary
    $dict = &NewDataDictionary($dbconn);

    $sqlarray = $dict->DropTableSQL($ClickedMetable);

    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dict->ExecuteSQLArray($sqlarray) != 2) {
        pnSessionSetVar('errormsg', _CLICKEDMEDROPTABLEFAILED);
        // Report failed deletion attempt
        return false;
    }

    $sqlarray = $dict->DropTableSQL($ClickedMe_Settingstable);

    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dict->ExecuteSQLArray($sqlarray) != 2) {
        pnSessionSetVar('errormsg', _CLICKEDMEDROPTABLEFORSETTINGSFAILED);
        // Report failed deletion attempt
        return false;
    }

    // Deletion successful
    return true;
}


?>