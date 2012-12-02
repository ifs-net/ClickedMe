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
 * Version.
 */
class ClickedMe_Version extends Zikula_AbstractVersion
{
    /**
     * Module meta data.
     *
     * @return array Module metadata.
     */
    public function getMetaData()
    {
        $meta = array();
        $meta['displayname']    = $this->__('ClickedMe');
        $meta['description']    = $this->__("Show your users who visited their profile");
        $meta['url']            = $this->__('ClickedMe');
        $meta['version']        = '3.0';
        return $meta;
    }
}