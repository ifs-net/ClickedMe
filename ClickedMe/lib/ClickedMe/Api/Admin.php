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
   class ClickedMe_Api_Admin extends Zikula_AbstractApi
   {
       /**
        * get available admin panel links
        *
        * @return array array of admin links
        */
       public function getlinks()
       {
           $links = array();
           if (SecurityUtil::checkPermission('ClickedMe::', '::', ACCESS_ADMIN)) {
               $links[] = array(
                           'url'   => ModUtil::url('ClickedMe', 'admin', 'main'),
                           'text'  => $this->__('Configure the ClickedMe module'),
                           'title' => $this->__('ClickedMe administration'),
                           'class' => 'z-icon-es-help',
                          );
           }
           return $links;
       }
   }