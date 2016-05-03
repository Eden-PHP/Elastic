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
 * Search Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Search extends Base
{
    /**
     * Default query builder.
     *
     * @var Eden\Elastic\Query
     */
    protected $builder = null;

    /**
     * Resolve to reserved keys.
     *
     * @var array
     */
    protected $resolve = array(
        'source'            => '_source'
        'script.fields'     => 'script_fields',
        'fielddata.fields'  => 'fielddata_fields',
        'post.filter'       => 'post_filter'
    );

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

        // set default query builder
        $this->builder    = Query::i();
    }

    /**
     * Property setter and getter.
     *
     * @param   string
     * @param   array
     * @return  Resource
     */
    public function __call($name, $args)
    {
        // get, set, add
        $property = lcfirst(substr($name, 3));

        // property exists on resource?
        if(property_exists($this->connection, $property)) {
            // call the method
            $this->connection->__call($name, $args);

            return $this;
        }

        // are we going to set?
        if(strpos($name, 'set') === 0) {
            // transform to query key
            $key = \Eden_String_Index::i($name)
                ->substr(3)
                ->preg_replace("/([A-Z0-9])/", '.'."$1")
                ->substr(strlen('.'))
                ->strtolower()
                ->get();
            
            // if arg isn't set
            if (!isset($args[0])) {
                // default is null
                $args[0] = null;
            }

            // check resolve key
            if(isset($this->resolve[$key])) {
                // get the key to resolve
                $key = $this->resolve[$key];
            }

            // if we have two arguments
            if(count($args) == 2 && isset($args[0])) {
                // set the key
                $key = $key . '.' . $args[0];
                // set the value
                $val = isset($args[1]) ? $args[1] : null;

                // add tree to builder
                $this->builder->setTree($key, $val);
            } else {
                // set the value
                $val = isset($args[0]) ? $args[0] : null;

                // add tree to builder
                $this->builder->setTree($key, $val);
            }

            return $this;
        }
    }

    /**
     * Set sort helper.
     *
     * @param   string | array | null
     * @param   *mixed
     * @return  $this
     */
    public function setSort($field = null, $value = null)
    {
        // Argument test
        Argument::i()->test(1, 'string', 'array', 'null');

        // get the current sort
        $sort = $this->builder->getTree('sort');

        // if sort is null
        if(is_null($sort)) {
            $sort = array();
        }

        // if we have two argumets
        if(func_num_args() == 2) {
            // set sort field plus value
            $sort[] = array($field => $value);
        } else {
            $sort[] = $field;
        }

        // set the sort tree
        $this->builder->setTree('sort', $sort);

        return $this;
    }

    /**
     * Get the current query.
     *
     * @return  array
     */
    public function getQuery()
    {
        // return the query from builder
        return $this->builder->getQuery();
    }

    /**
     * Get results based on the query
     * or query body given.
     *
     * @param   string | null
     * @return  array
     */
    public function getResults($type = null)
    {
        // Argument test
        Argument::i()->test(1, 'string', 'null');

        // get the connection
        $connection = $this->connection;

        // if type is set
        if(isset($type)) {
            // set type
            $connection->setType($type);
        }

        // send request
        return $connection
        // require index
        ->requireIndex()
        // require type
        ->requireType()
        // send endpoint
        ->setEndpoint('_search')
        // send request
        ->send();
    }
}
