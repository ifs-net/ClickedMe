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

use Doctrine\ORM\Mapping as ORM;
use DoctrineExtensions\StandardFields\Mapping\Annotation as ZK;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Visits entity class.
 *
 * Annotations define the entity mappings to database.
 *
 * @ORM\Entity
 * @ORM\Table(name="clickedme_visits")
 * @ORM\Entity(repositoryClass="ClickedMe_Entity_Repository_Visits")
 * @ORM\HasLifecycleCallbacks 
 */
class ClickedMe_Entity_Visits extends Zikula_EntityAccess
{
    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The following are annotations which define the clicked user id field.
     *
     * @ORM\Column(type="integer")
     * @var integer $clickedUserId.
     */
    private $clickedUserId;

    /**
     * @ORM\Column(type="integer")
     * @ZK\StandardFields(type="userid", on="create")
     * @var integer $createdUserId.
     */
    protected $createdUserId;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @var datetime $createdDate.
     */
    protected $createdDate;

    public function getId()
    {
        return $this->id;
    }

    public function getClickedUserId()
    {
        return $this->clickedUserId;
    }

    public function getCreatedUserId()
    {
        return $this->createdUserId;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function setClickedUserId($clickedUserId)
    {
        $this->clickedUserId = $clickedUserId;
    }
}