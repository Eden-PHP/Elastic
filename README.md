![logo](http://eden.openovate.com/assets/images/cloud-social.png) Eden Elastic Search
====

>**NOTE** Other advance Elasticsearch API functionalities are not yet documented.

---

- [Install](#install)
- [Introduction](#intro)
- [Low Level Request](#low-level-request)
- [Data Manipulation](#manipulation)
- [Searching Data](#search)
- [Query Builder](#query)
- [Model](#model)
- [Collection](#collection)
- [Contributing](#contributing)

---

<a name="install"></a>
## Install

`composer install eden\elastic`

---

## Enable Eden

The following documentation uses `eden()` in its example reference. Enabling this function requires an extra step as descirbed in this section which is not required if you access this package using the following.

```
Eden\Elastic\Index::i([HOST:PORT], [INDEX]);
```

When using composer, there is not an easy way to access functions from packages. As a workaround, adding this constant in your code will allow `eden()` to be available after. 

```
Eden::DECORATOR;
```

For example:

```
Eden::DECORATOR;

eden()->inspect('Hello World');
```

---

<a name="intro"></a>
## Introduction

*Eden's* Elasticsearch API is very simple and very easy to use, some of the most spectacular functionalities from *Eden's MySQL ORM* can be seen and use in this library, e.g Model, Collection and some of the known magical methods of *Eden MySQL*.

**Figure 1. ElasticSearch Resource Conenction**

```php
$client = eden('elastic', '[HOST:PORT]', '[INDEX]');
```

The above code show's how simple it is to define a resource/connection for Elasticsearch.

>**Note** Eden's Elasticsearch is only getting the host and index information
> upon initialization, no actual test request made to check if the host exists, this way we can avoid overhead.

---

<a name="low-level"></a>
## Low Level Request
**Figure 2. Low level request using Eden\Elastic\Resource class**

If you want to do some basic to advance request that is not provided
in Eden Elasticsearch API, you are always free to access the low level
resource class.

```php
$response = $client
// set the host (optional)
->setHost('http://localhost:9200')
// set the index name
->setIndex('twitter')
// set the index type
->setType('tweet')
// set the endpoint
->setEndpoint('_create')
// set the request body
->setBody(array(
    'user'      => 'Kimchy',
    'message'   => 'Eden Elasticsearch API!',
    'active'    => 1
))
// set the parameter
->setParam('routing', 'Charles')
// set the method
->setMethod(\Eden\Elastic\Index::PUT)
// set custom request header
->setHeaders('Content-Type', 'application/json')
// send the request
->send();
```

---

<a name="manipulation"></a>
## Data Manipulation

Basic Create/Index, Remove, Update, Delete is very easy to access, the following example will show how we could do such simple task in a simple way.

**Figure 3. Indexing Single Data**

```php
$tweet = array(
    '_id'       => 1,
    'user'      => 'Charles',
    'message'   => 'Elasticsearch is cool!'
);

$response = $client->insert($tweet, 'tweet');
```

**Figure 4. Indexing Multiple Data**

```php
$tweets = array(
    array(
        '_id'       => 1,
        'user'      => 'Charles',
        'message'   => 'Elasticsearch is cool!'
    ),
    array(
        '_id'       => 2,
        'user'      => 'Kimchy',
        'message'   => 'Yes @czamora it is cool!'
    )
);

$response = $client->insertRows($tweets, 'tweet');
```

**Figure 5. Update Single Data by id**

```php
$user = array(
    '_id'       => 45,
    'firstname' => 'Charles',
    'lastname'  => 'Zamora'
);

$response = $client->update($user, 'user');
```

**Figure 6. Update Multiple Data by id**

```php
$tweets = array(
    array(
        '_id'       => 1,
        'active'    => 0
    ),
    array(
        '_id'       => 2,
        'active'    => 1
    )
);

$response = $client->updateRows($tweets, 'tweet');
```

**Figure 7. Remove Single Data by id**

```php
$tweet = array('_id' => 1);

$response = $client->remove($tweet, 'tweet');
```

**Figure 8. Remove Multiple Data by id**

```php
$tweets = array(array('_id' => 1), array('_id' => 2));

$response = $client->removeRows($tweets, 'tweet');
```

**Figure 9. Get Single Data by id**

```php
$response = $client->get(1, 'tweet');
```

**Figure 10. Get Multiple Documents by id**

```php
$tweets = array(array('_id' => 1), array('_id' => 2));

$response = $client->getRows($tweets, 'tweet');
```

>**NOTE** The _id field is very important because it will be the basis of all the basic crud functionality.

---

<a name="search"></a>
## Searching Data

The code below will show how simple it is to search for an indexed data using
Eden Elasticsearch Search class.

**Figure 10. Basic Searching using Search class**

```php
$user = $client
->search('tweet')
->filterByUser('Charles')
->sortByActive('desc')
->setStart(0)
->setRange(1)
->getRows();
```

Most of the magical *Eden MySQL's search functionality can be accessed in search class e.g filterBy, sortBy, setStart, setRange etc.

**Other useful methods**

- `->getRow()` - Get the results limit by 1
- `->getCollection()` - Get the results as a collection
- `->getModel()` - Get the results as a model
- `->getQuery()` - Returns the current search query
- `->getTotal()` - Returns the total records of search query

> **NOTE** The magic methods from the search class is using the Query Builder class internally, so if you are going to do something like `->setAnything(value)` the anything key will be added to the query builder instance inside the search instance together with it's value. Another thing to consider is that the `->getModel()` method will return a single Eden\Elastic\Model instance the same as `->getCollection()` that will return the Eden\Elastic\Collection instance so that way we can make the search functionality more flexible.

---

<a name="query"></a>
## Query Builder

Elasticsearch's Query DSL is a complex query data structure, in Eden Elasticsearch we make this more simple using the Query Builder class. The query builder class contains simple API
functionalities that will help you build a simple to complex query data structure. below is an example on how it works.

**Figure 11. Building Complex queries using Query Builder**

```php
$query = $client
->query()
->setTree('query.bool.must.term.user', 'kimchy')
->setTree('query.bool.should.term.user', 'charles')
->setTree('query.filter.or.0.term.active', 1)
->addTree('query.filter.or', array(
    'term' => array('user' => 'Charles')
))
->getQuery();
```

**Query Output:**
```
Array
(
    [query] => Array
        (
            [bool] => Array
                (
                    [must] => Array
                        (
                            [term] => Array
                                (
                                    [user] => kimchy
                                )

                        )

                    [should] => Array
                        (
                            [term] => Array
                                (
                                    [user] => charles
                                )

                        )

                )

            [filter] => Array
                (
                    [or] => Array
                        (
                            [0] => Array
                                (
                                    [term] => Array
                                        (
                                            [active] => 1
                                        )

                                )

                            [1] => Array
                                (
                                    [term] => Array
                                        (
                                            [user] => Charles
                                        )

                                )

                        )

                )

        )

)
```

**Figure 12. Using the Query Builder output in search class**

```php
$query = $client
->setTree('query.bool.term.user', 'kimchy')
->setTree('sort.0.active.order', 'desc')
->getQuery();

$response = $client
->search('tweet')
->setBody($query)
->getRows();
```

---

<a name="model"></a>
## Model

The model class of Eden Elasticsearch is derived from the awesome *Eden MySQL Model* class, most of the functionality is the same as how the *Eden MySQL Model* works except the fact that setting the '_id' field has it's own functionality. Let's take a look on how it works.


**Figure 13. Basic Elasticsearch Model**

```php
$user = array(
    '_id'       => 10,
    'user'      => 'Foo',
    'message'   => 'I am foo!'
);

$response = $client
->model($user)
->save('tweet')
->get(false);
```

**Figure 14. Setting Model data magically**

```php
$response = $client
->model()
->setId(10) // IMPORTANT
->setUser('charles')
->setMessage('This is created using a model!')
->save('tweet')
->get(false);
```

**Other useful methods**

- `->insert('tweet')` - No upsert, strict data insertion only
- `->remove('tweet')` - Remove the model from the index type tweet
- `->update('tweet')` - Update the model from the index type tweet
- `->get(false)`        - Returns the model

---

<a name="collection"></a>
## Collection

The collection class is also derived from the awesome *Eden MySQL Collection* class, the collection class do exactly what the models can do, except the fact that it can process or manipulate bulk or collection of models in a simple manner. Below will show how collection works with Elasticsearch.

**Figure 15. Basic Elasticsearch Collection**

```php
$tweets = array(
  array(
      '_id'         => 10,
      'user'        => 'Foo',
      'message'     => 'I am foo!'
  ),
  array(
      '_id'         => 11,
      'user'        => 'Foo 2',
      'message'     => 'I am foo 2!'
  )
);

$collection = $client
->collection($tweets)
->save('tweet');
```

**Other useful methods**
 
- `->insert('tweet')` - Insert the collection of model to index type tweet, no upserts, just strict data insertion
- `->update('tweet')` - Updates the collection of model from index type tweet
- `->remove('tweet')` - Remove the collection of model from index type tweet
- `->add(array)`       - Add an array or model to the collection
- `->set(array)`      - Set the collection of array or model
- `->get(false)`      - Get the collection data

---

<a name="contributing"></a>
# Contributing to Eden

Contributions to *Eden* are following the Github work flow. Please read up before contributing.

## Setting up your machine with the Eden repository and your fork

1. Fork the repository
2. Fire up your local terminal create a new branch from the `v4` branch of your 
fork with a branch name describing what your changes are. 
 Possible branch name types:
    - bugfix
    - feature
    - improvement
3. Make your changes. Always make sure to sign-off (-s) on all commits made (git commit -s -m "Commit message")

## Making pull requests

1. Please ensure to run `phpunit` before making a pull request.
2. Push your code to your remote forked version.
3. Go back to your forked version on GitHub and submit a pull request.
4. An Eden developer will review your code and merge it in when it has been classified as suitable.
