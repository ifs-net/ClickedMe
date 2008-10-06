<?php
/**
* @package      ClickedMe
* @version      $Id$
* @author       Florian Schießl
* @link         http://www.ifs-net.de
* @copyright    Copyright (C) 2008
* @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
*/

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
    if (!($uid>1)) return;
    else if (DBUtil::selectObjectCountByID('clickedme_settings',(int)$args['uid'],'uid') > 0) return true;
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
    
    $anonymous = FormUtil::getPassedValue('anonymous');

    // very simple:
    // if a user wants to be anonymous he will get a entry in the table with his user id
    // so we always first delete a entry if there is one and - if $anonymous is set - we
    // will add a new one.
    
    // delete old
    DBUtil::deleteObjectByID('clickedme_settings',pnUserGetVar('uid'),'uid');
    
    // store new if we need a new setting stored
    if (!isset($anonymous)) return true;
    $obj = array('uid'=>pnUserGetVar('uid'));
    DBUtil::insertObject($obj,'clickedme_settings');
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
    $clicked_uid=$args['clicked_uid'];
    if (!($clicked_uid > 1)) {
	// we do not have a user-id. So we need to check if there is an username as an argument.
	$uname=$args[uname];
	if (isset($uname) && (pnUserGetIDFromName($uname)>0)) $clicked_uid=pnUserGetIDFromName($uname);
	else return;
    }

    // we do not need to register clicks on the own profile page
    if (pnUsergetvar('uid') == $clicked_uid) return;
    
    // ok we now have the user id of the clicked user - let's go on and get the database table
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();
    $table  = $pntable['clickedme'];
    $column = &$pntable['clickedme_column'];

    // delete old clicks if there are any...
    $where = "    WHERE ".$column['uid']."=" . pnVarPrepForStore(pnUserGetVar('uid')) . "
	    AND ".$column['clicked_uid']."= " . pnVarPrepForStore($clicked_uid);
    DBUtil::deleteWhere('clickedme',$where);
    
    // delete all old clicks that are no longer interesting. We don't want to store old data!
    $new_timestamp= time()-(60*60*24*60); // 60 days are enough!
    $where ="   WHERE ".$column['timestamp']." <" . pnVarPrepForStore($new_timestamp);
    DBUtil::deleteWhere('clickedme',$where);

    // register new click
    $obj = array (	'uid'		=> pnUserGetVar('uid'),
			'clicked_uid'	=> $clicked_uid,
			'timestamp'	=> time()	);
    DBUtil::insertObject($obj,'clickedme');
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
	$amount = (int)$args['amount'];
	$uid  = (int)$args['uid'];
	if (!($uid>1)) return;
    
    // Database information
	$tables =& pnDBGetTables();
    $column = $tables['clickedme_column'];

    $joinInfo[] = array ( 'join_table'			=> 'clickedme',   // table for the join
                          'join_field'			=> array('id', 'uid', 'clicked_uid','timestamp'),   // field in the join table that should be in the result with
                          'object_field_name'   => array('id', 'uid', 'clicked_uid','timestamp'),   // ...this name for the new column
                          'compare_field_table' => 'uid',   // regular table column that should be equal to
                          'compare_field_join'  => 'uid');  // ...the table in join_table

    // Get the data from the database
    $where   = "WHERE ".$column['clicked_uid']." = ".$uid;
    $orderby  = "ORDER by ".$column['timestamp']." DESC";
    return DBUtil::selectExpandedObjectArray('users',$joinInfo,$where,$orderby,-1,$amount);
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
	$amount = (int)$args['amount'];
	$uid  = (int)$args['uid'];
	if (!($uid>1)) return;
    
    // Database information
	$tables =& pnDBGetTables();
    $column = $tables['clickedme_column'];

    $joinInfo[] = array ( 'join_table'			=> 'clickedme',   // table for the join
                          'join_field'			=> array('id', 'uid', 'clicked_uid','timestamp'),   // field in the join table that should be in the result with
                          'object_field_name'   => array('id', 'uid', 'clicked_uid','timestamp'),   // ...this name for the new column
                          'compare_field_table' => 'uid',   // regular table column that should be equal to
                          'compare_field_join'  => 'clicked_uid');  // ...the table in join_table

    // Get the data from the database
    $where   = "WHERE a.".$column['uid']." = ".$uid;
    $orderby  = "ORDER by a.".$column['timestamp']." DESC";
    return DBUtil::selectExpandedObjectArray('users',$joinInfo,$where,$orderby,-1,$amount);
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
    if (!($uid>1)) return;
    
    $ts = time()-(7*24*60*60);
    
    // Database information
    $dbconn =& pnDBGetConn(true);
    $pntable =pnDBGetTables();
    $table = $pntable['clickedme'];
    $column = &$pntable['clickedme_column'];

    // Get the data from the database
    $where = "WHERE ".$column['clicked_uid']." = ".(int)$uid." AND ".$column['timestamp']." > ".$ts;
	    
    $counter = DBUtil::selectObjectCount('clickedme',$where);
    $avg=(string)($counter/7);
    return substr($avg,0,4);
}
?>