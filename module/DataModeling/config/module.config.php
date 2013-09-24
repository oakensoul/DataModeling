<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */

/**
 * To add a new route...
 *
 * Note: The "home" route comes from the oakensoul/Cornerstone module
 *
 * @see https://github.com/oakensoul/Cornerstone
 *
 * http://framework.zend.com/manual/2.2/en/modules/zend.mvc.routing.html
 */
$router = array ();

/**
 * To add a new controller...
 *
 * In this case, we're going to overload the default Application\Controller\Index
 * provided by Cornerstone so that we have our own, and our own documentation
 *
 * http://framework.zend.com/manual/2.2/en/user-guide/routing-and-controllers.html
 */
$controllers = array (
    'invokables' => array (
        'Application\Controller\Index' => 'DataModeling\Http\Controller\IndexController'
    )
);

/**
 * Working with Views
 *
 * http://framework.zend.com/manual/2.2/en/modules/zend.view.quick-start.html
 */
$view_manager = array (
    'template_map' => array(
        'application/index/index' => __DIR__ . '/../view/application/index/index.phtml'
    ),
    'template_path_stack' => array (
        __DIR__ . '/../view'
    )
);

$config = array ();
$config['router'] = $router;
$config['controllers'] = $controllers;
$config['view_manager'] = $view_manager;

return $config;