<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Eden\Elastic\Search;

use Eden\Elastic\Argument as Argument;
use Eden\Elastic\Exception as Exception;
use Eden\Elastic\Index as Index;
use Eden\Elastic\Search as Search;

/**
 * Search Scroll Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Scroll extends Search
{
    /**
     * Set default connection resource.
     *
     * @param   Eden\Elastic\Index
     */
    public function __construct(Index $connection)
    {
        // call parent construct
        return parent::__construct($connection);
    }

    /**
     * Create searc scroll context.
     *
     * @param   string
     * @param   string | null
     * @return  array
     */
    public function createScroll($scroll, $type = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'string')
            ->test(2, 'string', 'null');

        // get the connection
        $connection = $this->connection;

        // if type is set
        if(isset($type)) {
            // set type
            $connection->setType($type);
        }

        // get the body
        $connection->setBody($this->builder->getQuery());

        return $connection
        // require index
        ->requireIndex()
        // require type
        ->requireType()
        // requir body
        ->requireBody()
        // add param
        ->addParam('scroll', $scroll)
        // set endpoint
        ->setEndpoint('_search')
        // send request
        ->send();
    }

    /**
     * Get scroll context.
     *
     * @param   string
     * @param   string
     * @return  array
     */
    public function getScroll($time, $id)
    {
        // Argument test
        Argument::i()
            ->test(1, 'string')
            ->test(2, 'string');

        $connection = $this->connection;

        // add scroll
        $this->setScroll($time)
        // set scroll id
        ->setScrollId($id);

        // set request body
        $connection->setBody($this->builder->getQuery());
    
        // get original index
        $index = $connection->getIndex();

        // reset index
        $connection->setIndex('');

        // send request
        $response = $connection
        // set endpoint
        ->setEndpoint('_search/scroll')
        // send request
        ->send();

        // bring back index
        $connection->setIndex($index);

        return $response;
    }

    /**
     * Delete a scroll context.
     *
     * @param   string | array
     * @return  array
     */
    public function deleteScroll($ids)
    {
        // Argument test
        Argument::i()->test(1, 'string', 'array');

        // set connection
        $connection = $this->connection;

        // if id is string
        if(is_string($ids)) {
            $this->setScrollId(array($ids));
        } else if(is_array($ids)) {
            $this->setScrollId($ids);
        }

        // set request body
        $connection->setBody($this->builder->getQuery());
    
        // get original index
        $index = $connection->getIndex();

        // reset index
        $connection->setIndex('');

        // send request
        $response = $connection
        // set endpoint
        ->setEndpoint('_search/scroll')
        // set method to delete
        ->setMethod(Index::DELETE)
        // send request
        ->send();

        // bring back index
        $connection->setIndex($index);

        return $response;
    }

    /**
     * Delete all scroll context.
     *
     * @return array
     */
    public function deleteScrolls()
    {
        // set connection
        $connection = $this->connection;

        // get original index
        $index = $connection->getIndex();

        // reset index
        $connection->setIndex('');

        // send request
        $response = $connection
        // set endpoint
        ->setEndpoint('_search/scroll/_all')
        // set method to delete
        ->setMethod(Index::DELETE)
        // send request
        ->send();

        // bring back index
        $connection->setIndex($index);

        return $response;
    }
}
