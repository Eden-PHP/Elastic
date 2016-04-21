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
     * Default resource
     *
     * @var Eden\Elastic\Index
     */
    protected $resource = null;

    /**
     * Initialize Document class
     *
     * @param   Eden\Elastic\Index
     * @return  Eden\Elastic\Document
     */
    public function __construct(\Eden\Elastic\Index $resource)
    {
        // set the default resource
        $this->resource = $resource;
    }

    /**
     * Creates a document index based
     * on the given option and data.
     *
     * @param   int
     * @param   string
     * @param   array
     * @return  array
     */
    public function index($id, $type, $data = array()) 
    {
        // get argument count
        $count = func_num_args();

        // we have 3 arguments?
        if($count == 3) {
            // set document id
            $this->id   = $id;
            // set document type
            $this->type = $type;
            // set document data
            $this->data = $data;
        }

        // we have 2 arguments?
        if($count == 2) {
            // set document type
            $this->type = $id;
            // set document data
            $this->data = $type;
        }

        // we only have 1 argument?
        if($count == 1) {
            // set document type
            $this->type = $id;
        }

        print_r($this->getResource());
    }
}