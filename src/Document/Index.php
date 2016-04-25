<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Eden\Elastic\Document;

use Eden\Elastic as Elastic;

/**
 * Document Index Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Index extends Elastic\Document
{
    /**
     * Some basic magic to make
     * our life easier.
     *
     * @param   string
     * @param   array
     * @return  Eden\Elastic\Document\Index
     */
    public function __call($name, $args)
    {
        // if we wanted to set document data
        if(strpos($name, 'set') === 0) {
            // choose separator
            $separator = '_';

            // do we have custom separator?
            if (isset($args[1]) && is_scalar($args[1])) {
                $separator = (string) $args[1];
            }
            
            //transform method to column name
            $key = \Eden_String_Index::i($name)
                ->substr(3)
                ->preg_replace("/([A-Z0-9])/", $separator."$1")
                ->substr(strlen($separator))
                ->strtolower()
                ->get();

            // does value set?
            if(!isset($args[0])) {
                $args[0] = null;
            }

            // are we going to set document variables?
            if(strpos($key, 'document_') === 0) {
                // let's replace that to _*
                $key = str_replace('document_', '_', $key);
            }

            // let's set the data
            $this->data[$key] = $args[0];
        }

        return $this;
    }

    /**
     * Index the given data.
     *
     * @param   string | null
     * @param   array
     * @return  array
     */
    public function save($type = null, $options = array())
    {
        // Argument test
        Elastic\Argument::i()
            ->test(1, 'string', 'null')
            ->test(2, 'array');

        // get connection information
        $elastic = $this->connection->getResource();

        // if index is not set
        if(empty($elastic['index'])) {
            // throw exception
            return Elastic\Exception::i(self::INDEX_NOT_SET)->trigger();
        }

        // if index type is not set
        if(!isset($this->type) && !isset($type)) {
            // throw exception
            return Elastic\Exception::i(self::INDEX_TYPE_NOT_SET)->trigger();
        }

        // is data set?
        if(empty($this->data)) {
            // throw exception
            return Elastic\Exception::i(self::DATA_NOT_SET)->trigger();
        }

        // is id set?
        if(!isset($this->data['_id'])) {
            // throw exception
            return Elastic\Exception::i(self::ID_NOT_SET)->trigger();
        }

        // if type arg is set
        if(isset($type)) {
            // set document index type
            $this->setType($type);
        }

        // if options is set
        if(!empty($options)) {
            // set document options
            $this->setOptions($options);
        }

        // let's formulate the endpoint
        $endpoint = $this->type . '/' . $this->data['_id'];

        // unset the id
        unset($this->data['_id']);

        // do we have tail endpoint?
        if(isset($this->endpoint)) {
            // set tail endpoint
            $endpoint = $endpoint . '/' . $this->endpoint;
        }

        // try request
        try {
            // send put request
            $response = $this->connection
            ->request(Elastic\Index::PUT, $endpoint, $this->data, $this->options);
        } catch(\Exception $e) {
            // throw an exception
            return Elastic\Exception::i($e->getMessage())->trigger();
        }

        return $response;
    }
}
