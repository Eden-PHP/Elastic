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
     * Failed request error.
     *
     * @const string
     */
    const FAILED_REQUEST = 'An error occured while sending request.';

    /**
     * Elastic api host.
     *
     * @var string
     */
    protected $host = 'localhost';

    /**
     * Elastic api port.
     *
     * @var int
     */
    protected $port = 9200;

    /**
     * Elastic api http protocol.
     *
     * @var bool
     */
    protected $secure = false;

    /**
     * Current connection url.
     *
     * @param string
     */
    protected $url = null;

    /**
     * Sets the post data.
     *
     * @var array
     */
    protected $data = array();

    /**
     * Default document that
     * we are going to use.
     *
     * @var string
     */
    protected $document = null;

    /**
     * Document type that we
     * are going to use.
     *
     * @var string
     */
    protected $type = null;

    /**
     * Set query options.
     *
     * @var array
     */
    protected $options = array();

    /**
     * Optional request headers.
     *
     * @var array
     */
    protected $headers = array();

    /**
     * Connection resource.
     *
     * @var Eden\Curl\Index
     */
    protected $resource = null;

    /**
     * Initialize elastic resource.
     *
     * @param   string
     * @param   int
     * @param   bool
     * @param   array
     * @return  Eden\Elastic\Index
     */
    public function __construct(
        $host = 'localhost',
        $port = 9200,
        $secure = false,
        $options = array())
    {
        // set the host
        $this->host    = $host;
        // set the port
        $this->port    = $port;
        // set the secure
        $this->secure  = $secure;
        // set the headers
        $this->headers = array();

        // what's our protocol?
        $protocol = 'http';

        // if we're on secure
        if($this->secure) {
            $protocol = 'https';
        }

        // build out the connection url
        $this->url = $protocol . '://' . $this->host . ':' . $this->port;

        return $this;
    }

    /**
     * Abstract connect function signature.
     *
     * @return  Eden\Elastic\Resource
     */
    abstract public function connect();

    /**
     * Set elastic api host.
     *
     * @param   string
     * @return  Eden\Elastic\Index
     */
    public function setHost($host)
    {
        // set host
        $this->host = $host;

        return $this;
    }

    /**
     * Set elastic api port.
     *
     * @param   int
     * @return  Eden\Elastic\Index
     */
    public function setPort($port)
    {
        // set port
        $this->port = $port;

        return $this;
    }

    /**
     * Set elastic secure flag.
     *
     * @param   bool
     * @return  Eden\Elastic\Index
     */
    public function setSecure($secure)
    {
        // set secure
        $this->secure = $secure;

        return $this;
    }

    /**
     * Set request data.
     *
     * @param   array
     * @return  Eden\Elastic\Base
     */
    public function setData($data = array())
    {
        // set data
        $this->data = $data;

        return $this;
    }

    /**
     * Set optional headers.
     *
     * @param   array
     * @return  Eden\Elastic\Base
     */
    public function setHeaders($headers)
    {
        // set headers
        foreach($headers as $key => $value) {
            // set each headers
            $this->headers[$key] = $value;
        }

        return $this;
    }

    /**
     * Add optional header.
     *
     * @param   string
     * @param   *mixed
     * @return  Eden\Elastic\Base
     */
    public function addHeader($key, $value)
    {
        // set optional header
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Set the document that we
     * are going to use.
     *
     * @param   string
     * @return  Eden\Elastic\Base
     */
    public function setDocument($document)
    {
        // set default document
        $this->document = $document;

        return $this;
    }

    /**
     * Set the type under a document.
     *
     * @param   string
     * @return  Eden\Elastic\Base
     */
    public function setType($type)
    {
        // set default document type
        $this->type = $type;

        return $this;
    }

    /**
     * Set request options.
     *
     * @param   array
     * @return  Eden\Elastic\Base
     */
    public function setOptions($options)
    {
        // set options
        foreach($options as $key => $value) {
            $this->options[$key] = $value;    
        }

        return $this;
    }

    /**
     * Add option to the current set
     * of request options.
     *
     * @param   string
     * @param   *mixed
     * @return  Eden\Elastic\Base
     */
    public function addOption($key, $value)
    {
        // add option
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Basic request.
     *
     * @param   string
     * @param   array
     * @param   array
     * @param   array
     * @return  array
     */
    public function request($method, $data = array(), $query = array(), $headers = array())
    {
        // set the request url
        $request = $this->resource;

        // if data is set
        if(!empty($data)) {
            // let's set the request data
            $this->setData($data);
        }

        // if request headers is set
        if(!empty($headers)) {
            // add everything on our headers
            $this->setHeaders($headers);
        }

        // do we have extra headers?
        if(!empty($this->headers)) {
            // set the headers
            foreach($headers as $key => $value) {
                // set request headers
                $request->setHeader($key, $value);
            }
        }

        // if query is not empty
        if(!empty($query)) {
            // set options
            $this->setOptions($query);
        }

        // build out the url
        $url = $this->url;

        // if options are set
        if(!empty($this->options)) {
            // set query options
            $url = $url . '?' . http_build_query($this->options);
        }

        // let's set the url
        $request->setUrl($url);

        // if put request
        if($method == 'PUT') {
            // set as put request
            $request->setCustomRequest('PUT')
            // set content type
            ->setHeaders('Content-Type', 'application/json')
            // set post fields
            ->setPostFields(json_encode($this->data));
        } else if($method == 'POST') {
            // set as post request
            $request->setPost(true)
            // set content type
            ->setHeaders('Content-Type', 'application/json')
            // set post fields
            ->setPostFields(json_encode($this->data));
        } else if($method == 'DELETE') {
            // set custom request
            $request->setCustomRequest('DELETE');
        } else {
            // set as http get
            $request->setHttpGet(true);
        }

        try {
            // let's follow redirects
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
            // throw an exception
            return Exception::i(self::FAILED_REQUEST)->trigger();
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

    /**
     * Get the current resource
     * meta data.
     *
     * @return  array
     */
    public function getResource()
    {
        return get_object_vars($this);
    }
}
