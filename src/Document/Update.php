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
 * Document Update Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Update extends Elastic\Document
{
    /**
     * Some basic magic to make
     * our life easier.
     *
     * @param   string
     * @param   array
     * @return  Eden\Elastic\Document\Update
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

            // are we going to set document script?
            if(strpos($key, 'script_') === 0) {
                // let's replace that to *
                $key = str_replace('script_', '', $key);

                // now let's set the script data
                if(!isset($this->data['script'])) {
                    // let's set script
                    $this->data['script'] = array();
                }

                // now let's set the script key
                $this->data['script'][$key] = $args[0];

                return $this;
            }

            // let's set the data
            $this->data[$key] = $args[0];
        }

        return $this;
    }

    /**
     * Updates a record based on document
     * id and the given document data.
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
        $endpoint = $this->type . '/' . $this->data['_id'] . '/_update';

        // unset the id
        unset($this->data['_id']);

        // do we have tail endpoint?
        if(isset($this->endpoint)) {
            // set tail endpoint
            $endpoint = $endpoint . '/' . $this->endpoint;
        }

        // try request
        try {
            // send post request
            $response = $this->connection
            ->request(Elastic\Index::POST, $endpoint, $this->data, $this->options);
        } catch(\Exception $e) {
            // throw an exception
            return Elastic\Exception::i($e->getMessage())->trigger();
        }

        return $response;
    }
}
