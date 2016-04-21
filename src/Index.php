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
 * Index Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Index extends Resource
{
    /**
     * Unable to connect error.
     *
     * @const string
     */
    const UNABLE_TO_CONNECT = 'Unable to connect to host: %s';

    /**
     * Connect to elastic api.
     *
     * @return  Eden\Elastic\Index
     */
    public function connect()
    {
        // initialize resource
        $this->resource = \Eden\Curl\Index::i();

        try {
            // let's test the connection
            $response = $this->resource
            // set the resource url
            ->setUrl($this->url) 
            // get json response
            ->getJsonResponse();
        } catch(\Exception $e) {
            // throw up an exception
            return Exception::i($e->getMessage())->trigger();
        }

        // do we have a response?
        if(empty($response)) {
            // throw up an exception
            return Exception::i(sprintf(self::UNABLE_TO_CONNECT, $this->url))->trigger();
        }

        // set the connection information
        $this->elastic = $response;

        return $this;
    }

    /**
     * Index a single record.
     *
     * @param   string
     * @param   string
     * @param   array
     * @param   array
     * @return  array
     */
    public function index($document, $type, $data, $options = array())
    {
        // build up the url
        $url = $this->url . '/' . $document . '/' . $type;

        // if document is not set
        if(!isset($document)) {
            return Exception::i('Document name is required.')->trigger();
        }

        // if type is not set
        if(!isset($type)) {
            return Exception::i('Document type is required.')->trigger();
        }

        // does data empty?
        if(empty($data)) {
            return Exception::i('Data is required.')->trigger();
        }

        // does index id set?
        if(!isset($data['_id'])) {
            return Exception::i('Index id is required.')->trigger();
        }

        // set that id unto our url
        $url = $url . '/' . $data['_id'];

        // unset the id from the data
        unset($data['_id']);
        
        // do we have options?
        if(!empty($options)) {
            // set the options
            $this->setOptions($options);
        }

        // set the document
        $this->document = $document;

        // set the type
        $this->type = $type;

        // set the url
        $this->url = $url;

        // let's send this up
        return $this->request('PUT', $data, array(), array());
    }

    /**
     * Debug purposes.
     *
     * @param   string
     * @return  Eden\Core\Inspect
     */
    public function debug($message)
    {
        return \Eden\Core\Inspect::i()->inspect($message);
    }
}
