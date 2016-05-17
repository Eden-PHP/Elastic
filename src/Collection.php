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
 * Collection Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Collection extends \Eden\Collection\Index
{
    /**
     * Default model.
     *
     * @var string
     */
    protected $model = Index::MODEL;

    /**
     * Elastic index name.
     *
     * @var string
     */
    protected $index = null;

    /**
     * Elastic index type.
     *
     * @var string
     */
    protected $type = null;

    /**
     * Default connection resource.
     *
     * @var Eden\Elastic\Index
     */
    protected $connection = null;

    /**
     * Add an array or model to our collection.
     *
     * @param   array | Eden\Elastic\Model
     * @return  $this
     */
    public function add($row = array())
    {
        //Argument 1 must be an array or Eden_Model
        Argument::i()->test(1, 'array', $this->model);
        
        // if it's an array
        if(is_array($row)) {
            // get the model
            $model = $this->model;

            // initialize model
            $row = $model::i($row);
        }
            
        // if it's a connection resource
        if(!is_null($this->connection)) {
            // set the connection resource
            $row->setConnection($this->connection);
        }
        
        // if index is set
        if(!is_null($this->index)) {
            // set the index
            $row->setIndex($this->index);
        }

        // if type is set
        if(!is_null($this->type)) {
            // set the type
            $row->setType($this->type);
        }
        
        // add it now
        $this->list[] = $row;
        
        return $this;
    }

    /**
     * Sets default model
     *
     * @param   string 
     * @return  $this
     */
    public function setModel($model)
    {
        Argument::i()->test(1, 'string');

        // if it's not a valid model
        if ($model != Index::MODEL
        && !is_subclass_of($model, Index::MODEL)) {
            // throw an exception
            Exception::i()
                ->setMessage(Exception::NOT_SUB_MODEL)
                ->addVariable($model)
                ->trigger();
        }
        
        // set the default model
        $this->model = $model;

        return $this;
    }

    /**
     * Set elastic index.
     *
     * @param   string
     * @return  $this
     */
    public function setIndex($index)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // set the index
        $this->index = $index;

        // for each row
        foreach ($this->list as $row) {
            if (!is_object($row) || !method_exists($row, __FUNCTION__)) {
                continue;
            }
            
            // let the row handle this
            $row->setIndex($index);
        }

        return $this;
    }

    /**
     * Set elastic type.
     *
     * @param   string
     * @return  $this
     */
    public function setType($type)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // set the type
        $this->type = $type;

        // for each row
        foreach ($this->list as $row) {
            if (!is_object($row) || !method_exists($row, __FUNCTION__)) {
                continue;
            }
            
            // let the row handle this
            $row->setType($type);
        }

        return $this;
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

        // set the connection
        $this->connection = $connection;
        
        // for each row
        foreach ($this->list as $row) {
            if (!is_object($row) || !method_exists($row, __FUNCTION__)) {
                continue;
            }
            
            // let the row handle this
            $row->setConnection($connection);
        }
        
        return $this;
    }
}
