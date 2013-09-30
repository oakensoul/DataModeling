<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace DataModeling\DataAccess\ServiceWrapper\PDO;

/* Use statements for Framework namespaces */
use DataModeling\DataAccess\Interrupt;

/* Use statements for core php namespaces */
use SplDoublyLinkedList;
use PDO;
use PDOStatement;

abstract class StoredProcedureAbstract extends QueryAbstract
{

    /**
     * Accessor -- GetProcedureName
     *
     * Returns the stored procedure name for this query
     *
     * @return string
     */
    abstract public function GetProcedureName ();

    /**
     * Utility -- Execute
     *
     * executes the service call, logging, monitoring, etc for the service
     */
    public function Execute ()
    {
        $procedure = $this->GetProcedureName();
        $parameters = $this->GetParameterList();

        $sql = "CALL $procedure($parameters)";

        $stmt = $this->GetServiceWrapper()
            ->GetServiceObject()
            ->prepare($sql);

        $this->WhereBind($stmt);

        if (true === $this->mLimitEnabled)
        {
            $stmt->bindParam(':limit', $this->mLimit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $this->mOffset, PDO::PARAM_INT);
        }

        $stmt->execute();

        $this->mServiceResponse = $stmt->fetchAll();

        $this->mResult = $this->GetResultPrototype();

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
     * Helper -- GetParameterList
     *
     * returns a string of parameters to be bound into the stored procedure
     *
     * @return string
     */
    protected function GetParameterList ()
    {
        $params = array ();

        foreach ($this->GetPayloadPrototype()->PropertyList() as $property)
        {
            $params[] = ":$property";
        }

        if (true == $this->mLimitEnabled)
        {
            $params[] = ":limit";
            $params[] = ":offset";
        }

        $result = implode(', ', $params);

        return $result;
    }
}