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
 * Document API Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Document extends Base
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
            $this->$property = isset($args[0]) ? $args[0] : null;
            
            return $this;
        }

        // are we going to add something?
        if(strpos($name, 'add') === 0) {
            // does property exists and is an array?
            if(is_array($this->$property)) {
                // get the key
                $key = isset($args[0]) ? $args[0] : null;
                // get the value
                $val = isset($args[1]) ? $args[1] : null;

                // does the key set?
                if(is_null($key)) {
                    return $this;
                }

                // set the property
                $this->$property[$key] = $val;
            }

            return $this;
        }
    }
}