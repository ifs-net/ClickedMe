<?php

/*
 * getSettings
 *
 * Get  user's privacy settings 
 *
 * @param	$args['uid']	int
 * @return	bool		true = anonymous, false = not anonymous
 */
function clickedme_userapi_getSettings($args) 
{
    $uid=(int)$args['uid'];
    if (!($uid>0)) return;
    
    // if there is a entry in the settings table we return true for anonymous and false for not anonymous
    
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();
    $table  = $pntable['clickedme_settings'];
    $column = &$pntable['clickedme_settings_column'];

    // delete old clicks if there are any...
    $sql =  "SELECT $column[uid] FROM $table WHERE $column[uid]='" . $uid ."'";
    $result = $dbconn->Execute($sql);
    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg','Error getting settings from table. Error-No: '.$dbconn->ErrorNo()."$sql");
        return false;
    }
    if ($result->RowCount()>0) return true;
    else return false;
} 

/*
 * storeSettings
 *
 * Stores user's settings - The variables are taken from the _POST-Array
 *
 * @return	bool;
 */
function clickedme_userapi_storeSettings() {

    // No tracking for unregistered users!
    if (!pnuserloggedin()) return;
    
    $anonymous = pnVarCleanFromInput('anonymous');

    // very simple:
    // if a user wants to be anonymous he will get a entry in the table with his user id
    // so we always first delete a entry if there is one and - if $anonymous is set - we
    // will add a new one.
    
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();
    $table  = $pntable['clickedme_settings'];
    $column = &$pntable['clickedme_settings_column'];

    // delete old clicks if there are any...
    $sql =  "DELETE FROM $table WHERE $column[uid]='" . (int)pnVarPrepForStore(pnUserGetVar('uid'))."'";
    $dbconn->Execute($sql);
    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg','Error deleting old setting '.$dbconn->ErrorNo());
        return false;
    }
    
    // User does not want to be anonymous? So we can return now.
    if (!isset($anonymous)) return true;
    
    // register new settings if needed
    $sql =  "INSERT INTO $table ($column[uid]) VALUES ('" . pnVarPrepForStore(pnUserGetVar('uid'))."')";
    $dbconn->Execute($sql);
    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg','Error saving new settings. Error No:'.$dbconn->ErrorNo());
        return false;
    }
    return true;
} 

/*
 * addClick
 *
 * This function should be called from the pnRender-templates.
 * It registers a new click from the clicker to the actual profile.
 *
 * @param	$args[clicked_uid]		UID of the user that was clicked
 * @param	$args[clicked_uname]		username of the user that was clicked
 * @return	void;
 */
function clickedme_userapi_addClick($args) {

    // No tracking for unregistered users!
    if (!pnuserloggedin()) return;
    
    // If the user wants to be anonymous we can return also
    if (clickedme_userapi_getSettings(array('uid'=>pnUserGetVar('uid')))) return;
    
    // get the user-id
    $clicked_uid=$args[clicked_uid];
    if (!($clicked_uid > 0)) {
	// we do not have a user-id. So we need to check if there is an username as an argument.
	$uname=$args[uname];
	if (isset($uname) && (pnUserGETIDFromName($uname)>0)) $clicked_uid=pnUserGetIDFromName($uname);
	else return;
    }
    
    // ok we now have the user id of the clicked user - let's go on and get the database table
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();
    $table  = $pntable['clickedme'];
    $column = &$pntable['clickedme_column'];

    // delete old clicks if there are any...
    $sql =  "DELETE FROM $table 
	    WHERE $column[uid]=" . pnVarPrepForStore(pnUserGetVar('uid')) . "
	    AND $column[clicked_uid]= " . pnVarPrepForStore($clicked_uid) . "
	    LIMIT 1
    ";
    $dbconn->Execute($sql);
    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg','Error deleting old clicks for the user:'.$dbconn->ErrorNo());
        return;
    }
    
    // delete all old clicks that are no longer interesting. We don't want to store old data!
    // delete data that is older than $days days...
    $days = 60;
    $new_timestamp= time()-(60*60*24*$days); 
    $sql =  "DELETE FROM $table 
	    WHERE $column[timestamp] <" . pnVarPrepForStore($new_timestamp) . "
    ";
    $dbconn->Execute($sql);
    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg','Error deleting old clicks for the user:'.$dbconn->ErrorNo());
        return;
    }

    // register new click
    $sql =  "INSERT INTO $table (
                $column[uid],
                $column[clicked_uid],
                $column[timestamp]
	                                )
            VALUES (
	    '" . pnVarPrepForStore(pnUserGetVar('uid'))."',
	    '" . pnVarPrepForStore($clicked_uid) ."',
	    '" . pnVarPrepForStore(time()) ."')";
    $dbconn->Execute($sql);
    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg','Error saving click. Error No:'.$dbconn->ErrorNo());
        return;
    }
    return;
}

/**
 * get the last visitors of the own profile page
 * 
 * @param	$args['uid']	int	uid of the user that has been "viewed"
 * @param	$args['amount']	int	how many items should be returned?
 * @return	array
 */
function clickedme_userapi_getViewers($args)
{

    $uid = (int)$args['uid'];
    if (!($uid>0)) return;
    $args['amount']++;
    
    // Database information
    $dbconn =& pnDBGetConn(true);
    $pntable =pnDBGetTables();
    $table = $pntable['clickedme'];
    $column = &$pntable['clickedme_column'];

    // Get the data from the database
    $sql = "SELECT $column[uid]
            FROM $table
	    WHERE $column[clicked_uid] = ".(int)$uid."
	      AND $column[uid] != ".$uid."
            ORDER by $column[timestamp] DESC
	    ";
    $result = $dbconn->SelectLimit($sql, (int)$args['amount']);
    if ($dbconn->ErrorNo() != 0) {
        return;
    }
    // if there is nothing return!
    if (!($result->RowCount()==0)) {
        $items=array();
	for (; !$result->EOF; $result->MoveNext()) {
	    unset($item);
	    list($item[uid])=$result->fields;
	    $item[uname]=pnUsergetVar('uname',$item[uid]);
	    $items[]=$item;
	}
	return $items;
    }
    return;
}

/**
 * get the history of profile pages you have visited yourself
 * 
 * @param	$args['uid']	int	uid of the user
 * @param	$args['amount']	int	how many items should be returned?
 * @return	array
 */
function clickedme_userapi_getHistory($args)
{

    $uid = (int)$args['uid'];
    if (!($uid>0)) return;
    $args['amount']++;
    
    // Database information
    $dbconn =& pnDBGetConn(true);
    $pntable =pnDBGetTables();
    $table = $pntable['clickedme'];
    $column = &$pntable['clickedme_column'];

    // Get the data from the database
    $sql = "SELECT $column[clicked_uid]
            FROM $table
	    WHERE $column[clicked_uid] != ".(int)$uid."
	      AND $column[uid] = ".$uid."
            ORDER by $column[timestamp] DESC
	    ";
    $result = $dbconn->SelectLimit($sql, (int)$args['amount']);
    if ($dbconn->ErrorNo() != 0) {
        return;
    }
    // if there is nothing return!
    if (!($result->RowCount()==0)) {
        $items=array();
	for (; !$result->EOF; $result->MoveNext()) {
	    unset($item);
	    list($item[uid])=$result->fields;
	    $item[uname]=pnUsergetVar('uname',$item[uid]);
	    $items[]=$item;
	}
	return $items;
    }
    return;
}

/**
 * get the average visits of a profile
 * 
 * @param	$args['uid']	int	uid of the user
 * @return	array
 */
function clickedme_userapi_getAvg($args)
{

    $uid = (int)$args['uid'];
    if (!($uid>0)) return;
    
    $ts = time()-(7*24*60*60);
    
    // Database information
    $dbconn =& pnDBGetConn(true);
    $pntable =pnDBGetTables();
    $table = $pntable['clickedme'];
    $column = &$pntable['clickedme_column'];

    // Get the data from the database
    $sql = "SELECT $column[clicked_uid]
            FROM $table
	    WHERE $column[clicked_uid] = ".(int)$uid."
	      AND $column[uid] != ".$uid."
	      AND $column[timestamp] > $ts
	    ";
    $result = $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
        return;
    }
    // if there is nothing return!
    $counter = $result->RowCount();
    $avg=(string)($counter/7);
    return substr($avg,0,4);
}

?>