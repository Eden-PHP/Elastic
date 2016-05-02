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
     * Returns elastic Bulk API.
     *
     * @param   array
     * @return  Eden\Elastic\Bulk
     */
    public function bulk($data = array())
    {
        // initialize bulk
        $bulk = Bulk::i($this);

        // data set?
        if(!empty($data)) {
            // set data
            $bulk->setBody($data);
        }

        return $bulk;
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
     * Returns elastic query builder.
     *
     * @return  Eden\Elastic\Query
     */
    public function query()
    {
        return Query::i();
    }

    /**
     * Create or index a single document.
     *
     * @param   string | null
     * @param   array
     * @param   bool
     * @return  array
     */
    public function createRow($type = null, $data = array(), $auto = false)
    {
        // Argument test
        Argument::i()
            ->test(1, 'string', 'null')
            ->test(2, 'array')
            ->test(3, 'bool');

        // get the connection
        $connection = $this;

        // if type is set
        if(isset($type)) {
            // set type
            $connection->setType($type);
        }

        // if data is set
        if(!empty($data)) {
            // set data
            $connection->setBody($data);
        }

        // auto create id?
        if($auto) {
            $connection->setMethod(Index::POST);
        } else {
            $connection->setMethod(Index::PUT);
        }

        return $connection
        // require index
        ->requireIndex()
        // require type
        ->requireType()
        // require id
        ->requireId()
        // require body
        ->requireBody()
        // send request
        ->send();
    }

    /**
     * Create multiple documents.
     *
     * @param   string | null
     * @param   array
     * @return  array
     */
    public function createRows($type = null, $data = array())
    {
        // Argument test
        Argument::i()
            ->test(1, 'string', 'null')
            ->test(2, 'array');

        // get the connection
        $connection = $this;

        // if type is set
        if(isset($type)) {
            // set type
            $connection->setType($type);
        }

        // if data is set
        if(!empty($data)) {
            // set data
            $connection->setBody($data);
        }

        // initialize bulk
        $bulk = Bulk::i($connection);
        // get request body
        $body = $connection->getBody();

        // clear request body
        $connection->setBody(null);

        // iterate on each body
        foreach($body as $index) {
            // get the bulk action
            $action = array();
            // get the bulk data
            $data   = array();

            // iterate on each data
            foreach($index as $key => $value) {
                // if key starts with underscore
                if(strpos($key, '_') === 0) {
                    $action[$key] = $value;
                } else {
                    $data[$key] = $value;
                }
            }

            // add bulk
            $bulk->addBulk('index', $action);
            // add bulk
            $bulk->addBulk($data);
        }

        // send bulk request
        return $bulk->send($connection->getIndex(), $type);
    }

    /**
     * Update a document by id.
     *
     * @param   string | null
     * @param   array
     * @return  array
     */
    public function updateRow($type = null, $data = array())
    {
        // Argument test
        Argument::i()
            ->test(1, 'string', 'null')
            ->test(2, 'array');

        // get the connection
        $connection = $this;

        // if type is set
        if(isset($type)) {
            // set type
            $connection->setType($type);
        }

        // if data is set
        if(!empty($data)) {
            // set data
            $connection->setBody($data);
        }

        // initialize bulk
        $bulk = Bulk::i($connection);
        // get request body
        $body = $connection->getBody();

        // clear request body
        $connection->setBody(null);

        // get the bulk action
        $action = array();
        // get the bulk data
        $data   = array();

        // iterate on each data
        foreach($body as $key => $value) {
            // if key starts with underscore
            if(strpos($key, '_') === 0) {
                $action[$key] = $value;
            } else {
                $data[$key] = $value;
            }
        }

        // add bulk
        $bulk->addBulk('update', $action);
        // add bulk
        $bulk->addBulk('doc', $data);

        // send bulk request
        return $bulk->send($connection->getIndex(), $type);
    }

    /**
     * Update multiple documents by id.
     *
     * @param   string | null
     * @param   array
     * @return  array
     */
    public function updateRows($type = null, $data = array())
    {
        // Argument test
        Argument::i()
            ->test(1, 'string', 'null')
            ->test(2, 'array');

        // get the connection
        $connection = $this;

        // if type is set
        if(isset($type)) {
            // set type
            $connection->setType($type);
        }

        // if data is set
        if(!empty($data)) {
            // set data
            $connection->setBody($data);
        }

        // initialize bulk
        $bulk = Bulk::i($connection);
        // get request body
        $body = $connection->getBody();

        // clear request body
        $connection->setBody(null);

        // iterate on each body
        foreach($body as $index) {
            // get the bulk action
            $action = array();
            // get the bulk data
            $data   = array();

            // iterate on each data
            foreach($index as $key => $value) {
                // if key starts with underscore
                if(strpos($key, '_') === 0) {
                    $action[$key] = $value;
                } else {
                    $data[$key] = $value;
                }
            }

            // add bulk
            $bulk->addBulk('update', $action);
            // add bulk
            $bulk->addBulk('doc', $data);
        }

        // send bulk request
        return $bulk->send($connection->getIndex(), $type);
    }

    /** 
     * Delete a single document based on id.
     *
     * @param   string | null
     * @param   array
     * @return  array
     */
    public function deleteRow($type = null, $data = array())
    {
        // Argument test
        Argument::i()->test(1, 'string', 'null');

        // get the connection
        $connection = $this;

        // if type is set
        if(isset($type)) {
            // set type
            $connection->setType($type);
        }

        // if data is set
        if(!empty($data)) {
            // set data
            $connection->setBody($data);
        }

        return $connection
        // require id
        ->requireId()
        // require index
        ->requireIndex()
        // require tpye
        ->requireType()
        // set method
        ->setMethod(Index::DELETE)
        // send request
        ->send();
    }

    /**
     * Delete multiple indexes based on id.
     *
     * @param   string | null
     * @param   array
     * @return  array
     */
    public function deleteRows($type = null, $data = array())
    {
        // Argument test
        Argument::i()->test(1, 'string', 'null');

        // get the connection
        $connection = $this;

        // if type is set
        if(isset($type)) {
            // set type
            $connection->setType($type);
        }

        // if data is set
        if(!empty($data)) {
            // set data
            $connection->setBody($data);
        }

        // initialize bulk
        $bulk = Bulk::i($connection);
        // get request body
        $body = $connection->getBody();

        // clear request body
        $connection->setBody(null);

        // iterate on each body
        foreach($body as $index) {
            // set delete action
            $bulk->addBulk('delete', $index);
        }

        // send bulk request
        return $bulk->send($connection->getIndex(), $type);
    }
}
