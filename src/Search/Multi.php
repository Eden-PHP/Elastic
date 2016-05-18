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
 * Multi Search Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Multi extends Search
{
    /**
     * Set of actions and headers.
     *
     * @var string
     */
    protected $actions = array();

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
     * Add's an action or header.
     *
     * @param   string
     * @param   *mixed
     * @return  $this
     */
    public function add($action = null, $value = null)
    {
        // Argument test
        Argument::i()->test(1, 'string', 'array', 'null');

        // if we only have one argument
        if(func_num_args() == 1) {
            // push that argument
            $this->actions[] = $action;

            return $this;
        }

        // push the argument with key value
        $this->actions[] = array($action => $value);

        return $this;
    }

    /**
     * Set multiple actions.
     *
     * @param   array
     * @return  $this
     */
    public function set($data = array())
    {
        // Argument test
        Argument::i()->test(1, 'array');

        // merge original to data
        $this->actions = array_merge($this->actions, $data);

        return $this;
    }

    /**
     * Sends multi search request to elastic api.
     *
     * @param   string | null
     * @param   string | null
     * @return  array
     */
    public function send($index = null, $type = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'string', 'null')
            ->test(2, 'string', 'null');

        $connection = $this->connection;

        // set endpoint
        $endpoint = '_msearch';

        // if index is only set
        if(func_num_args() == 1) {
            // replace index and type
            $connection->setIndex($index)->setType(null);
        } else if(func_num_args() == 2) {
            // set index and type
            $connection->setIndex($index)->setType($type);
        }

        // set the request body
        $body = $this->actions;

        // merge body with actions
        if(!is_null($connection->getBody())) {
            // merge current body with actions
            $body = array_merge($body, $connection->getBody());
        }

        return $connection
        // require body
        ->requireBody()
        // set endpoint
        ->setEndpoint($endpoint)
        // set binary request flag
        ->setBinary(true)
        // set the request body
        ->setBody($body)
        // send request
        ->send();
    }
}
