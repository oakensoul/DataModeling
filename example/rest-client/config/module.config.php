<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/DataModeling for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
$config = array ();

/**
 * To add a new route...
 *
 * Note: The "home" route comes from the oakensoul/Cornerstone module (or the site this module is composed into)
 *
 * @see https://github.com/oakensoul/Cornerstone http://framework.zend.com/manual/2.2/en/modules/zend.mvc.routing.html
 */
$config['console'] = array (
    'router' => array ()
);

$config['ExampleDataAPI']['routes'] = array (
    'game' => array (
        'type' => 'Segment',
        'may_terminate' => false,
        'options' => array (
            'route' => '/standard/JSON/Game[/]'
        ),
        'child_routes' => array (
            'gametype' => array (
                'type' => 'Segment',
                'may_terminate' => true,
                'options' => array (
                    'route' => ':Type',
                    'constraints' => array (
                        'Type' => 'rpg|rts|action|all'
                    ),
                    'defaults' => array (
                        'type' => 'current'
                    )
                )
            )
        )
    )
);

/**
 * To add a new controller...
 *
 * http://framework.zend.com/manual/2.2/en/user-guide/routing-and-controllers.html
 */
$config['controllers'] = array (
    'invokables' => array ()
);

/**
 * Working with Views
 *
 * http://framework.zend.com/manual/2.2/en/modules/zend.view.quick-start.html
 */
$config['view_manager'] = array (
    'template_map' => array (
        'application/index/index' => __DIR__ . '/../view/application/index/index.phtml'
    ),
    'template_path_stack' => array (
        __DIR__ . '/../view'
    )
);

/**
 * Configuration settings for the Service
 */
$config['ExampleService'] = array (
    'Location' => 'http://api.games.example.com/',
    'APIKey' => '000dddd1-a14c-5656-8e8e-ab0c12345678'
);

return $config;