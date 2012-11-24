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
class ClickedMe_Controller_Admin extends Zikula_AbstractController
{
    /**
     * This method provides a generic item list overview.
     *
     * @return string
     */
    public function main()
    {
        // Set page title
        PageUtil::setVar('title', __('ClickedMe Module - Administration'));
        $form = FormUtil::newForm('ClickedMe', $this);
        return $form->execute('clickedme_admin_main.tpl', new ClickedMe_Form_Handler_Admin_Main());
    }
}