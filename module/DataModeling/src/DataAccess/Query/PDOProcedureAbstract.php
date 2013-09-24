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
use DataModeling\DataAccess\Interrupt;

/* Use statements for core php namespaces */
use SplObjectStorage;
use PDO;
use PDOStatement;

abstract class PDOProcedureAbstract extends PDOQueryAbstract
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

        $this->ParameterBind($stmt);

        if (true === $this->mLimitEnabled)
        {
            $stmt->bindParam(':limit', $this->mLimit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $this->mOffset, PDO::PARAM_INT);
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

    /**
     * Helper -- ParameterBind
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
    protected function ParameterBind (PDOStatement $pStatement)
    {
        foreach ($this->GetPayloadPrototype()->PropertyList() as $property)
        {
            $pStatement->bindParam(":$property", $this->GetPayload()
                ->GetProperty($property));
        }
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
            $model->Hydrate($row);

            $this->mResult->attach($model);
        }

        $this->mResult->rewind();
    }
}