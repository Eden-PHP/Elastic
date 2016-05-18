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
class Resource extends Base
{
    /**
     * Get request.
     *
     * @const string
     */
    const GET = 'GET';

    /**
     * POST request.
     *
     * @const string
     */
    const POST = 'POST';

    /**
     * PUT request.
     *
     * @const string
     */
    const PUT = 'PUT';

    /**
     * DELETE request.
     *
     * @const string
     */
    const DELETE = 'DELETE';

    /**
     * HEAD request.
     *
     * @const string
     */
    const HEAD = 'HEAD';

    /**
     * OPTIONS request.
     *
     * @const string
     */
    const OPTIONS = 'OPTIONS';

    /**
     * Required property error.
     *
     * @const string
     */
    const REQUIRED = 'Request %s is required.';

    /**
     * Error sending request.
     *
     * @const string
     */
    const REQUEST_ERROR = 'An error occured while sending request.';

    /**
     * API Host.
     *
     * @var string
     */
    protected $host = 'http://localhost:9200';

    /**
     * API Id.
     *
     * @var scalar | null
     */
    protected $id = null;

    /**
     * API Index.
     *
     * @var string | null
     */
    protected $index = null;

    /**
     * API Type.
     *
     * @var string | null
     */
    protected $type = null;

    /**
     * API Tail endpoint.
     *
     * @var string | null
     */
    protected $endpoint = null;

    /**
     * API Body.
     *
     * @var array | string | null
     */
    protected $body = null;

    /**
     * API Query Parameters.
     *
     * @var array
     */
    protected $param = array();

    /**
     * Test request, flag
     * for HEAD request.
     *
     * @var bool
     */
    protected $test = false;

    /**
     * Debug flag.
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * Binary request flag.
     *
     * @var bool
     */
    protected $binary = false;

    /**
     * Request metod.
     *
     * @var string | null
     */
    protected $method = null;

    /**
     * Request headers.
     *
     * @var array
     */
    protected $headers = array();

    /**
     * Required properties before
     * sending the request.
     *
     * @var array
     */
    protected $require = array();

    /**
     * Connection resource.
     *
     * @var Eden\Index\Curl
     */
    protected $connection = null;

    /**
     * Initialize connection.
     *
     * @param   string | null
     * @param   string | null
     * @return  Resource
     */
    public function __construct($host = null, $index = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'string', 'null')
            ->test(2, 'string', 'null');

        // is host set?
        if(isset($host)) {
            $this->host = $host;
        }

        // is index set?
        if(isset($index)) {
            $this->index = $index;
        }
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
        // are we going to set required property?
        if(strpos($name, 'require') === 0) {
            // get the property
            $property = str_replace('require', '', $name);

            // property exists?
            if(!property_exists($this, lcfirst($property))) {
                return $this;
            }

            // set required field
            $this->require[] = lcfirst($property);

            return $this;
        }

        // get, set, add
        $property = lcfirst(substr($name, 3));

        // check if property exists
        if(!property_exists($this, $property)) {
            return $this;
        }

        // are we going to set?
        if(strpos($name, 'set') === 0) {
            $this->$property = isset($args[0]) ? $args[0] : null;
            
            return $this;
        }

        // are we going to add something?
        if(strpos($name, 'add') === 0) {
            // does property exists and is an array?
            if(is_array($this->$property)) {
                // get the key
                $key = isset($args[0]) ? $args[0] : null;
                // get the value
                $val = isset($args[1]) ? $args[1] : null;

                // does the key set?
                if(is_null($key)) {
                    return $this;
                }

                // get the data first
                $data = $this->$property;

                // add the key value
                $data[$key] = $val;

                // set the property
                $this->$property = $data;
            }

            return $this;
        }

        // are we going to get?
        if(strpos($name, 'get') === 0) {
            return $this->$property;
        }
    }

    /**
     * Send the request.
     *
     * @return  array
     */
    public function send()
    {
        // is id set in body?
        if(isset($this->body['_id'])) {
            $this->id = $this->body['_id'];

            // unset id
            unset($this->body['_id']);
        }

        // is index set?
        if(isset($this->body['_index'])) {
            $this->index = $this->body['_index'];

            // unset index
            unset($this->body['_index']);
        }

        // is type set?
        if(isset($this->body['_type'])) {
            $this->type = $this->body['_type'];

            // unset type
            unset($this->body['_type']);
        }

        // first let's check all required fields.
        foreach($this->require as $property) {
            // are we requiring scalar?
            if(!is_array($this->$property) 
            && !is_null($this->$property)) {
                continue;
            }

            // are we requiring array?
            if(is_array($this->$property)
            && !empty($this->$property)) {
                continue;
            }

            // throw an exception
            return Exception::i(sprintf(self::REQUIRED, strtoupper($property)))->trigger();
        }

        // initialize request
        $request = \Eden\Curl\Index::i();

        // set request url
        $url = $this->host;

        // is index set?
        if(isset($this->index)) {
            $url = $url . '/' . $this->index;
        }

        // is index type set?
        if(isset($this->type)) {
            $url = $url . '/' . $this->type;
        }

        // is id set?
        if(isset($this->id)) {
            $url = $url . '/' . $this->id;
        }

        // do we have tail endpoint?
        if(isset($this->endpoint)) {
            $url = rtrim($url, '/') . '/' . $this->endpoint;
        }

        // is query parameters set?
        if(!empty($this->param)) {
            // parameter separator
            $separator = '?';
            // do we have ? already?
            if(strpos($url, '?') !== false) {
                // set separator
                $separator = '&';
            }
            // set request query parameters
            $url = $url . $separator . http_build_query($this->param);
        }

        // set request url
        $request->setUrl($url);

        // does request method set?
        if(!isset($this->method)) {
            // default to get method
            $this->method = self::GET;
        }

        // test request?
        if($this->test) {
            // set method to head
            $this->method = self::HEAD;
        }

        // set custom request
        $request->setCustomRequest($this->method);

        // do we have data?
        if(!empty($this->body)) {
            // are we going to send this as binary request?
            if($this->binary) {
                // set string body
                $body = '';

                // iterate on each body
                foreach($this->body as $value) {
                    // if empty array
                    if(empty($value)) {
                        $body = $body . '{}' . "\n";

                        continue;
                    }

                    // encode body and add new line
                    $body = $body . json_encode($value) . "\n";
                }

                // set the body and set request as binary
                $request->setPostFields($body)
                ->setBinaryTransfer(true);
            } else {
                // set post fields
                $request->setPostFields(json_encode($this->body));
            }

            // set custom header
            $this->addHeaders('Content-Type', 'application/json');
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

        // if debug
        if($this->debug) {
            echo '<pre style="color:red">';
            echo '**DEBUG MODE**' . PHP_EOL;
            echo 'Request URL     : ' . $url . PHP_EOL;
            echo 'Request Method  : ' . $this->method . PHP_EOL;
            echo 'Request Data    : ' . PHP_EOL;
            print_r($this->body);
            if($this->binary) {
                echo 'Binary Data     : ' . PHP_EOL;
                echo $body . PHP_EOL;
            }
            echo PHP_EOL;
            echo 'Request Meta    : ' . PHP_EOL;
            print_r($request->getMeta());
            echo PHP_EOL . PHP_EOL;
            echo '</pre>';
        }

        // get request meta data
        $meta = $request->getMeta();

        // do we have request errors?
        if($meta['info'] == 400 || $meta['info'] == 0) {
            // get the message
            $message = $meta['response'];
            // empty message?
            if(strlen($message) == 0) {
                // get the error code
                $message = $meta['error_code'];
            }
            // throw the error from request
            return Exception::i($message)->trigger();
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

        // reset properties
        $this->reset();

        return isset($response) || !empty($response) ? $response : array();
    }

    /**
     * Reset's default resource configuration.
     *
     * @return  Eden\Elastic\Resource
     */
    private function reset()
    {
        // reset id
        $this->id       = null;
        // reset type
        $this->type     = null;
        // reset request method
        $this->method   = null;
        // reset request body
        $this->body     = null;
        // reset query parameters
        $this->param    = array();
        // reset required properties
        $this->require  = array();
        // reset endpoint
        $this->endpoint = null;
        // reset binary flag
        $this->binary   = false;
        // reset headers
        $this->headers  = array();

        return $this;
    }
}