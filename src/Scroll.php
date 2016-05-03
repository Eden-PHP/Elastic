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
 * Search Scroll Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Scroll extends Search
{
    /**
     * Set default connection resource.
     *
     * @param   Eden\Elastic\Index
     */
    public function __construct(Index $connection)
    {
        // call parent construct
        return parent::__construct($connection);
    }
}
