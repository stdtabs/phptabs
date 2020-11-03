Yousign API client
==================

[![Build Status](https://api.travis-ci.org/landrok/yousign-api.svg?branch=master)](https://travis-ci.org/landrok/yousign-api)
[![Maintainability](https://api.codeclimate.com/v1/badges/cad81750c32c5346ac6b/maintainability)](https://codeclimate.com/github/landrok/yousign-api/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/cad81750c32c5346ac6b/test_coverage)](https://codeclimate.com/github/landrok/yousign-api/test_coverage)

Yousign API client is a wrapper for the Yousign API v2 in PHP.

Its purpose is to use this API without having to write the HTTP calls
yourself and then to retrieve the returned data through an object model.

If you still want to make HTTP calls to check the API responses, this is
possible thanks to the low-level calls.

It provides several an API wrapper and shortcut methods.

All the API calls are wrapped into an object model. All features are
implemented, it aims to be a full-featured client.

All subsequent types (Member, Procedure, File, etc...) are implemented
too.

[See the full documentation](https://yousign-api.readthedocs.io/) or
an overview below.

Table of contents
=================

- [Requirements](#requirements)
- [Install](#install)
- [Quick start](#quick-start)
- [Basic mode](#basic-mode)
- [Advanced mode](#advanced-mode)

________________________________________________________________________

Requirements
------------

- PHP 7.1+
- You have to create your account on Yousign platform to get an API
token before using this library.

________________________________________________________________________

Install
-------

```sh
composer require landrok/yousign-api
```

________________________________________________________________________

Quick start
-----------

In this example, we will get all users in staging mode.

```php
use Yousign\YousignApi;

/*
 * token
 */
$token = '123456789';

$yousign = new YousignApi($token);

$users = $yousign->getUsers();

```

Good news, your token is available.


________________________________________________________________________

Responses and data
------------------

All API responses are converted into objects that are iterable when it's
a collection (ie a list of users) or an item (an user itself).

### Dump data

You can use toArray() method to dump all data as a PHP array.

```php

print_r(
    $users->toArray()
);

```

### Iterate over a list

You can iterate over all items of a collection.

```php

foreach ($users as $user) {
    /*
     * For each User model, some methods are available
     */

    // toArray(): to get all property values
    print_r($user->toArray());

    // get + property name
    echo PHP_EOL . "User.id=" . $user->getId();

    // property (read-only)
    echo PHP_EOL . "User.id=" . $user->id;

    // Some properties are models that you can use the same way
    echo PHP_EOL . "User.Group.id=" . $user->getGroup()->getId();
    echo PHP_EOL . "User.Group.id=" . $user->group->id;

    // Some properties are collections that you can iterate
    foreach ($user->group->permissions as $index => $permission) {
        echo PHP_EOL . "User.Group.Permission.name=" . $permission->getName();
    }

    // At any level, you can call a toArray() to dump the current model
    // and its children
    echo PHP_EOL . "User.Group=\n";
    print_r($user->group->toArray());
    echo PHP_EOL . "User.Group.Permissions=\n";
    print_r($user->group->permissions->toArray());
}

```
________________________________________________________________________

Basic Mode
----------

Let's create your first signature procedure in basic mode.

In this example, we will accomplish this mode with low-level
features.

```php
use Yousign\YousignApi;

/*
 * Token
 */
$token = '123456789';

/*
 * Production mode
 */
$production = false;

/*
 * Instanciate API wrapper
 */
$yousign = new YousignApi($token, $production);

/*
 * 1st step : send a file
 */
$file = $yousign->postFile([
    'name'    => 'My filename.pdf',
    'content' => base64_encode(
        file_get_contents(
            dirname(__DIR__, 2) . '/tests/samples/test-file-1.pdf'
        )
    )
]);

/*
 * 2nd step : create the procedure
 */
$procedure = $yousign->postProcedure([
    "name"        => "My first procedure",
    "description" => "Awesome! Here is the description of my first procedure",
    "members"     => [
        [
            "firstname" => "John",
            "lastname" => "Doe",
            "email" => "john.doe@yousign.fr",
            "phone" => "+33612345678",
            "fileObjects" => [
                [
                    "file" => $file->getId(),
                    "page" => 2,
                    "position" => "230,499,464,589",
                    "mention" => "Read and approved",
                    "mention2" => "Signed by John Doe"
                ]
            ]
        ]
    ]
]);

// toJson() supports all PHP json_encode flags
echo $procedure->toJson(JSON_PRETTY_PRINT);
```

When the procedure is created, you can retrieve all the data with the
getters or dump all data with `toJson()` and `toArray()` methods.

It would output something like:

```json
{
    "id": "\/procedures\/XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
    "name": "My first procedure",
    "description": "Awesome! Here is the description of my first procedure",
    "createdAt": "2018-12-01T11:49:11+01:00",
    "updatedAt": "2018-12-01T11:49:11+01:00",
    "finishedAt": null,
    "expiresAt": null,
    "status": "active",
    "creator": null,
    "creatorFirstName": null,
    "creatorLastName": null,
    "workspace": "\/workspaces\/XXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
    "template": false,
    "ordered": false,
    "parent": null,
    "metadata": [],
    "config": [],
    "members": [
        {
            "id": "\/members\/XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
            "user": null,
            "type": "signer",
            "firstname": "John",
            "lastname": "Doe",
            "email": "john.doe@yousign.fr",
            "phone": "+33612345678",
            "position": 1,
            "createdAt": "2018-12-01T11:49:11+01:00",
            "updatedAt": "2018-12-01T11:49:11+01:00",
            "finishedAt": null,
            "status": "pending",
            "fileObjects": [
                {
                    "id": "\/file_objects\/XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
                    "file": {
                        "id": "\/files\/XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
                        "name": "The best name for my file.pdf",
                        "type": "signable",
                        "contentType": "application\/pdf",
                        "description": null,
                        "createdAt": "2018-12-01T11:36:20+01:00",
                        "updatedAt": "2018-12-01T11:49:11+01:00",
                        "sha256": "bb57ae2b2ca6ad0133a699350d1a6f6c8cdfde3cf872cf526585d306e4675cc2",
                        "metadata": [],
                        "workspace": "\/workspaces\/XXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
                        "creator": null,
                        "protected": false,
                        "position": 0,
                        "parent": null
                    },
                    "page": 2,
                    "position": "230,499,464,589",
                    "fieldName": null,
                    "mention": "Read and approved",
                    "mention2": "Signed by John Doe",
                    "createdAt": "2018-12-01T11:49:11+01:00",
                    "updatedAt": "2018-12-01T11:49:11+01:00",
                    "parent": null,
                    "reason": "Signed by Yousign"
                }
            ],
            "comment": null,
            "notificationsEmail": [],
            "operationLevel": "custom",
            "operationCustomModes": [
                "sms"
            ],
            "operationModeSmsConfig": null,
            "parent": null
        }
    ],
    "subscribers": [],
    "files": [
        {
            "id": "\/files\/XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
            "name": "My filename.pdf",
            "type": "signable",
            "contentType": "application\/pdf",
            "description": null,
            "createdAt": "2018-12-01T11:36:20+01:00",
            "updatedAt": "2018-12-01T11:49:11+01:00",
            "sha256": "bb57ae2b2ca6ad0133a699350d1a6f6c8cdfde3cf872cf526585d306e4675cc2",
            "metadata": [],
            "workspace": "\/workspaces\/XXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
            "creator": null,
            "protected": false,
            "position": 0,
            "parent": null
        }
    ],
    "relatedFilesEnable": false,
    "archive": false,
    "archiveMetadata": [],
    "fields": [],
    "permissions": []
}
```
________________________________________________________________________

Advanced Mode
-------------

Here is how to create a procedure in 5 steps with the advanced mode.

```php
use Yousign\YousignApi;

/*
 * Token
 */
$token = '123456789';

/*
 * Production mode
 */
$production = false;

/*
 * Instanciate API wrapper
 */
$yousign = new YousignApi($token, $production);

/*
 * Step 1 - Create your procedure
 */
$procedure = $yousign->postProcedure([
    "name"        => "My first procedure",
    "description" => "Description of my procedure with advanced mode",
    "start"       => false,
]);

/*
 * Step 2 - Add the files
 */
$file = $yousign->postFile([
    'name'    => 'Name of my signable file.pdf',
    'content' => base64_encode(
        file_get_contents(
            dirname(__DIR__, 2) . '/tests/samples/test-file-1.pdf'
        )
    ),
    'procedure' => $procedure->getId(),
]);

/*
 * Step 3 - Add the members
 */
$member = $yousign->postMember([
    "firstname"     => "John",
    "lastname"      => "Doe",
    "email"         => "john.doe@yousign.fr",
    "phone"         => "+33612345678",
    "procedure"     => $procedure->getId(),
]);

/*
 * Step 4 - Add the signature images
 */
$fileObject = $yousign->postFileObject([
    "file"      => $file->getId(),
    "member"    => $member->getId(),
    "position"  => "230,499,464,589",
    "page"      => 2,
    "mention"   => "Read and approved",
    "mention2"  => "Signed By John Doe"
]);

 /*
  * Step 5 - Start the procedure
  */
$procedure = $yousign->putProcedure(
    $procedure->getId(), [
        "start" => true,
    ]
);


echo $procedure->toJson(JSON_PRETTY_PRINT);
```

In step 3, you may add several members.

In step 4, you may add one or more signature images for each one.

________________________________________________________________________

More
----

- [See the full documentation](https://yousign-api.readthedocs.io/)

- To discuss new features, make feedback or simply to share ideas, you
  can contact me on Mastodon at
  [https://cybre.space/@landrok](https://cybre.space/@landrok)

- Create an account and an API token on
  [Yousign Sandbox sign-up](https://staging-auth.yousign.com/pre-signup)

- [Official API manual](https://dev.yousign.com/?version=latest)

