<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace DataModeling\DataAccess\Query;

/* Use statements for Framework namespaces */
use DataModeling\DataAccess\Model;
use DataModeling\DataAccess\Interrupt;

/* Use statements for ZF2 namespaces */
use Zend\Validator;

/* Use statements for core php namespaces */
use SplObjectStorage;
use PDO;
use PDOStatement;

abstract class PDOQueryAbstract extends QueryAbstract
{

    /**
     * If the query uses a Limit clause, this will be the default
     *
     * @var Int
     */
    protected $mLimit = 100;

    /**
     * If the query uses an offset clause, this will be the default
     *
     * @var Int
     */
    protected $mOffset = 0;

    /**
     * Tells execute whether to kick off limit binding or not
     *
     * @var bool
     */
    protected $mLimitEnabled = true;

    /**
     * Stores the Order By Clause either as defined by derviced class or
     * accessor method SetOrderByClause
     *
     * @var string
     */
    protected $mOrderByClause = ' ';

    /**
     * Allows the caller to disable the empty model exception from being
     * throw and just return an empty SPL object instead.
     *
     * @var bool
     */
    protected $mThrowEmptyModelExceptions = false;

    /**
     * Set to the table alias in your derived class if you're using a join
     *
     * @var string
     */
    const TABLE_ALIAS = false;

    /**
     * Triggers the select clause to itemize each column in the select statement
     * vs just executing a SELECT * FROM style
     *
     * @var bool
     */
    const SELECT_COLUMNS = false;

    /**
     * This accessor allows the caller to set the behavior of a Query so that
     * it either throws an empty model exception when it can't find results
     * or it will return an empty SplObjectStorage object
     *
     * This behavior defaults to false
     *
     * @param bool $pBool
     */
    public function ThrowEmptyModelExceptions ($pBool)
    {
        $this->mThrowEmptyModelExceptions = true === $pBool ? true : false;
    }

    /**
     * Accessor -- SetLimit
     *
     * Sets the limit of the query
     *
     * @param int $pLimit
     *
     * @throws Interrupt\InvalidQueryLimitException
     */
    public function SetLimit ($pLimit)
    {
        if (false === Validator\StaticValidator::execute($pLimit, 'Digits'))
        {
            throw new Interrupt\InvalidQueryLimitException(get_class($this));
        }

        $this->mLimit = $pLimit;
    }

    /**
     * Accessor -- GetLimit
     *
     * Returns the limit that will be used for this query if supported
     *
     * @return int
     */
    public function GetLimit ()
    {
        return $this->mLimit;
    }

    /**
     * Accessor -- SetOffset
     *
     * Sets the offset of the query
     *
     * @param int $pOffset
     *
     * @throws Interrupt\InvalidQueryLimitException
     */
    public function SetOffset ($pOffset)
    {
        if (false === Validator\StaticValidator::execute($pOffset, 'Digits'))
        {
            throw new Interrupt\InvalidQueryOffsetException(get_class($this));
        }

        $this->mOffset = $pOffset;
    }

    /**
     * Accessor -- GetLimit
     *
     * Returns the offset that will be used for this query if supported
     *
     * @return int
     */
    public function GetOffset ()
    {
        return $this->mOffset;
    }

    /**
     * Accessor -- SetPayloadClass
     *
     * This method should be used to define the $this->mPayloadClass
     * property so that it can be used by the SetPayload function
     */
    protected function SetPayloadClass ()
    {
        $exploded_namespace = explode('\\', get_class($this));
        $class = array_pop($exploded_namespace);

        $namespace = implode('\\', $exploded_namespace);

        $this->mPayloadClass = $this->mNamespace . '\\Payload\\' . $class;
    }

    /**
     * Utility -- Execute
     *
     * executes the service call, logging, monitoring, etc for the service
     */
    public function Execute ()
    {
        $sql = $this->SelectClause(static::SELECT_COLUMNS);
        $sql .= $this->FromClause();
        $sql .= $this->WhereClause();
        $sql .= $this->OrderByClause();
        $sql .= $this->LimitClause();

        $stmt = $this->GetServiceWrapper()
            ->GetServiceObject()
            ->prepare($sql);

        $this->WhereBind($stmt);

        if (true === $this->mLimitEnabled)
        {
            $stmt->bindParam(':offset', $this->mOffset, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $this->mLimit, PDO::PARAM_INT);
        }

        $stmt->execute();

        $this->mServiceResponse = $stmt->fetchAll();

        $this->mResult = new SplObjectStorage();

        if (empty($this->mServiceResponse))
        {
            if (true === $this->mThrowEmptyModelExceptions)
            {
                throw new Interrupt\ModelNotFoundFailure(get_class($this), get_class($this->GetServiceWrapper()->GetPrototype()), $stmt->queryString);
            }
        }
        else
        {
            $this->ProcessResponse();
        }

        return $this->mResult;
    }

    /**
     * Helper -- SelectClause
     *
     * returns a Select statement.
     *
     * @param $pWithColumns bool
     *
     * @return string
     */
    protected function SelectClause ($pWithColumns)
    {
        return $this->GetServiceWrapper()->SelectClause($pWithColumns, static::TABLE_ALIAS);
    }

    /**
     * Helper -- FromClause
     *
     * returns the FROM clause
     *
     * @return string
     */
    protected function FromClause ()
    {
        return $this->GetServiceWrapper()->FromClause();
    }

    /**
     * Helper -- WhereClause
     *
     * returns a where clause automatically built from the payload object. if
     * something more complicated is needed, just override this method and build
     * the where clause however needed. Just be sure to also update the
     * WhereBind method as well
     *
     * Be mindful if extending this method to include the space at the end of
     * the stmt
     *
     * @return string
     */
    protected function WhereClause ()
    {
        $where_binds = array ();

        $alias = false === static::TABLE_ALIAS ? '' : '`' . static::TABLE_ALIAS . '`.';

        foreach ($this->GetPayloadPrototype()->PropertyList() as $property)
        {
            $where_binds[] = $alias . "`$property` = :$property";
        }

        if (true === empty($where_binds))
        {
            $result = ' ';
        }
        else
        {
            $result = 'WHERE ' . implode(' AND ', $where_binds) . ' ';
        }

        return $result;
    }

    /**
     * Helper -- WhereBind
     *
     * adds the parameter binding to the statement for each property in the
     * payload. currently it doesn't do anything more complicated than spinning
     * through the properties so there is no logic for handling optional
     * parameters in the payload object yet, but i'll add them at some point
     * when they become needed. for now just add them into the derived class and
     * bug me when you need the option
     *
     * @param PDOStatement $pStatement
     */
    protected function WhereBind (PDOStatement $pStatement)
    {
        foreach ($this->GetPayloadPrototype()->PropertyList() as $property)
        {
            $pStatement->bindParam(":$property", $this->GetPayload()
                ->GetProperty($property));
        }
    }

    /**
     * Helper -- OrderByClause
     *
     * This is a helper method for the Execute method. Set mOrderByClause
     * via the SetOrderByClause accessor if needed. You can also overload
     * this method in the child query if it needs to be variable.
     *
     * Keep in mind, if you extend this method, you will likely need to
     * add parameters, etc. If you do that, please do not add the options
     * in the payload object unless you plan on overloading that as well
     * as it uses the properties to build where statements.
     *
     * Be mindful if extending this method to include the space at the end of
     * the stmt
     *
     * @return string
     */
    protected function OrderByClause ()
    {
        return $this->mOrderByClause . ' ';
    }

    /**
     * Helper -- OrderByClause
     *
     * If an OrderBy clause is needed for this statement, use this method to set
     * it. Be mindful that this method does not do *any* validation, use with
     * care and be sure not to use any user data to build it without sanitization
     *
     * @return string
     */
    public function SetOrderByClause ($pClause)
    {
        $this->mOrderByClause = $pClause;
    }

    /**
     * Helper -- LimitClause
     *
     * Limits are enabled by default, if you wish to disable them, simply set
     * the mLimitEnabled property to false in your derived class
     *
     * @return string
     */
    protected function LimitClause ()
    {
        return (true === $this->mLimitEnabled) ? 'LIMIT :offset, :limit' : NULL;
    }

    /**
     * Helper -- ProcessResponse
     *
     * Should process the response from the remote service and parse it
     * into a format that will be returned to the caller
     */
    protected function ProcessResponse ()
    {
        foreach ($this->mServiceResponse as $row)
        {
            $model = $this->GetServiceWrapper()->GetPrototype();
            $model->Import(Model\StandardModelAbstract::FORMAT_ARRAY, $row);

            $this->mResult->attach($model);
        }

        $this->mResult->rewind();
    }
}