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
     * Default model.
     *
     * @const string
     */
    const MODEL = 'Eden\\Elastic\\Model';

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
     * Returns elastic Search API.
     *
     * @param   array
     * @return  Eden\Elastic\Search
     */
    public function search($data = array())
    {
        // initialize search
        $search = Search::i($this);

        // is string?
        if(is_string($data)) {
            // set type
            $search->setType($data);
        }

        // data set?
        if(is_array($data) && !empty($data)) {
            // set data
            $search->setBody($data);
        }

        return $search;
    }

    /**
     * Returns elastic model.
     *
     * @param   array
     * @return  Eden\Elastic\Model
     */
    public function model($data = array())
    {
        // initialize model
        $model = Model::i();

        // is string?
        if(is_string($data)) {
            // set type
            $model->setType($data);
        }

        // data set?
        if(is_array($data) && !empty($data)) {
            // set data
            $model->set($data);
        }

        return $model->setConnection($this);
    }

    /**
     * Returns elastic collection.
     *
     * @param   array
     * @return  Eden\Elastic\Collection
     */
    public function collection($data = array())
    {
        // initialize collection
        $collection = Collection::i();

        // is string?
        if(is_string($data)) {
            // set type
            $collection->setType($data);
        }

        // data set?
        if(is_array($data) && !empty($data)) {
            // set data
            $collection->set($data);
        }

        return $collection
        // set the connection
        ->setConnection($this)
        // set the model
        ->setModel(Index::MODEL);
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
     * @param   array | null
     * @param   string | null
     * @param   bool
     * @return  array
     */
    public function insert($data = array(), $type = null, $auto = false)
    {
        // Argument test
        Argument::i()
            ->test(1, 'array', 'null')
            ->test(2, 'string', 'null')
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
     * @param   array | null
     * @param   string | null
     * @return  array
     */
    public function insertRows($data = array(), $type = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'array', 'null')
            ->test(2, 'string', 'null');

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
     * @param   array | null
     * @param   string | null
     * @return  array
     */
    public function update($data = array(), $type = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'array',  'null')
            ->test(2, 'string', 'null');

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
     * @param   array | null
     * @param   string | null
     * @return  array
     */
    public function updateRows($data = array(), $type = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'array', 'null')
            ->test(2, 'string', 'null');

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
     * @param   array | null
     * @param   string | null
     * @return  array
     */
    public function delete($data = array(), $type = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'array', 'null')
            ->test(2, 'string', 'null');

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
        // require type
        ->requireType()
        // set method
        ->setMethod(Index::DELETE)
        // send request
        ->send();
    }

    /**
     * Delete multiple indexes based on id.
     *
     * @param   array | null
     * @param   string | null
     * @return  array
     */
    public function deleteRows($data = array(), $type = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'array', 'null')
            ->test(2, 'string', 'null');

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

    /**
     * Select document by id.
     *
     * @param   string | int
     * @param   string | null
     * @param   bool
     * @return  array
     */
    public function get($id, $type = null, $test = false)
    {
        // Argument test
        Argument::i()
            ->test(1, 'string', 'int')
            ->test(2, 'string', 'null');

        // get the connection
        $connection  = $this;

        // if type is set
        if(isset($type)) {
            // set index type
            $connection->setType($type);
        }

        // if test existence
        if($test) {
            // set head method
            $connection->setMethod(Index::HEAD);
        } else {
            // set get method
            $connection->setMethod(Index::GET);
        }

        return $connection
        // require index
        ->requireIndex()
        // require type
        ->requireType()
        // require id
        ->requireId()
        // set id
        ->setId($id)
        // send request
        ->send();
    }

    /**
     * Select documents by id.
     *
     * @param   array | null
     * @param   string | null
     * @return  array
     */
    public function getRows($data = array(), $type = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'array', 'null')
            ->test(2, 'string', 'null');

        // get the connection
        $connection  = $this;

        // if type is set
        if(isset($type)) {
            // set index type
            $connection->setType($type);
        }

        // if data is not empty
        if(!empty($data)) {
            // load up the query builder
            $query = Query::i();

            // create the path
            $path = 'query.bool.should.%s.match';

            // let's add every path
            foreach($data as $index => $value) {
                // format the path
                $tree = sprintf($path, $index);

                // add the query tree
                $query->setTree($tree, $value);
            }

            // get the whole query
            $query = $query->getQuery();

            // set the request body
            $connection->setBody($query);
        }

        return $connection
        // require index
        ->requireIndex()
        // require type
        ->requireType()
        // require body
        ->requireBody()
        // set endpoint
        ->setEndpoint('_search')
        // send request
        ->send();
    }
}
