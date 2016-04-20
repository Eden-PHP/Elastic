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
 * Base Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Base extends \Eden\Core\Base
{
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
     * Set index id.
     *
     * @var int
     */
    protected $id = null;

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
        $this->headers = $headers;

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
     * Set document id.
     *
     * @param   int
     * @return  Eden\Elastic\Base
     */
    public function setId($id)
    {
        // set document id
        $this->id = $id;

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
        $this->options = $options;

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
     * Get the current resource
     * meta data.
     *
     * @return  array
     */
    public function getResourceMeta()
    {
        return get_object_vars($this);
    }
}