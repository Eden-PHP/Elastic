<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Eden\Elastic\Search;

use Eden\Elastic\Argument as Argument;
use Eden\Elastic\Exception as Exception;
use Eden\Elastic\Index as Index;
use Eden\Elastic\Search as Search;

/**
 * Search Template Class
 *
 * @vendor   Eden
 * @package  Elastic
 * @author   Charles Zamora <czamora@openovate.com>
 * @standard PSR-2
 */
class Template extends Search
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

    /**
     * Retrieve a template.
     *
     * @param   string | null
     * @return  array
     */
    public function getTemplate($template = null)
    {
        // Argument test
        Argument::i()->test(1, 'string', 'null');

        // get body
        $body = $this->getQuery();

        // set endpoint
        $endpoint = '_search/template';

        // if template name is set
        if(!is_null($template)) {
            $endpoint = '_search/template/' . $template;
        }

        // get original index
        $index = $this->connection->getIndex();
        // get original type
        $type  = $this->connection->getType();

        // remove index and type
        $this->connection->setIndex('')->setType('');

        // get response
        $response = $this->connection
        // set endpoint
        ->setEndpoint($endpoint)
        // send request
        ->send();

        // bring back the index and type
        $this->connection->setIndex($index)->setType($type);

        return $response;
    }

    /**
     * Set / Create a search template.
     *
     * @param   string
     * @return  array
     */
    public function createTemplate($template)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // get query body
        $body = $this->getQuery();

        // set endpoint
        $endpoint = '_search/template/' . $template;

        // get original index
        $index = $this->connection->getIndex();
        // get original type
        $type  = $this->connection->getType();

        // remove index and type
        $this->connection->setIndex('')->setType('');

        // get response
        $response = $this->connection
        // require the body
        ->requireBody()
        // set body
        ->setBody($body)
        // set endpoint
        ->setEndpoint($endpoint)
        // set method to post
        ->setMethod(Index::POST)
        // send request
        ->send();

        // bring back the index and type
        $this->connection->setIndex($index)->setType($type);

        return $response;
    }

    /**
     * Delete a search template.
     *
     * @param   string
     * @return  array
     */
    public function deleteTemplate($template)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // get body
        $body = $this->getQuery();

        // set endpoint
        $endpoint = '_search/template/' . $template;

        // get original index
        $index = $this->connection->getIndex();
        // get original type
        $type  = $this->connection->getType();

        // remove index and type
        $this->connection->setIndex('')->setType('');

        // get response
        $response = $this->connection
        // set endpoint
        ->setEndpoint($endpoint)
        // set method to delete
        ->setMethod(Index::DELETE)
        // send request
        ->send();

        // bring back the index and type
        $this->connection->setIndex($index)->setType($type);

        return $response;
    }

    /**
     * Renders a search template in a response.
     *
     * @param   string
     * @return  array
     */
    public function renderTemplate($template = null)
    {
        // Argument test
        Argument::i()->test(1, 'string');

        // get query body
        $body = $this->getQuery();

        // set endpoint
        $endpoint = '_render/template';

        // if template is not null
        if(!is_null($template)) {
            // set endpoint
            $endpoint = '_render/template/' . $template;
        }

        // get original index
        $index = $this->connection->getIndex();
        // get original type
        $type  = $this->connection->getType();

        // remove index and type
        $this->connection->setIndex('')->setType('');

        // get response
        $response = $this->connection
        // require the body
        ->requireBody()
        // set body
        ->setBody($body)
        // set endpoint
        ->setEndpoint($endpoint)
        // send request
        ->send();

        // bring back the index and type
        $this->connection->setIndex($index)->setType($type);

        return $response;
    }
}
