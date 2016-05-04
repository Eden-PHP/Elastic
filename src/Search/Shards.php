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
 * Search Shards Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Shards extends Search
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
     * Get shards of the given index.
     *
     * @param   string | null
     * @return  array
     */
    public function getShards($index = null)
    {
        // Argument test
        Argument::i()->test(1, 'string', 'null');

        // set endpoint
        $endpoint = '_search_shards';

        // get original index
        $index = $this->connection->getIndex();
        // get original type
        $type  = $this->connection->getType();

        // remove index and type
        $this->connection->setIndex($index)->setType('');

        // get response
        $response = $this->connection
        // require index
        ->requireIndex()
        // set endpoint
        ->setEndpoint($endpoint)
        // send request
        ->send();

        // bring back the index and type
        $this->connection->setIndex($index)->setType($type);

        return $response;
    }
}