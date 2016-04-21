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

    public function debug($message)
    {
        return \Eden\Core\Inspect::i()->inspect($message);
    }
}
