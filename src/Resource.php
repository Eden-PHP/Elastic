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
 * Resource Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
abstract class Resource extends Base
{
    /**
     * GET Method.
     *
     * @const string
     */
    const GET = 'GET';

    /**
     * POST Method.
     *
     * @const string
     */
    const POST = 'POST';

    /**
     * PUT Method.
     *
     * @const string
     */
    const PUT = 'PUT';

    /**
     * DELETE Method.
     *
     * @const string
     */
    const DELETE = 'DELETE';

    /**
     * Error sending request.
     *
     * @const string
     */
    const REQUEST_ERROR = 'An error occured while sending request.';

    /**
     * Elastic api host.
     *
     * @var string
     */
    protected $host = 'http://localhost:9200';

    /**
     * Elastic api index.
     *
     * @var string
     */
    protected $index = null;

    /**
     * Custom headers.
     *
     * @var array
     */
    protected $headers = array();

    /**
     * Custom request query.
     *
     * @var array
     */
    protected $query = array();

    /**
     * Builds out connection information.
     *
     * @param   string
     * @param   string
     * @return  Eden\Elastic\Resource
     */
    public function __construct($host = 'http://localhost:9200', $index = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'string')
            ->test(2, 'string', 'null');

        // set elastic api host
        $this->host  = $host;
        // set elastic api index
        $this->index = $index;

        // initialize our resource
        $this->resource = \Eden\Curl\Index::i();
    }

    /**
     * Abstract connect method signature.
     *
     * @return  Eden\Elastic\Resource
     */
    abstract public function connect();

    /**
     * Set elastic api host.
     *
     * @param   string
     * @return  Eden\Elastic\Resource
     */
    public function setHost($host)
    {
        // Argument test
        Argument::i()->test(1, 'string');
    
        // set elastic api host
        $this->host = $host;

        return $this;
    }

    /**
     * Set elastic api index.
     *
     * @param   string
     * @return  Eden\Elastic\Resource
     */
    public function setIndex($index)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // set elastic api index
        $this->index = $index;

        return $this;
    }

    /** 
     * Set custom list of headers.
     *
     * @param   array
     * @return  Eden\Elastic\Resource
     */
    public function setHeaders($headers = array())
    {
        // if headers set is empty
        if(empty($headers)) {
            // set empty headers
            $this->headers = array();

            return $this;
        }

        // iterate on each headers
        foreach($headers as $key => $value) {
            // set header
            $this->headers[$key] = $value;
        }

        return $this;
    }

    /**
     * Add custom header.
     *
     * @param   string
     * @param   *midex
     * @return  Eden\Elastic\Resource
     */
    public function addHeader($key, $value)
    {
        // set header
        $this->header[$key] = $value;

        return $this;
    }

    /**
     * Set custom request queries.
     *
     * @param   array
     * @return  Eden\Elastic\Resource
     */
    public function setQuery($query = array())
    {
        // if query is empty
        if(empty($query)) {
            // set empty query
            $this->query = array();
        }

        // iterate on each query
        foreach($query as $key => $value) {
            // set query
            $this->query[$key] = $value;
        }

        return $this;
    }

    /**
     * Add custom request query.
     *
     * @param   string
     * @param   *mixed
     * @return  Eden\Elastic\Resource
     */
    public function addQuery($key, $value)
    {
        // add custom query
        $this->query[$key] = $value;

        return $this;
    }

    /**
     * Sets basic authorization (TODO).
     *
     * @param   string
     * @param   string
     * @return  Eden\Elastic\Resource
     */
    public function setBasicAuthorization($username, $password) 
    {
        // Argument test
        Argument::i()
            ->test(1, 'string')
            ->test(2, 'string');

        // :TODO

        return $this;
    }

    /**
     * Basic request method.
     *
     * @param   string
     * @param   string
     * @param   array
     * @param   array
     * @param   array
     * @return  array
     */
    public function request(
        $method, 
        $endpoint, 
        $data = array(), 
        $query = array(), 
        $headers = array())
    {
        // Argument test
        Argument::i()
            ->test(1, 'string')
            ->test(2, 'string')
            ->test(3, 'array')
            ->test(4, 'array')
            ->test(5, 'array');

        // initialize request
        $request = $this->resource;

        // set endpoint
        $url = $this->host . '/' . $endpoint;

        // set query
        if(!empty($query)) {
            // set query
            $this->setQuery($query);
        }

        // set headers
        if(!empty($headers)) {
            // set headers
            $this->setHeaders($headers);
        }

        // query set?
        if(!empty($this->query)) {
            // set request query
            $url = $url . '?' . http_build_query($this->query);
        }

        // set the url
        $request->setUrl($url);

        // check the method
        switch ($method) {
            // GET method?
            case self::GET:
                // set http get
                $request->setHttpGet(true);

                break;
            // POST method?
            case self::POST:
                // set http post
                $request->setPost(true)
                // set post fields
                ->setPostFields(json_encode($data));

                // set headers
                $this->addHeader('Content-Type', 'application/json');

                break;
            // PUT method?
            case self::PUT:
                // set custom request
                $request->setCustomRequest(self::PUT)
                // set post fields
                ->setPostFields(json_encode($data));

                // set headers
                $this->addHeader('Content-Type', 'application/json');

                break;
            // DELETE method?
            case self::DELETE:
                // set custom request
                $request->setCustomRequest(self::DELETE)
                // set header
                ->setHeaders('Content-Type', 'application/json');
                
                break;
            default:
                // set http get
                $request->setHttpGet(true);

                break;
        }

        // let's set the headers
        foreach($this->headers as $key => $value) {
            $request->setHeaders($key, $value);
        }

        try {
            // trigger request
            $response = $request->setFollowLocation(true)
            // set connection timeout
            ->setConnectTimeout(60)
            // set max redirects
            ->setMaxRedirs(5)
            // return repsonse instead of printing
            ->setReturnTransfer(true)
            // get the response
            ->getJsonResponse();
        } catch(\Exception $e) {
            // throw an error
            return Exception::i(self::REQUEST_ERROR)->trigger();
        }

        // do we have an error?
        if(isset($response['error'])) {
            // build out the message
            $message = $response['error']['type'] . ': ' . 
                       $response['error']['reason'] . ', status: ' . 
                       $response['status'];

            // throw it!
            return Exception::i($message)->trigger(); 
        }

        return isset($response) || !empty($response) ? $response : array();
    }
}