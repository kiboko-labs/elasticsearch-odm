Elasticsearch ODM
=================

Elasticsearch Object Document Mapper is build for PHP 5.4.0+. It is a wrapper for the official [elasticsearch/elasticsearch-php](https://github.com/elastic/elasticsearch-php/tree/2.0) client.

In the current state of the library allows you to perform basic CRUD and search operations for documents.

Installation via Composer
-------------------------

The recommended method to install _Elasticsearch ODM_ is through [Composer](http://getcomposer.org).

```bash
$ composer require mosiyash/elasticsearch-odm:^0.1
```

Quickstart
----------

### Setup

For example, create ``User`` document.

```php
<?php
// src/Documents/User.php

namespace Project/Documents;

use Mosiyash\Elasticsearch\DocumentAbstract;

class User extends DocumentAbstract
{
    /**
     * @return UserRepository
     */
    public function getRepository()
    {
        return $this->di->get('project/documents:user_repository');
    }

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

Also, create repository class.

```php
<?php
// src/Documents/UserRepository.php

namespace Project/Documents;

use Elasticsearch\Client;
use Mosiyash\Elasticsearch\DocumentRepositoryAbstract;

class UserRepository extends DocumentRepositoryAbstract
{
    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->di->get('project/elasticsearch:client');
    }
    
    public function getIndex()
    {
        return 'project';
    }

    public function getType()
    {
        return 'users';
    }
    
    /**
     * @return User
     */
    public function newDocument()
    {
        return $this->di->get('tests/documents:user')->__invoke();
    }
}
```

``Elasticsearch ODM`` uses [Aura.Di](https://github.com/auraphp/Aura.Di/tree/2.x) to manage dependencies. Features of this library describes the project page.

So, start writing the bootstrap file.

```php
<?php
// your bootstrap

use Aura\Di\Container;
use Aura\Di\Factory;
use Elasticsearch\ClientBuilder;

$di = new Container(new Factory());

$di->set('project/elasticsearch:client', $di->lazy(function() {
    return ClientBuilder::create()->build();
}));

$di->setter['Mosiyash\Elasticsearch\DocumentAbstract']['setDi'] = $di;
$di->setter['Mosiyash\Elasticsearch\DocumentRepositoryAbstract']['setDi'] = $di;

$di->set('project/documents:user', $di->newFactory('Project\Documents\User'));
$di->set('project/documents:user_repository', $di->lazyNew('Project\Documents\UserRepository'));
```

Done.

### Base document feautures

```php
<?php

// Create new User object
$user = $di->get('project/documents:user')->__invoke();

$user->isNew();   // return true
$user->version;   // return null
$user->id;        // return null
$user->firstname; // return null
$user->lastname;  // return null

// Set firstname
$user->firstname = 'John';

$user->isNew();   // return true
$user->version;   // return null
$user->id;        // return null
$user->firstname; // return 'John'
$user->lastname;  // return null

// Save new User to Elasticsearch index
$user->save();

$user->isNew();   // return false
$user->version;   // return 1
$user->id;        // return 'AU9fUbhkk3sUEJdq9IQh'
$user->firstname; // return 'John'
$user->lastname;  // return null

// Update User
$user->id = 1;
$user->lastname = 'Doe';
$user->save();

$user->isNew();   // return false
$user->version;   // return 2
$user->id;        // return 1
$user->firstname; // return 'John'
$user->lastname;  // return 'Doe'

// Delete User
$user->delete();
```

### Base repository feautures

```php
<?php

use Mosiyash\Elasticsearch\QueryParams\Search;

// Create new User object
$user = $di->get('project/documents:user')->__invoke();
$user->id = 2;
$user->firstname = 'John';
$user->save();

// Lazy get repository object
$userRepository = $di->get('project/documents:user_repository');

// Find not exists user
$foundUser = $userRepository->find(1);
// null

// Find exists user
$foundUser = $userRepository->find(2);

$user->isNew();   // return false
$user->version;   // return 1
$user->id;        // return 2
$user->firstname; // return 'John'
$user->lastname;  // return null

// Find users by params
$params = new Search($userRepository);
$params->body = ['query' => ['match' => ['firstname' => 'John']]];
$result = $userRepository->findBy($params);

count($result);        // return 1
$result[0]->isNew();   // return false
$result[0]->version;   // return 1
$result[0]->id;        // return 2
$result[0]->firstname; // return 'John'
$result[0]->lastname;  // return null
```

Tests
=====

Run from project root:

```bash
$ phpunit
```
