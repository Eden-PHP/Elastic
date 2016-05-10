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
        'source'            => '_source',
        'script.fields'     => 'script_fields',
        'fielddata.fields'  => 'fielddata_fields',
        'post.filter'       => 'post_filter',
        'scroll.id'         => 'scroll_id',
        'indices.boost'     => 'indices_boost',
        'min.score'         => 'min_score',
        'start'             => 'from',
        'range'             => 'size'
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

        // are we going to add?
        if(strpos($name, 'add') === 0) {
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
                $this->builder->addTree($key, $val);
            } else {
                // set the value
                $val = isset($args[0]) ? $args[0] : null;

                // add tree to builder
                $this->builder->addTree($key, $val);
            }

            return $this;
        }

        // are we going to filter?
        if(strpos($name, 'filterBy') === 0) {
            // transform to query key
            $key = \Eden_String_Index::i($name)
                ->substr(8)
                ->preg_replace("/([A-Z0-9])/", '_'."$1")
                ->substr(strlen('_'))
                ->strtolower()
                ->get();
            
            // if arg isn't set
            if (!isset($args[0])) {
                // default is null
                $args[0] = 'DESC';
            }

            // add it to builder
            $this->builder->addTree('query.bool.must', array('match' => array($key => $args[0])));

            return $this;
        }

        // are we going to sort?
        if(strpos($name, 'sortBy') === 0) {
            // transform to query key
            $key = \Eden_String_Index::i($name)
                ->substr(6)
                ->preg_replace("/([A-Z0-9])/", '_'."$1")
                ->substr(strlen('_'))
                ->strtolower()
                ->get();
            
            // if arg isn't set
            if (!isset($args[0])) {
                // default is null
                $args[0] = 'DESC';
            }

            // add it to builder
            $this->builder->addTree('sort', array($key => $args[0]));

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
        // get the original body
        $body = $this->connection->getBody();

        // if body is not empty
        if(!is_null($body)) {
            $body = array_merge($body, $this->builder->getQuery());
        } else {
            $body = $this->builder->getQuery();
        }

        // return the query from builder
        return $body;
    }

    /**
     * Returns elastic Search Template API.
     *
     * @return  Eden\Elastic\Search\Tempplate
     */
    public function template()
    {
        // initialize template
        $template = Search\Template::i($this->connection);

        // get the current data
        $data = $this->getQuery();

        // data set?
        if(!empty($data)) {
            // set data
            $template->setBody($data);
        }

        return $template;
    }

    /**
     * Returns elastic Scroll API.
     *
     * @return  Eden\Elastic\Search\Scroll
     */
    public function scroll()
    {
        // initialize scroll
        $scroll = Search\Scroll::i($this->connection);

        // get the current data
        $data = $this->getQuery();

        // data set?
        if(!empty($data)) {
            // set data
            $scroll->setBody($data);
        }

        return $scroll;
    }

    /**
     * Returns elastic Shards API.
     *
     * @return  Eden\Elastic\Search\Shards
     */
    public function shards()
    {
        // initialize shards
        $shards = Search\Shards::i($this->connection);

        // get the current data
        $data = $this->getQuery();

        // data set?
        if(!empty($data)) {
            // set data
            $shards->setBody($data);
        }

        return $shards;
    }

    /**
     * Returns the suggesters API.
     *
     * @return  Eden\Elastic\Search\Suggesters
     */
    public function suggesters()
    {
        // initialize shards
        $suggesters = Search\Suggesters::i($this->connection);

        // get the current data
        $data = $this->getQuery();

        // data set?
        if(!empty($data)) {
            // set data
            $suggesters->setBody($data);
        }

        return $suggesters;
    }

    /**
     * Get single record based on the
     * query or the query body given.
     *
     * @param   string | null
     * @return  array
     */
    public function getRow($type = null)
    {
        // Argument test
        Argument::i()->test(1, 'string', 'null');

        // set range
        $this->setRange(1);

        return $this->getRows($type);
    }

    /**
     * Get results based on the query
     * or query body given.
     *
     * @param   string | null
     * @return  array
     */
    public function getRows($type = null)
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

        // get request body
        $body = $this->getQuery();

        // if body is not empty or not is null
        if(!is_null($body) || (is_array($body) && !empty($body))) {
            // set request body
            $connection->setBody($body);
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
