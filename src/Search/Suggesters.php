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
 * Search Suggesters Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Suggesters extends Search
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
     * Save suggestion data.
     *
     * @param   array
     * @param   string | null
     * @return  array
     */
    public function suggest($data = array(), $index = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'array')
            ->test(2, 'string', 'null');

        // get the connection
        $connection = $this->connection;

        // if index is set
        if(isset($index)) {
            $connection->setIndex($index);
        } else {
            // get the current index
            $index = $connection->getIndex();

            $connection->setIndex('');
        }

        // get the current data
        $current = $this->builder->getQuery();

        // current not null?
        if(!is_null($current)) {
            // merge current and given array
            $current = array_merge($current, $data);
        } else {
            // set the data
            $current = $data;
        }

        // send request
        $response = $connection
        // require body
        ->requireBody()
        // set method
        ->setMethod(Index::POST)
        // set the body
        ->setBody($current)
        // set endpoint
        ->setEndpoint('_suggest')
        // send request
        ->send();

        // if index is set
        if(isset($index)) {
            $connection->setIndex($index);
        }

        return $response;
    }
}