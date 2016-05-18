![logo](http://eden.openovate.com/assets/images/cloud-social.png) Eden Elastic Search
====

- [Install](#install)
- [Introduction](#intro)
- [Low Level Request](#low-level)
- [Data Manipulation](#manipulation)
- [Searching Data](#search)

----

<a name="install"></a>
## Install

`composer install eden\elastic`

---

## Enable Eden

The following documentation uses `eden()` in its example reference. Enabling this function requires an extra step as descirbed in this section which is not required if you access this package using the following.

```
Eden\Mysql\Index::i();
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
    '_id'       => 1
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

---

<a name="search"></a>
## Searching Data
