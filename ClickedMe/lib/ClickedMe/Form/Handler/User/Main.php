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
 * This class provides a handler to the modules preferences.
 */
class ClickedMe_Form_Handler_User_Main extends Zikula_Form_AbstractHandler
{
    /**
     * Setup form.
     *
     * @param Zikula_Form_View $view Current Zikula_Form_View instance.
     *
     * @return boolean
     *
     * @throws Zikula_Exception_Forbidden If the current user does not have adequate permissions to perform this *
     */
    function initialize(Zikula_Form_View $view)
    {
        $this->view->caching = false;
        $ClickedMe_trackingDisabled = ModUtil::apiFunc('ClickedMe', 'User', 'getTrackingDisabled');
        $ClickedMe_waitUntil = ModUtil::apiFunc('ClickedMe', 'User', 'getWaitUntil');
        if ($ClickedMe_waitUntil > 0) {
            $waitingTime = round(($ClickedMe_waitUntil-time())/(24*60*60),2);
        } else {
            $waitingTime = 0;
        }
        $this->view->assign('waitingTime', $waitingTime);
        $this->view->assign('ClickedMe_trackingDisabled', $ClickedMe_trackingDisabled);
        $this->view->assign('ClickedMe_waitUntil', $ClickedMe_waitUntil);

        // get own visits
        $this->userId = UserUtil::getVar('uid');
        $visits = $this->entityManager->getRepository('ClickedMe_Entity_Visits')->getVisits($this->userId);
        $this->view->assign('visits', $visits);
        
    }
    /**
     * Handle form submission.
     *
     * @param Zikula_Form_View $view  Current Zikula_Form_View instance.
     * @param array            &$args Arguments.
     *
     * @return bool|void
     */
    function handleCommand(Zikula_Form_View $view, &$args)
    {
        // Security check
        if (!SecurityUtil::checkPermission('ClickedMe::', '::', ACCESS_COMMENT)) {
            return LogUtil::registerPermissionError();
        }

        if ($args['commandName'] == 'cancel') {
            return true;
        }

        // check for valid form
        if (!$view->isValid()) {
            return false;
        }
        $data = $view->getValues();
        
        // set or delete user variable
        if ($data['ClickedMe_trackingDisabled'] == 1) {
            if (UserUtil::getVar('_ClickedMe_trackingDisabled') != '1') {
                // Tracking should be disabled for this user
                if (UserUtil::setVar('_ClickedMe_trackingDisabled',1)) {
                    LogUtil::registerStatus(__('Tracking was successfully disabled for your account'));
                } else {
                    LogUtil::registerError(__('An error occured: Tracking could not be disabled for your account'));
                }
            } else {
                LogUtil::registerStatus(__('No changes made - Logging was already disabled'));
            }
        } else {
            // Tracking is OK - but we have to check if there is really something to do
            if (UserUtil::getVar('_ClickedMe_trackingDisabled') == '1') {
                if (UserUtil::delVar('_ClickedMe_trackingDisabled')) {
                    LogUtil::registerStatus(__('Tracking was successfully enabled for your account'));
                    // Set a waiting period for this user
                    $days = (int) ModUtil::getVar('ClickedMe','waitingPeriod');
                    $waitUntil = ModUtil::apiFunc('ClickedMe', 'User', 'getWaitUntil');
                    if (($waitUntil < time()) && ($days > 0)) {
                        // we have to set a new waitUntil variable
                        UserUtil::setVar('_ClickedMe_waitUntil', time()+24*60*60*$days);
                    }
                } else {
                    LogUtil::registerError(__('An error occured: Tracking could not be enabled for your account'));
                }
            } else {
                LogUtil::registerStatus(__('No changes made - Logging was already enabled'));
            }
        }
        return $view->redirect(ModUtil::url('ClickedMe'));
    }
}