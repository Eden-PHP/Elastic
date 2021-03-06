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
 * The base class for any class handling exceptions. Exceptions
 * allow an application to custom handle errors that would
 * normally let the system handle. This exception allows you to
 * specify error levels and error types. Also using this exception
 * outputs a trace (can be turned off) that shows where the problem
 * started to where the program stopped.
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Exception extends \Eden\Core\Exception
{
    /**
     * Error template
     *
     * @const string
     */
    const NOT_SUB_MODEL = 'Class %s is not a child of Eden\\Model\\Index';

    /**
     * Error template
     *
     * @const string
     */
    const NOT_SUB_COLLECTION = 'Class %s is not a child of Eden\\Collection\\Index';
}
