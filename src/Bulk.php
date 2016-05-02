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
 * Bulk Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Bulk extends Base
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
    }

    /**
     * Add's a bulk action.
     *
     * @param   string
     * @param   *mixed
     * @return  $this
     */
    public function addBulk($action = null, $value = null)
    {
        // Argument test
        Argument::i()->test(1, 'string', 'array', 'null');

        // if we only have one argument
        if(func_num_args() == 1) {
            // push that argument
            $this->bulk[] = $action;

            return $this;
        }

        // push the argument with key value
        $this->bulk[] = array($action => $value);

        return $this;
    }

    /**
     * Set multiple bulk actions.
     *
     * @param   array
     * @return  $this
     */
    public function setBulk($data = array())
    {
        // Argument test
        Argument::i()->test(1, 'array');

        // merge original to data
        $this->bulk = array_merge($this->bulk, $data);

        return $this;
    }

    /**
     * Sends bulk request to elastic api.
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
        $endpoint = '_bulk';

        // if index is only set
        if(func_num_args() == 1) {
            // replace index and type
            $connection->setIndex($index)->setType(null);
        } else if(func_num_args() == 2) {
            // set index and type
            $connection->setIndex($index)->setType($type);
        }

        // set the request body
        $body = $this->bulk;

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
        // set method post
        ->setMethod(Index::POST)
        // set the request body
        ->setBody($body)
        // send request
        ->send();
    }
}