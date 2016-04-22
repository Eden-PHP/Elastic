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
     * Index not set error.
     *
     * @const string
     */
    const INDEX_NOT_SET = 'Document index is not set.';

    /**
     * Index type not set error.
     *
     * @const string
     */
    const INDEX_TYPE_NOT_SET = 'Document index type is not set.';

    /**
     * Document data is not set.
     *
     * @const string
     */
    const DATA_NOT_SET = 'Document data is not set.';

    /**
     * Document id is not set.
     *
     * @const string
     */
    const ID_NOT_SET = 'Document id is not set.';

    /**
     * Default connection.
     *
     * @var Eden\Elastic\Index
     */
    protected $connection = null;

    /**
     * Document data.
     *
     * @var array
     */
    protected $data = null;

    /**
     * Document index
     *
     * @var string
     */
    protected $index = null;

    /**
     * Document type.
     *
     * @var string
     */
    protected $type = null;

    /**
     * Document options.
     *
     * @var array
     */
    protected $options = array();

    /**
     * Request endpoint.
     *
     * @var string
     */
    protected $endpoint = null;

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
     * Set document data.
     *
     * @param   array
     * @return  Eden\Elastic\Document
     */
    public function setData($data = array())
    {
        // Argument test
        Argument::i()->test(1, 'array');

        // set document data
        $this->data = $data;

        return $this;
    }

    /**
     * Set index type.
     *
     * @param   string
     * @return  Eden\Elastic\Document
     */
    public function setType($type)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // set index type
        $this->type = $type;

        return $this;
    }

    /**
     * Set document index.
     *
     * @param   string
     * @return  Eden\Elastic\Document
     */
    public function setIndex($index)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // set document index
        $this->index = $index;

        return $this;
    }

    /**
     * Set request endpoint.
     *
     * @param   string
     * @return  Eden\Elastic\Document
     */
    public function setEndpoint($endpoint)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // set endpoint
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Set request options.
     *
     * @param   array
     * @return  Eden\Elastic\Document
     */
    public function setOptions($options = array())
    {
        // Argument test
        Argument::i()->test(1, 'array');

        // options set?
        if(empty($options)) {
            $this->options = array();

            return $this;
        }

        // iterate on each options
        foreach($options as $key => $value) {
            $this->options[$key] = $value;
        }

        return $this;
    }

    /**
     * Add request option.
     *
     * @param   string
     * @param   string
     * @return  Eden\Elastic\Document
     */
    public function addOption($key, $value)
    {
        // Argument test
        Argument::i()
            ->test(1, 'string')
            ->test(2, 'scalar');

        // set request option
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Index a document to elasitc.
     *
     * @param   string | null
     * @param   array
     * @return  array
     */
    public function index($type = null, $options = array())
    {
        // Argument test
        Argument::i()
            ->test(1, 'string', 'null')
            ->test(2, 'array');

        // get connection information
        $elastic = $this->connection->getResource();

        // if index is not set
        if(empty($elastic['index'])) {
            // throw exception
            return Exception::i(self::INDEX_NOT_SET)->trigger();
        }

        // if index type is not set
        if(!isset($this->type) && !isset($type)) {
            // throw exception
            return Exception::i(self::INDEX_TYPE_NOT_SET)->trigger();
        }

        // is data set?
        if(empty($this->data)) {
            // throw exception
            return Exception::i(self::DATA_NOT_SET)->trigger();
        }

        // is id set?
        if(!isset($this->data['_id'])) {
            // throw exception
            return Exception::i(self::ID_NOT_SET)->trigger();
        }

        // if type arg is set
        if(isset($type)) {
            // set document index type
            $this->setType($type);
        }

        // if options is set
        if(!empty($options)) {
            // set document options
            $this->setOptions($options);
        }

        // let's formulate the endpoint
        $endpoint = $this->type . '/' . $this->data['_id'];

        // unset the id
        unset($this->data['_id']);

        // do we have tail endpoint?
        if(isset($this->endpoint)) {
            // set tail endpoint
            $endpoint = $endpoint . '/' . $this->endpoint;
        }

        // try request
        try {
            // send put request
            $response = $this->connection
            ->request(Index::PUT, $endpoint, $this->data, $this->options);
        } catch(\Exception $e) {
            // throw an exception
            return Exception::i($e->getMessage())->trigger();
        }

        return $response;
    }

    /**
     * Get a single document.
     *
     * @param   int | string
     * @param   string | null
     * @param   bool | array
     * @param   array
     * @return  array
     */
    public function get($id, $type = null, $test = false, $options = array())
    {
        // Argument test
        Argument::i()
            ->test(1, 'int', 'string')
            ->test(2, 'string', 'null')
            ->test(3, 'bool', 'array')
            ->test(4, 'array');

        // if id is not set
        if(!isset($id)) {
            // throw an exception
            return Exception::i(self::ID_NOT_SET)->trigger();
        }

        // if index type is not set
        if(!isset($this->type) && !isset($type)) {
            // throw exception
            return Exception::i(self::INDEX_TYPE_NOT_SET)->trigger();
        }

        // if type arg is set
        if(isset($type)) {
            // set document index type
            $this->setType($type);
        }

        // check arguments
        if(is_array($test)) {
            // set options
            $options = $test;
            // set test
            $test    = false;
        }

        // if options is not empty
        if(!empty($options)) {
            // set options
            $this->setOptions($options);
        }

        // let's formulate the endpoint
        $endpoint = $this->type . '/' . $id;

        // do we have tail endpoint?
        if(isset($this->endpoint)) {
            // set tail endpoint
            $endpoint = $endpoint . '/' . $this->endpoint;
        }

        // try request
        try {
            // set method
            $method = Index::GET;

            // are we going to test?
            if($test) {
                // set method
                $method = Index::HEAD;
            }

            $response = $this->connection
            // set query
            ->setQuery($this->options)
            // send up request
            ->request($method, $endpoint);
        } catch(\Exception $e) {
            // throw exceptiion
            return Exception::i($e->getMessage())->trigger();
        }

        return $response;
    }

    /**
     * Deletes a single record.
     *
     * @param   int | string
     * @param   string | null
     * @param   array
     * @return  array
     */
    public function delete($id, $type = null, $options = array())
    {
        // Argument test
        Argument::i()
            ->test(1, 'int', 'string')
            ->test(2, 'string', 'null')
            ->test(3, 'array');

        // if id is not set
        if(!isset($id)) {
            // throw an exception
            return Exception::i(self::ID_NOT_SET)->trigger();
        }

        // if index type is not set
        if(!isset($this->type) && !isset($type)) {
            // throw exception
            return Exception::i(self::INDEX_TYPE_NOT_SET)->trigger();
        }

        // if type arg is set
        if(isset($type)) {
            // set document index type
            $this->setType($type);
        }

        // if options is not empty
        if(!empty($options)) {
            // set options
            $this->setOptions($options);
        }

        // let's formulate the endpoint
        $endpoint = $this->type . '/' . $id;

        // do we have tail endpoint?
        if(isset($this->endpoint)) {
            // set tail endpoint
            $endpoint = $endpoint . '/' . $this->endpoint;
        }

        // try request
        try {
            $response = $this->connection
            // set query
            ->setQuery($this->options)
            // send up request
            ->request(Index::DELETE, $endpoint);
        } catch(\Exception $e) {
            // throw exceptiion
            return Exception::i($e->getMessage())->trigger();
        }

        return $response;
    }
}