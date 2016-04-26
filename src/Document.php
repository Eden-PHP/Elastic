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
 * Document API Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Document extends Base
{
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

        // from param to query
        if($property == 'param') {
            $property = $query;
        }

        // property exists on resource?
        if(property_exists($this->connection, $property)) {
            // call the method
            $this->connection->__call($name, $args);

            return $this;
        }


        // if property does not exists
        if(!property_exists($this, $property)) {
            return $this;
        }

        // are we going to set?
        if(strpos($name, 'set') === 0) {
            $this->$property = isset($args[0]) ? $args[0] : null;
            
            return $this;
        }

        // are we going to add something?
        if(strpos($name, 'add') === 0) {
            // does property exists and is an array?
            if(is_array($this->$property)) {
                // get the key
                $key = isset($args[0]) ? $args[0] : null;
                // get the value
                $val = isset($args[1]) ? $args[1] : null;

                // does the key set?
                if(is_null($key)) {
                    return $this;
                }

                // set the property
                $this->$property[$key] = $val;
            }

            return $this;
        }
    }

    /**
     * Index a data to elastic api.
     *
     * @param   string | null
     * @param   bool
     * @return  array
     */
    public function index($type = null, $auto = false)
    {
        // Argument test
        Argument::i()
            ->test(1, 'string', 'null')
            ->test(2, 'bool');

        // set request basics
        $connection = $this->connection;

        // is type set?
        if(isset($type)) {
            // set type
            $connection->setType($type);
        }

        // auto create index?
        if($auto) {
            // set method to post
            $connection->setMethod(Index::POST);
        } else {
            // set method to put
            $connection->setMethod(Index::PUT);
        }

        return $connection
        // index is required
        ->requireIndex()
        // type is required
        ->requireType()
        // body is required
        ->requireBody()
        // send request
        ->send();
    }

    /**
     * Get a document based on id.
     *
     * @param   scalar
     * @param   string | null
     * @return  array
     */
    public function get($id, $type = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'scalar')
            ->test(2, 'string', 'null');

        // set request basics
        $connection = $this->connection;

        // is type set?
        if(isset($type)) {
            // set type
            $connection->setType($type);
        }

        return $connection
        // require index
        ->requireIndex()
        // require type
        ->requireType()
        // require id
        ->requireId()
        // set the id
        ->setId($id)
        // send request
        ->send();
    }
}