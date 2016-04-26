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
            return Exception::i(sprintf(self::UNABLE_TO_CONNECT, $this->host))->trigger();
        }

        // set connection information
        $this->elastic      = $response;
        // set connected flag
        $this->connected    = true;

        return $this;
    }

    /**
     * Returns elastic Document API.
     *
     * @param   array
     * @return  Eden\Elastic\Document
     */
    public function document($data = array())
    {
        // initialize document
        $document = Document::i($this);

        // data set?
        if(!empty($data)) {
            // set data
            $document->setBody($data);
        }

        return $document;
    }

    /**
     * Debugging purposes.
     *
     * @param   *mixed
     * @return  Eden\Elastic\Index
     */
    public static function debug($message = '')
    {
        $message = '<pre>' . $message;

        print PHP_EOL;
        print $message;
        print PHP_EOL;
    }
}
