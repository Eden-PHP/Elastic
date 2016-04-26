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
