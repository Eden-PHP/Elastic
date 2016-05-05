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
     * Send's up an update by query api call.
     *
     * @param   string | null
     * @param   string | null
     * @return  array
     */
    public function updateByQuery($index = null, $type = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'string', 'null')
            ->test(2, 'string', 'null');

        // get current connection
        $connection = $this->connection;

        // if both are set
        if(func_num_args() == 2) {
            // set index and set type
            $connection->setIndex($index)->setType($type);
        } else if(func_num_args() == 1) {
            // set type
            $connection->setType($index);
        }

        return $connection
        // require index
        ->requireIndex()
        // set method to post
        ->setMethod(Index::POST)
        // set endpoint
        ->setEndpoint('_update_by_query')
        // send request
        ->send();
    }

    /**
     * Send's up a multi-get api request.
     *
     * @param   array | null
     * @param   string | null
     * @param   string | null
     * @return  array
     */
    public function multiGet($data = array(), $index = null, $type = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'array', 'null')
            ->test(2, 'string', 'null')
            ->test(3, 'string', 'null');

        // get current connection
        $connection = $this->connection;

        // if we have all arguments
        if(func_num_args() == 3) {
            $connection
            // set index
            ->setIndex($index)
            // set type
            ->setType($type)
            // set body
            ->setBody($data);
        } else if(func_num_args() == 2) {
            $connection
            // set index
            ->setIndex($index)
            // set body
            ->setBody($data);
        } else {
            // get original index
            $index = $connection->getIndex();
            // get original type
            $type  = $connection->getType();

            // send the request
            $response = $connection
            // require body
            ->requireBody()
            // set the body
            ->setBody($data)
            // send request
            ->send();

            // set original index and type
            $connection->setIndex($index)->setType($type);

            return $response;
        }

        return $connection
        // require body
        ->requireBody()
        // send request
        ->send();
    }

    /**
     * Returns the Document Reindex Class.
     *
     * @return  Eden\Elastic\Document\Reindex
     */
    public function reindex()
    {
        return Document\Reindex::i($this->connection);
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