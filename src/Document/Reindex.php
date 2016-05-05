<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Eden\Elastic\Document;

use Eden\Elastic\Argument as Argument;
use Eden\Elastic\Exception as Exception;
use Eden\Elastic\Index as Index;
use Eden\Elastic\Document as Document;
use Eden\Elastic\Query as Query;

/**
 * Document Reindex Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Reindex extends Document
{
    /**
     * Query builder class.
     *
     * @var Eden\Elastic\Query
     */
    protected $builder = null;

    /**
     * Set default connection resource.
     *
     * @param   Eden\Elastic\Index
     */
    public function __construct(Index $connection)
    {
        // initialize query builder
        $this->builder = Query::i();

        // call parent construct
        return parent::__construct($connection);
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

        // are we going to set?
        if(strpos($name, 'set') === 0) {
            // transform to query key
            $key = \Eden_String_Index::i($name)
                ->substr(3)
                ->preg_replace("/([A-Z0-9])/", '.'."$1")
                ->substr(strlen('.'))
                ->strtolower()
                ->get();
            
            // if arg isn't set
            if (!isset($args[0])) {
                // default is null
                $args[0] = null;
            }

            // if we have two arguments
            if(count($args) == 2 && isset($args[0])) {
                // set the key
                $key = $key . '.' . $args[0];
                // set the value
                $val = isset($args[1]) ? $args[1] : null;

                // add tree to builder
                $this->builder->setTree($key, $val);
            } else {
                // set the value
                $val = isset($args[0]) ? $args[0] : null;

                // add tree to builder
                $this->builder->setTree($key, $val);
            }

            return $this;
        }
    }

    /**
     * Send reindex request.
     *
     * @param   array | null
     * @return  array
     */
    public function save($data = array())
    {
        // Argument test
        Argument::i()->test(1, 'array', 'null');

        // get the current connection
        $connection = $this->connection;

        // set the current query body
        $current = $this->builder->getQuery();

        // if data is set
        if(!empty($data)) {
            // if current is null
            if(!is_null($current) && is_array($current)) {
                $data = array_merge($data, $current);
            }
        } else if(!is_null($current)) {
            $data = $current;
        }

        // get current index
        $index = $connection->getIndex();
        // get current type
        $type  = $connection->getType();

        // clear index and type
        $connection->setIndex('')->setType('');

        $response = $connection
        // require body
        ->requireBody()
        // set request method
        ->setMethod(Index::POST)
        // set body
        ->setBody($data)
        // set endpoint
        ->setEndpoint('_reindex')
        // send request
        ->send();

        // set original index and type
        $connection->setIndex($index)->setType($type);

        return $response;
    }
}