<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Eden\Elastic;

/**
 * Search Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Search extends Base
{
    /**
     * Default query builder.
     *
     * @var Eden\Elastic\Query
     */
    protected $builder = null;

    /**
     * Default connection resource.
     *
     * @var Eden\Elastic\Index
     */
    protected $connection = null;

    /**
     * Set default connection resource.
     *
     * @param   Eden\Elastic\Index
     */
    public function __construct(Index $connection)
    {
        // Argument test
        Argument::i()->test(1, '\\Eden\\Elastic\\Index');

        // set connection
        $this->connection = $connection;

        // set default query builder
        $this->builder    = Query::i();
    }

    /**
     * Property setter and getter.
     *
     * @param   string
     * @param   array
     * @return  Resource
     */
    public function __call($name, $args)
    {
        // get, set, add
        $property = lcfirst(substr($name, 3));

        // property exists on resource?
        if(property_exists($this->connection, $property)) {
            // call the method
            $this->connection->__call($name, $args);

            return $this;
        }
    }

    /**
     * Get results based on the query
     * or query body given.
     *
     * @param   string | null
     * @return  array
     */
    public function getResults($type = null)
    {
        // Argument test
        Argument::i()->test(1, 'string', 'null');

        // get the connection
        $connection = $this->connection;

        // if type is set
        if(isset($type)) {
            // set type
            $connection->setType($type);
        }

        // send request
        return $connection
        // require index
        ->requireIndex()
        // require type
        ->requireType()
        // send endpoint
        ->setEndpoint('_search')
        // send request
        ->send();
    }
}
