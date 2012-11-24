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


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

use DoctrineExtensions\Paginate\Paginate;

/**
 * Repository class used to implement own convenience methods for performing certain DQL queries.
 *
 * This is the base repository class for product image entities.
 */
class ClickedMe_Entity_Repository_Visits extends EntityRepository
{
    
    /**
     * Retrieve visit of a given userId and clickedUserId
     * 
     * @return array
     */
    public function getVisit($userId, $clickedUserId)
    {
        $visits = $this->selectWhere('tbl.createdUserId = '.(int)$userId.' and tbl.clickedUserId = '.(int)$clickedUserId);
        return $visits[0]; // We need the first (there cannot be more than one!) one
    }

    /**
     * Retrieve visits for a userId with latest visit on top
     * 
     * @return array
     */
    public function getVisits($userId, $itemsToShow = 10)
    {
        $visits = $this->selectWherePaginated('tbl.clickedUserId = '.(int)$userId, 'createdDate DESC',1,$itemsToShow);
        $visits = $visits[0];
        return $visits;
    }

    /**
     * @var string The default sorting field/expression.
     */
    protected $defaultSortingField = 'clickedUserId';

    /**
     * Retrieves an array with all fields which can be used for sorting instances.
     *
     * @TODO to be refactored
     * @return array
     */
    public function getAllowedSortingFields()
    {
        return array(
                     'clickedUserId',
                     'createdUserId',
                     'createdDate'
        );
    }

    /**
     * Get default sorting field.
     *
     * @return string
     */
    public function getDefaultSortingField()
    {
        return $this->defaultSortingField;
    }

    /**
     * Set default sorting field.
     *
     * @param string $defaultSortingField.
     *
     * @return void
     */
    public function setDefaultSortingField($defaultSortingField)
    {
        $this->defaultSortingField = $defaultSortingField;
    }



    /**
     * Return name of the field used as title / name for entities of this repository.
     *
     * @return string name of field to be used as title. 
     */
    public function getTitleFieldName()
    {
        $fieldName = 'createdUserId';
        return $fieldName;
    }

    /**
     * Return name of the field used for describing entities of this repository.
     *
     * @return string name of field to be used as description. 
     */
    public function getDescriptionFieldName()
    {
        $fieldName = '';
        return $fieldName;
    }

    /**
     * Return name of the first upload field which is capable for handling images.
     *
     * @return string name of field to be used for preview images 
     */
    public function getPreviewFieldName()
    {
        $fieldName = '';
        return $fieldName;
    }

    /**
     * Returns an array of additional template variables which are specific to the object type treated by this repository.
     *
     * @param string $context Usage context (allowed values: controllerAction, api, actionHandler, block, contentType).
     * @param array  $args    Additional arguments.
     *
     * @return array List of template variables to be assigned.
     */
    public function getAdditionalTemplateParameters($context = '', $args = array())
    {
        if (!in_array($context, array('controllerAction', 'api', 'actionHandler', 'block', 'contentType'))) {
            $context = 'controllerAction';
        }

        $templateParameters = array();

        // nothing per default, this is for manual enhancements
        // in the concrete child class you could do something like
        // $parameters = parent::getAdditionalTemplateParameters($context, $args);
        // $parameters['myvar'] = 'myvalue';
        // return $parameters;

        return $templateParameters;
    }

    /**
     * Helper method for truncating the table.
     * Used during installation when inserting default data.
     */
    public function truncateTable()
    {
        $query = $this->getEntityManager()
                 ->createQuery('DELETE ClickedMe_Entity_Visits');
        $query->execute();
    }

    /**
     * Select object from the database.
     *
     * @param mixed   $id       The id (or array of ids) to use to retrieve the object (optional) (default=null).
     * @param boolean $useJoins Whether to include joining related objects (optional) (default=true).
     *
     * @return array|ClickedMe_Entity_Visits retrieved data array or ClickedMe_Entity_Visits instance
     */
    public function selectById($id = 0, $useJoins = true)
    {
        // check id parameter
        if ($id == 0) {
            return LogUtil::registerArgsError();
        }

        $where = '';
        if (is_array($id)) {
            foreach ($id as $fieldName => $fieldValue) {
                if (!empty($where)) {
                    $where .= ' AND ';
                }
                $where .= 'tbl.' . DataUtil::formatForStore($fieldName) . ' = \'' . DataUtil::formatForStore($fieldValue) . '\'';
            }
        } else {
            $where .= 'tbl.id = ' . DataUtil::formatForStore($id);
        }

        $query = $this->_intBaseQuery($where, '', $useJoins);

        return $query->getOneOrNullResult();
    }


    /**
     * Select with a given where clause.
     *
     * @param string  $where    The where clause to use when retrieving the collection (optional) (default='').
     * @param string  $orderBy  The order-by clause to use when retrieving the collection (optional) (default='').
     * @param boolean $useJoins Whether to include joining related objects (optional) (default=true).
     *
     * @return ArrayCollection collection containing retrieved ClickedMe_Entity_Visits instances
     */
    public function selectWhere($where = '', $orderBy = '', $useJoins = true)
    {
        $query = $this->_intBaseQuery($where, $orderBy, $useJoins);

        return $query->getResult();
    }

    /**
     * Select with a given where clause and pagination parameters.
     *
     * @param string  $where          The where clause to use when retrieving the collection (optional) (default='').
     * @param string  $orderBy        The order-by clause to use when retrieving the collection (optional) (default='').
     * @param integer $currentPage    Where to start selection
     * @param integer $resultsPerPage Amount of items to select
     * @param boolean $useJoins       Whether to include joining related objects (optional) (default=true).
     *
     * @return Array with retrieved collection and amount of total records affected by this query.
     */
    public function selectWherePaginated($where = '', $orderBy = '', $currentPage = 1, $resultsPerPage = 25, $useJoins = true)
    {
        $query = $this->_intBaseQuery($where, $orderBy, $useJoins);
        $offset = ($currentPage-1) * $resultsPerPage;

        // count the total number of affected items
        $count = Paginate::getTotalQueryResults($query);

        $query->setFirstResult($offset)
              ->setMaxResults($resultsPerPage);

        $result = $query->getResult();

        return array($result, $count);
    }

    /**
     * Select entities by a given search fragment.
     *
     * @param string  $fragment       The fragment to search for.
     * @param string  $exclude        Comma separated list with ids to be excluded from search.
     * @param string  $orderBy        The order-by clause to use when retrieving the collection (optional) (default='').
     * @param integer $currentPage    Where to start selection
     * @param integer $resultsPerPage Amount of items to select
     * @param boolean $useJoins       Whether to include joining related objects (optional) (default=true).
     *
     * @return Array with retrieved collection and amount of total records affected by this query.
     */
    public function selectSearch($fragment = '', $exclude = array(), $orderBy = '', $currentPage = 1, $resultsPerPage = 25, $useJoins = true)
    {
        $where = '';
        if (count($exclude) > 0) {
            $exclude = DataUtil::formatForStore($exclude);
            $where .= 'tbl.id NOT IN (' . implode(', ', $exclude) . ')';
        }

        $fragment = DataUtil::formatForStore($fragment);

        $whereSub = '';
        $whereSub .= ((!empty($whereSub)) ? ' OR ' : '') . 'tbl.name LIKE \'%' . $fragment . '%\'';

        if (!empty($whereSub)) {
            $where .= ((!empty($where)) ? ' AND (' . $whereSub . ')' : $whereSub);
        }

        return $this->selectWherePaginated($where, $orderBy, $currentPage, $resultsPerPage, $useJoins);
    }

    /**
     * Select count with a given where clause.
     *
     * @param string  $where    The where clause to use when retrieving the object count (optional) (default='').
     * @param boolean $useJoins Whether to include joining related objects (optional) (default=true).
     *
     * @return integer amount of affected records
     * @TODO fix usage of joins; please remove the first line and test.
     */
    public function selectCount($where = '', $useJoins = true)
    {
        $useJoins = false;

        $selection = 'COUNT(tbl.id) AS numVisitss';
        if ($useJoins === true) {
            $selection .= $this->addJoinsToSelection();
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select($selection)
           ->from('ClickedMe_Entity_Visits', 'tbl');

        if ($useJoins === true) {
            $this->addJoinsToFrom($qb);
        }

        if (!empty($where)) {
            $qb->where($where);
        }

        $query = $qb->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * Check for unique values.
     *
     * @param string $fieldName  The name of the property to be checked
     * @param string $fieldValue The value of the property to be checked
     * @param int    $excludeid  Id of product images to exclude (optional).
     * @return boolean result of this check, true if the given product image does not already exist
     */
    public function detectUniqueState($fieldName, $fieldValue, $excludeid = 0)
    {
        $where = 'tbl.' . $fieldName . ' = \'' . DataUtil::formatForStore($fieldValue) . '\'';

        if ($excludeid > 0) {
            $where .= ' AND tbl.id != \'' . (int) DataUtil::formatForStore($excludeid) . '\'';
        }

        $count = $this->selectCount($where);
        return ($count == 0);
    }


    /**
     * Build a generic Doctrine query supporting WHERE and ORDER BY
     *
     * @param string  $where    The where clause to use when retrieving the collection (optional) (default='').
     * @param string  $orderBy  The order-by clause to use when retrieving the collection (optional) (default='').
     * @param boolean $useJoins Whether to include joining related objects (optional) (default=true).
     *
     * @return Doctrine\ORM\Query query instance to be further processed
     */
    protected function _intBaseQuery($where = '', $orderBy = '', $useJoins = true)
    {
        $selection = 'tbl';
        if ($useJoins === true) {
            $selection .= $this->addJoinsToSelection();
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select($selection)
           ->from('ClickedMe_Entity_Visits', 'tbl');

        if ($useJoins === true) {
            $this->addJoinsToFrom($qb);
        }

        if (!empty($where)) {
            $qb->where($where);
        }

        // add order by clause
        if (!empty($orderBy)) {
            $qb->add('orderBy', 'tbl.' . $orderBy);
        }

        $query = $qb->getQuery();

// TODO - see https://github.com/zikula/core/issues/118
        // use FilterUtil to support generic filtering
        //$fu = new FilterUtil('ClickedMe', $this);

        // you could set explicit filters at this point, something like
        // $fu->setFilter('type:eq:' . $args['type'] . ',id:eq:' . $args['id']);
        // supported operators: eq, ne, like, lt, le, gt, ge, null, notnull

        // process request input filters and add them to the query.
        //$fu->enrichQuery($query);


        return $query;
    }

    /**
     * Helper method to add join selections.
     *
     * @return String Enhancement for select clause.
     */
    protected function addJoinsToSelection()
    {
        $selection = '';
        return $selection;
    }

    /**
     * Helper method to add joins to from clause.
     *
     * @param Doctrine\ORM\QueryBuilder $qb query builder instance used to create the query.
     *
     * @return String Enhancement for from clause.
     */
    protected function addJoinsToFrom(QueryBuilder $qb)
    {
        return $qb;
    }
}