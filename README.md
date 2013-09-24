Data Modeling
===========================

Introduction
------------
...

Requirements
------------
As noted in the composer file, this module requires `zendframework/zendframework` 2.2 or higher as well as `oakensoul/Cornerstone`.

Generate a Configuration File
------------------------------------------
Create a config file in config/autoload/<environment>.json. Use the example_environment.json.dist file as a base if needed.

Generate Your Virtual Host
--------------------------
To generate a virtual host you need to run the packaged cli script. Note, this depends on the oakensoul/Cornerstone module to run.
```
./cornerstone.php application build-vhost --env=<environment>
```

Update the `module` Folder
--------------------------
If you are creating a new module using this Application skeleton, simply rename the ApplicationSkeleton folder to match the
name of your new module. Note, you will need to update various references to it throughout the code.

```
$ cd module/
$ mv ApplicationSkeleton NewModuleName
```

Namespace Updates for `Module.php` and `IndexController.php`
```
namespace ApplicationSkeleton;
```

becomes

```
namespace NewModuleName;
```

Update Configuration Files
--------------------------
### config/application.config.php

Update the `$modules` array and change `Application` to your module name (e.g., NewModuleName).
```
$modules = array (
    'Cornerstone',
    'ApplicationSkeleton'
);
$application_config['modules'] = $modules;
```

becomes

```
$modules = array (
    'Cornerstone',
    'NewModuleName'
);
$application_config['modules'] = $modules;
```

### config/autoload/global.php
Change the value of the `Region` key to match your module slug
Change the value of the `Domain` key to match your domain

For example, when I test any of my modules I tend to set them up as:
  http://module-name.oakensoul.com/

```
return array (
    'Installation' => array (
        'Vhost' => array (
            'Server' => array (
                'Domain' => 'oakensoul',
                'Region' => 'skeleton-application.'
            )
        )
    )
);
```

becomes

```
return array (
    'Installation' => array (
        'Vhost' => array (
            'Server' => array (
                'Domain' => 'oakensoul',
                'Region' => 'module-name.'
            )
        )
    )
);
```

Or, update it to match your new sites region and domain:
```
return array (
    'Installation' => array (
        'Vhost' => array (
            'Server' => array (
                'Domain' => 'oakensoul',
                'Region' => 'www.'
            )
        )
    )
);
```

### module/<module name>/config/module.config.php
#### Add routes

Add any routes you would like as necessary.

#### Update Invokables
```
$controllers = array (
    'invokables' => array (
        'Application\Controller\Index' => 'ApplicationSkeleton\Controller\IndexController'
    )
);
```

becomes

```
$controllers = array (
    'invokables' => array (
        'Application\Controller\Index' => 'NewModuleName\Controller\IndexController'
    )
);
```

Note: In the above example, we are overloading the home route (Application\Controller\Index). Likely you won't
wnat to do that unless you're creating an actual site / new homepage. If you're working with a module, typically
you would create entirely new routes.

Add additional invokables as necessary.

#### Update Views (If Needed)
You typically will not need to update the view data. If necessary, you would place your changes in this block:
```
$view_manager = array (
    'template_path_stack' => array (
        __DIR__ . '/../view'
    )
);
```

Update/Create Automated Tests
-----------------------------
All PHPUnit based tests are located under the `test/phpunit` directory with CasperJS tests
located under the `test/caspserjs` directory. Update or create tests as you develop your module.

Documentation
-------------
Please update your module documentation, everyone loves great documentation!

LICENSE
=======

This module is licensed using the BSD 2-Clause License:

```
Copyright (c) 2013 Robert Gunnar Johnson Jr.
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

- Redistributions of source code must retain the above copyright notice, this
  list of conditions and the following disclaimer.
- Redistributions in binary form must reproduce the above copyright notice,
  this list of conditions and the following disclaimer in the documentation
  and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
```