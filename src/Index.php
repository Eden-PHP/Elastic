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
    const UNABLE_TO_CONNECT = 'Unable to connect to: %s';

    /**
     * Connects to elastic api.
     *
     * @return  Eden\Elastic\Index
     */
    public function connect()
    {
        // let's try to connect
        try {
            // test request
            $response = $this->request(self::GET, '');
        } catch(\Exception $e) {
            // throw an exception
            return Exception::i(self::UNABLE_TO_CONNECT)->trigger();
        }

        // set connection information
        $this->elastic      = $response;
        // set connected flag
        $this->connected    = true;

        echo '<pre>';
        print_r($this);

        return $this;
    }
}
