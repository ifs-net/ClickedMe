<?php
/**
 * ClickedMe.
 *
 * @copyright Copyrighted 2008 - 2012 Florian Schießl
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package ClickedMe
 * @author Florian Schießl <info@ifs-net.de>.
 * @link http://www.ifs-net.de
 */

/**
 * This is the User controller class providing navigation and interaction functionality.
 */
class ClickedMe_Api_User extends Zikula_AbstractApi
{
    /**
     * Get Timestamp / waitUntil - value
     * Deletes old waituntil values automatically
     *
     * @return int
     */
    public function getWaitUntil()
    {
        $waitUntil = (int)UserUtil::getVar('_ClickedMe_waitUntil');
        if (($waitUntil < time() || ($waitUntil == '') || !isset($waitUntil))) {
            // outdated...
            UserUtil::delVar('_ClickedMe_waituntil');
            return 0;
        } else {
            return $waitUntil;
        }
    }

    /**
     * check if User has a waiting period and is locked for tracking
     *
     * @return boolean
     */
    public function hasWaitUntil()
    {
        $waitUntil = ModUtil::apiFunc('ClickedMe', 'User', 'getWaitUntil');
        return ($waitUntil > 0);
    }

    /**
     * Check if user has tracking deaktivated
     * Deletes old waituntil values automatically
     *
     * @return boolean
     */
    public function getTrackingDisabled($uid)
    {
        if (!($uid > 0)) {
            $uid = UserUtil::getVar('uid');
        }
        $ClickedMe_trackingDisabled = UserUtil::getVar('_ClickedMe_trackingDisabled');
        return ($ClickedMe_trackingDisabled == 1);
    }

    /**
     * Track a user's visit
     * 
     * @return void (empty string, called in user profile templates)
     */
    public function trackVisit()
    {
        // get user IDs
        $userId = (int)UserUtil::getVar('uid');
        $clickedUserId = (int) FormUtil::getPassedValue('uid');
        if (!($clickedUserId > 1)) {
            // It seems as if there is no (valid) uid URL Parameter
            $clickedUserName = FormUtil::getPassedValue('uname');
            $clickedUserId = (int)UserUtil::getIdFromName($clickedUserName);
        }
        if (($userId> 1) && ($userId != $clickedUserId) && ($clickedUserId > 1)) {
            // a registered User visited another profile than the own
            // check if the user has tracking enabled
            $user1_trackingDisabled = ModUtil::apiFunc('ClickedMe', 'user', 'getTrackingDisabled',array('uid' => $userId));
            $user2_trackingDisabled = ModUtil::apiFunc('ClickedMe', 'user', 'getTrackingDisabled',array('uid' => $clickedUserId));
            $user2_hasWaitUntil = ModUtil::apiFunc('ClickedMe', 'user', 'hasWaitUntil',array('uid' => $clickedUserId));
            if (($user1_trackingDisabled == false) && ($user2_trackingDisabled == false) && ($user2_hasWaitUntil == false)) {
                
                $visit = $this->entityManager->getRepository('ClickedMe_Entity_Visits')->getVisit($userId, $clickedUserId);
                if (is_object($visit)) {
                    // Delete old click
                    print "lösche ".$visit->getClickedUserId();
                    $this->entityManager->remove($visit);
                    $this->entityManager->flush();
                }
                
                // Insert visit into database
                $visit = new ClickedMe_Entity_Visits();
                $visit->setClickedUserId($clickedUserId);
                $this->entityManager->persist($visit);
                $this->entityManager->flush();
            }
        }
        return '';
    }
}