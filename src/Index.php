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
class Index extends \Eden\Elastic\Base
{
    /**
     * Build's the connection string.
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
        $headers = array())
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
}
