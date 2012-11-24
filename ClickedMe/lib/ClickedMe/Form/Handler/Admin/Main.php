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
class ClickedMe_Form_Handler_Admin_Main extends Zikula_Form_AbstractHandler
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
        $this->view->assign('ClickedMe_waitingPeriod', ModUtil::getVar('ClickedMe', 'waitingPeriod'));
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
        if (!SecurityUtil::checkPermission('ClickedMe::', '::', ACCESS_ADMIN)) {
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
        
        // set module variable
        if (ModUtil::setVar('ClickedMe', 'waitingPeriod', $data['ClickedMe_waitingPeriod'])) {
            LogUtil::registerStatus(__('Settings updated successfully'));
        } else {
            LogUtil::registerError(__('Settings could not be updated'));
        }
        return $view->redirect(ModUtil::url('ClickedMe','admin'));
    }
}