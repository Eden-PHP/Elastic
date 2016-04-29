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
     * Bulk actions.
     *
     * @var array
     */
    protected $bulk = array();

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

        // property exists on resource?
        if(property_exists($this->connection, $property)) {
            // call the method
            $this->connection->__call($name, $args);

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
     * Adds bulk action.
     *
     * @param   string
     * @param   *mixed
     * @return  array
     */
    public function addBulkAction($action, $payload)
    {
        // Argument test
        Argument::i()
            ->test(1, 'string')
            ->test(2, 'string', 'int', 'array');

        $this->bulk[] = array($action => $payload);

        return $this;
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

    /**
     * Delete's a document based on id.
     *
     * @param   scalar
     * @param   string | null
     * @return  array
     */
    public function delete($id, $type = null)
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
        // set request method
        ->setMethod(Index::DELETE)
        // send request
        ->send();
    }

    /**
     * Update's a document based
     * on the given id and script.
     *
     * @param   scalar
     * @param   string | null
     * @return  array
     */
    public function update($id, $type = null)
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
        // require body
        ->requireBody()
        // set the id
        ->setId($id)
        // set endpoint
        ->setEndpoint('_update')
        // set request method
        ->setMethod(Index::POST)
        // send request
        ->send();
    }

    /**
     * The simplest usage of _update_by_query 
     * just performs an update on every document 
     * in the index without changing the source.
     *
     * NOTE: This function is experimental as of
     * https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-update-by-query.html
     *
     * @param   string | null
     * @return  array
     */
    public function updateByQuery($type = null)
    {
        // Argument test
        Argument::i()->test(1, 'string', 'null');

        // set request basics
        $connection = $this->connection;

        // is type set?
        if(isset($type)) {
            // set type
            $connection->setType($type);
        }

        return $connection
        // set endpoint
        ->setEndpoint('_update_by_query')
        // set request method
        ->setMethod(Index::POST)
        // send request
        ->send();
    }

    /**
     * Multi GET API allows to get 
     * multiple documents based on 
     * an index, type (optional) and 
     * id (and possibly routing).
     *
     * @param   string | null
     * @param   string | null
     * @return  array
     */
    public function multiGet($index = null, $type = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'string', 'null')
            ->test(2, 'string', 'null');

        // set request basics
        $connection = $this->connection;

        // set index
        if(isset($index)) {
            $this->setIndex($index);
        } else {
            $this->setIndex('');
        }

        // set type
        if(isset($type)) {
            $this->setType($type);
        }

        return $connection
        // require body
        ->requireBody()
        // set endpoint
        ->setEndpoint('_mget')
        // send request
        ->send();
    }

    /**
     * The bulk API makes it possible 
     * to perform many index/delete 
     * operations in a single API call. 
     * This can greatly increase the 
     * indexing speed.
     *
     * @param   array | null
     * @return  array
     */
    public function bulk($body = null)
    {
        // Argument test
        Argument::i()->test(1, 'string', 'null');

        // if body is set
        if(isset($body)) {
            // iterate on each body
            foreach($body as $payload) {
                $this->bulk[] = $payload;
            }
        }

        return $this->connection
        // require body
        ->requireBody()
        // set index to none
        ->setIndex('')
        // set body
        ->setBody($this->bulk)
        // set endpoint
        ->setEndpoint('_bulk')
        // set method to post
        ->setMethod(Index::POST)
        // set binary
        ->setBinary(true)
        // send request
        ->send();
    }

    /**
     * The most basic form of _reindex 
     * just copies documents from one 
     * index to another.
     *
     * @return array
     */
    public function reindex()
    {
        return $this->connection
        // require body
        ->requireBody()
        // set index to non
        ->setIndex('')
        // set endpoint
        ->setEndpoint('_reindex')
        // set method
        ->setMethod(Index::POST)
        // send request
        ->send();
    }

    /**
     * Returns information and statistics 
     * on terms in the fields of a particular 
     * document.
     *
     * @param   scalar | null
     * @param   string | null
     * @return  array
     */
    public function termVectors($id = null, $type = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'scalar', 'null')
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
        // set id
        ->setId($id)
        // set endpoint
        ->setEndpoint('_termvectors')
        // send request
        ->send();
    }

    /**
     * Multi termvectors API allows 
     * to get multiple termvectors at 
     * once. The documents from which 
     * to retrieve the term vectors are 
     * specified by an index, type and id.
     *
     * @param   string | null
     * @return  array
     */
    public function multiTermVectors($type = null)
    {
        // Argument test
        Argument::i()->test(1, 'string', 'null');

        // set request basics
        $connection = $this->connection;

        // is type set?
        if(isset($type)) {
            // set type
            $connection->setType($type);
        }

        return $connection
        // set endpoint
        ->setEndpoint('_mtermvectors')
        // send request
        ->send();
    }
}