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
 * Model Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Model extends \Eden\Model\Index
{
    /**
     * Default connection resource.
     *
     * @var Eden\Elastic\Index
     */
    protected $connection = null;

    /**
     * Set default connection resource.
     *
     * @param   Eden\Elastic\Index
     */
    public function __construct(Index $connection)
    {
        // Argument test
        Argument::i()->test(1, '\\Eden\\Elastic\\Index');

        // set connection
        $this->connection = $connection;
    }

    /**
     * Set connection handler.
     *
     * @param   Eden\Elastic\Index
     * @return  $this
     */
    public function setConnection(Index $connection)
    {
        // Argument test
        Argument::i()->test(1, '\\Eden\\Elastic\\Index');

        // set connection
        $this->connection = $connection;
    
        return $this;
    }

    /**
     * Set index name.
     *
     * @param   string
     * @return  $this
     */
    public function setIndex($index)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // set the connection index
        $this->connection->setIndex($index);

        return $this;
    }

    /**
     * Set index type.
     *
     * @param   string
     * @return  $this
     */
    public function setType($type)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // set the connection type
        $this->connection->setType($type);

        return $this;
    }

    /**
     * Set model id.
     *
     * @param   int | string
     * @return  $this
     */
    public function setId($id)
    {
        // Argument test
        Argument::i()->test(1, 'int', 'string');

        // set the model id
        $this->connection->setBody(array('_id' => $id));

        return $this;
    }

    /**
     * Saves the model data, this will
     * do upsert functionality, if record
     * does not exists it will create the
     * record otherwise it will update.
     *
     * @param   string
     * @return  $this
     */
    public function save($type)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // get the connection
        $connection = $this->connection;

        // get the data
        $data = $this->getData();

        // get the original data
        $this->original = $data;

        // send the request
        $this->connection->update($data, $type);

        return $this;
    }

    /**
     * Insert's the model data.
     *
     * @param   string
     * @return  $this
     */
    public function insert($type)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // get the connection
        $connection = $this->connection;

        // get the data
        $data = $this->getData();

        // get the original data
        $this->original = $data;

        // send the request
        $this->connection
        // set the endpoint
        ->setEndpoint('_create')
        // insert the data
        ->insert($data, $type);

        return $this;
    }

    /**
     * Update's the model data.
     *
     * @param   string
     * @return  $this
     */
    public function update($type)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // get the connection
        $connection = $this->connection;

        // get the data
        $data = $this->getData();

        // get the original data
        $this->original = $data;

        // send the request
        $this->connection->update($data, $type);

        return $this;
    }

    /**
     * Remove the model data.
     *
     * @param   string
     * @return  $this
     */
    public function remove($type)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // get the connection
        $connection = $this->connection;

        // get the data
        $data = $this->getData();

        // get the original data
        $this->original = $data;

        // send the request
        $this->connection->delete($data, $type);

        return $this;
    }

    /**
     * Get the data.
     *
     * @param   bool
     * @return  array
     */
    protected function getData($id = false)
    {
        // get the data
        $data = array();

        // iterate on each data
        foreach($this as $key => $value) {
            // set the key value pair
            $data[$key] = $value;
        }

        // get the connection body
        $body = $this->connection->getBody();

        // if body is set
        if(!is_null($body)) {
            // merge body and data
            $data = array_merge($body, $data);
        }

        // if id only
        if($id) {
            // get the ids
            $ids = array();

            // iterate on each data
            foreach($data as $key => $value) {
                // if id key
                if($key == '_id') {
                    // set the id
                    $ids[$key] = $value;
                }
            }

            return $ids;
        }

        return $data;
    }
}
