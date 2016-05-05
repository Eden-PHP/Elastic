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
 * Query Builder Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Query extends Base
{
    /**
     * Query set.
     *
     * @var array
     */
    protected $query = array();

    /**
     * Set / Add a key to the query set,
     * nested key's is separated by
     * comma, value of the key can
     * be anything.
     *
     * @param   string | array | null
     * @param   *mixed
     * @return  $this
     */
    public function setTree($key = null, $value = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'string', 'array', 'null');

        // if key is not set
        if(is_null($key)) {
            return $this;
        }

        // if key is an array
        if(is_array($key)) {
            // just add it on our query body
            $this->query = array_merge($this->query, $key);

            return $this;
        }

        // create key path
        $keys = $this->createKey($key);

        // get the original set of query
        $original  = $this->query;
        // maximum depth
        $max       = count($keys);

        // if we have only one key
        if($max == 1) {
            // set that key on root instead
            $this->query[array_pop($keys)] = $value;

            return $this;
        }

        // recursively add keys to the query
        $query = $this->recurse($original, $value, $keys, 0, $max);

        // set the query to original
        $this->query = $query;

        return $this;
    }

    /**
     * Get's a key and value pair depending
     * on the key that is passed as the first
     * parameter, we could also access nested
     * key's by separating key's by comma.
     *
     * @param   string | null
     * @return  $this
     */
    public function getTree($key = null)
    {
        // Argument test
        Argument::i()->test(1, 'string', 'null');

        // if key is not set
        if(is_null($key)) {
            // return the entire query
            return $this->query;
        }

        // create key path
        $keys = $this->createKey($key);

        // get the original set of query
        $original  = $this->query;
        // maximum depth
        $max       = count($keys);

        // if we only have 1 key
        if($max == 1) {
            // return the key
            return isset($this->query[array_pop($keys)]) ? $this->query[array_pop($keys)] : null;
        }

        return $this->scan($this->query, $keys, 0, $max);
    }

    /**
     * Add a value to an array path.
     *
     * @param   string
     * @param   *mixed
     * @param   string | null
     * @return  $this
     */
    public function addTree($key, $value = null, $custom = null)
    {
        // Argument test
        Argument::i()
            ->test(1, 'string')
            ->test(3, 'string', 'null');

        // get the key path
        $current = $this->getTree($key);

        // if it's null
        if(is_null($current)) {
            $current = array();
        }

        // if has custom keys
        if($custom) {
            $current[$custom] = $value;
        } else {
            $current[] = $value;
        }

        // set the tree
        $this->setTree($key, $current);

        return $this;
    }

    /**
     * Get the query array.
     *
     * @return  array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Recursively add keys to an array.
     *
     * @param   array | null
     * @param   *mixed
     * @param   array
     * @param   int
     * @param   int
     * @return  array
     */
    private function recurse(&$target, $value,  $keys, $index, $max)
    {
        // are we on the last part?
        if($max == $index) {
            $target = $value;

            return $target;
        }

        // get the current key
        $key = $keys[$index];

        // are we on the first index, does the
        // key already exists or not?
        if(!isset($target[$key])) {
            // put some empty array
            $target[$key] = array();
        }

        // re-iterate process
        $this->recurse($target[$key], $value, $keys, ++$index, $max);

        return $target;
    }

    /**
     * Scans the object tree recursively.
     *
     * @param   array
     * @param   string
     * @param   int
     * @param   int
     * @return  *mixed
     */
    private function scan($target, $keys, $start, $end)
    {
        // get current
        $current = isset($target[$keys[$start]]) ? $target[$keys[$start]] : null;

        // if not found
        if(is_null($current)) {
            return $current;
        }

        // are we on the end?
        if(($end - 1) == $start) {
            return $current;
        }

        // if it's an array
        if(is_array($current)) {
            return $this->scan($current, $keys, ++$start, $end);
        }
    }

    /**
     * Creates a path.
     *
     * @param   string
     * @return  string
     */
    private function createKey($key)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // replace dot's with >
        $key = str_replace('.', '>', $key);
        // replace all escaped keys
        $key = preg_replace_callback('/(\[[^]]*?)(\>)([^]]*?\])/m', function($matches) { 
            // replace back all the > to dots
            return str_replace('>', '.', $matches[0]); 
        }, $key);
        // replace all [ ]
        $key = str_replace(array('[', ']'), '', $key);

        // explode keys
        $keys = explode('>', $key);

        return $keys;
    }
}
