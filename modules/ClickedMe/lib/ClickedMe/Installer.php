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
 * Installer.
 */
class ClickedMe_Installer extends Zikula_AbstractInstaller
{
    /**
     * Install the ClickedMe module.
     *
     * This function is only ever called once during the lifetime of a particular
     * module instance.
     *
     * @return boolean True on success, false otherwise.
     */
    public function install()
    {

        // Create database tables.
        try {
            DoctrineHelper::createSchema($this->entityManager, array(
                'ClickedMe_Entity_Visits'
            ));
        } catch (Exception $e) {
            return LogUtil::registerError($e->getMessage());
            return false;
        }
        // Set Module variables
        ModUtil::setVar('ClickedMe','waitingPeriod',7);
        // Initialisation successful.
        return true;
    }

    /**
     * Upgrade the errors module from an old version
     *
     * This function must consider all the released versions of the module!
     * If the upgrade fails at some point, it returns the last upgraded version.
     *
     * @param  string $oldVersion   version number string to upgrade from
     *
     * @return mixed  true on success, last valid version string or false if fails
     */
    public function upgrade($oldversion)
    {
        // Update successful
        return true;
    }


    /**
     * Uninstall the module.
     *
     * This function is only ever called once during the lifetime of a particular
     * module instance.
     *
     * @return bool True on success, false otherwise.
     */
    public function uninstall()
    {
      // Drop database tables
        DoctrineHelper::dropSchema($this->entityManager, array(
            'ClickedMe_Entity_Visits'
        ));

        // Remove module vars.
        $this->delVars();
        
        // Delete all user variables
        // # ClickedMe_waitUntil
        // # ClickedMe_trackingDisabled
        // Todo
        // ...
        
        // Deletion successful.
        return true;
    }
}