Elasticsearch ODM
=================

Elasticsearch Object Document Mapper is build for PHP 5.4.0+. It is a wrapper for the official [elasticsearch/elasticsearch-php](https://github.com/elastic/elasticsearch-php/tree/2.0) client.

In the current state of the library allows you to perform basic CRUD and search operations for documents.

Installation via Composer
-------------------------

The recommended method to install _Elasticsearch ODM_ is through [Composer](http://getcomposer.org).

    ```bash
        composer require mosiyash/elasticsearch-odm:dev-master
    ```

Quickstart
----------

### Setup

For example, create new model and repository classes:

```php
// src/Documents/User.php

namespace Project/Documents;

use Mosiyash\ElasticSearch\DocumentAbstract;

class User extends DocumentAbstract
{
    /**
     * @var string
     * @isBodyParameter
     */
    public $firstname;

    /**
     * @var string
     * @isBodyParameter
     */
    public $lastname;
}
```

and

```php
// src/Documents/UserRepository.php

namespace Project/Documents;

use Mosiyash\ElasticSearch\DocumentRepositoryAbstract;

class UserRepository extends DocumentRepositoryAbstract
{
    public function getIndex()
    {
        return 'project';
    }

    public function getType()
    {
        return 'users';
    }
}
```

After that you need to supplement your bootstrap to make classes loadable.

```php
use Aura\Di\Container;
use Aura\Di\Factory;
use Elasticsearch\ClientBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$di = new Container(new Factory());

di->set('project/elasticsearch:client', function() {
    $logger = new Logger('elasticsearch');
    $logger->pushHandler('/path/to/file.log'), Logger::ERROR);

    $client = ClientBuilder::create();
    $client->setLogger($logger);

    return $client->build();
});

$this->di->setter['Mosiyash\ElasticSearch\DocumentAbstract']['setDi'] = $di;
$this->di->setter['Mosiyash\ElasticSearch\DocumentRepositoryAbstract']['setDi'] = $di;
$this->di->setter['Mosiyash\ElasticSearch\DocumentRepositoryAbstract']['setClientServiceName'] = 'project/elasticsearch:client';
```
