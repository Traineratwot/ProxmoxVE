ProxmoxVE API Client
====================

![Proxmox Logo](assets/proxmox-full-lockup-color.png)

This **PHP 8+** library allows you to interact with your Proxmox server via API.

Installation
------------

Recommended installation is using [Composer], if you do not have [Composer] what are you waiting?

In the root of your project execute the following:

```sh
$ composer require triopsi/proxmoxve ~5.0
```

Or add this to your `composer.json` file:

```json
{
    "require": {
        "triopsi/proxmoxve": "~5.0"
    }
}
```

Then perform the installation:
```sh
$ composer install --no-dev
```


Usage
-----

```php
<?php

// Require the autoloader
require_once 'vendor/autoload.php';

// Use the library namespace
use ProxmoxVE\Proxmox;

// Create your credentials array
$credentials = [
    'hostname' => 'proxmox.server.com',  // Also can be an IP
    'username' => 'root',
    'password' => 'secret',
];

// realm and port defaults to 'pam' and '8006' but you can specify them like so
$credentials = [
    'hostname' => 'proxmox.server.com',
    'username' => 'root',
    'password' => 'secret',
    'realm' => 'pve',
    'port' => '9009',
];

// Then simply pass your credentials when creating the API client object.
$proxmox = new Proxmox($credentials);

$allNodes = $proxmox->get('/nodes');

print_r($allNodes);
```


Sample output:

```php
Array
(
    [data] => Array
        (
            [0] => Array
                (
                    [disk] => 2539465464
                    [cpu] => 0.031314446882002
                    [maxdisk] => 30805066770
                    [maxmem] => 175168446464
                    [node] => mynode1
                    [maxcpu] => 24
                    [level] =>
                    [uptime] => 139376
                    [id] => node/mynode1
                    [type] => node
                    [mem] => 20601992182
                )

        )

)
```

Using the MailGateway API
-------------------------

This library can be used to communicate with the [Proxmox MailGateway API](https://pmg.proxmox.com/pmg-docs/api-viewer/index.html) as well, by simply changing the `system` entry in the credentials array.

```php
<?php

$credentials = [
    'hostname' => 'proxmox.server.com',  // Also can be an IP
    'username' => 'root',
    'password' => 'secret',
    'system' => 'pmg', // Defaults to 'pve' for the PVE API. Use 'pmg' for the PMG API.
];

```

Want to know more nifty tricks?
-------------------------------

Checkout our [wiki](https://github.com/triopsi/ProxmoxVE/wiki).


Docs
----

On your proxmox client object you can use `get()`, `create()`, `set()` and `delete()` functions for all resources specified at [PVE2 API Documentation], params are passed as the second parameter in an associative array.

**What resources or paths can I interact with and how?**

In your proxmox server you can use the [pvesh CLI Tool](http://pve.proxmox.com/wiki/Proxmox_VE_API#Using_.27pvesh.27_to_access_the_API) to manage all the pve resources, you can use this library in the exact same way you would use the pvesh tool. For instance you could run `pvesh` then, as the screen message should say, you can type `help [path] [--verbose]` to see how you could use a path and what params you should pass to it. Be sure to [read about the pvesh CLI Tool](http://pve.proxmox.com/wiki/Proxmox_VE_API#Using_.27pvesh.27_to_access_the_API) at the [Proxmox wiki].

**How does the Proxmox API works?**

Consult the [ProxmoxVE API] article at the [Proxmox wiki].

**I feel you're hiding some cool tricks! I need more docs!**

See the project's [wiki](https://github.com/triopsi/ProxmoxVE/wiki) for a more
detailed documentation, it's all in there I swear it... I think.


License
-------

This project is released under the MIT License. See the bundled [LICENSE] file for details.
