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
class ClickedMe_Controller_User extends Zikula_AbstractController
{
    /**
     * This method provides a generic item list overview.
     *
     * @return string
     */
    public function main()
    {
        $form = FormUtil::newForm('ClickedMe', $this);
        // Set page title
        PageUtil::setVar('title', __('Tracking preferences'));
        return $form->execute('clickedme_user_main.tpl', new ClickedMe_Form_Handler_User_Main());
    }
}