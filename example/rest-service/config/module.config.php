<?php
return array (
    'controllers' => array (
        'invokables' => array (
            'PhlyRestfully\Resource' => 'PhlyRestfully\ResourceController'
        )
    ),

    'phlyrestfully' => array (

        'resources' => array (
            'ExampleService\Resource\Heartbeat' => array (
                'identifier' => 'ExampleService\Resource\Heartbeat\Model',
                'listener' => 'ExampleService\Resource\Heartbeat\Listener',
                'route_name' => 'example-service/heartbeat',
                'collection_name' => 'heartbeat',
                'collection_http_options' => array (
                    'GET',
                    'POST',
                    'OPTIONS'
                ),
                'resource_http_options' => array (
                    'GET',
                    'POST',
                    'OPTIONS'
                ),
                'resource_identifiers' => array (
                    'HeartbeatResource'
                ),
                'page_size' => 10,
                'accept_criteria' => array (
                    'PhlyRestfully\View\RestfulJsonModel' => array (
                        'application/json',
                        'text/json'
                    )
                )
            ),

            'ExampleService\Resource\VideoGame' => array (
                'identifier' => 'ExampleService\Resource\VideoGame\Model',
                'listener' => 'ExampleService\Resource\VideoGame\Listener',
                'route_name' => 'example-service/video-game',
                'collection_name' => 'video-game',
                'collection_http_options' => array (
                    'GET',
                    'OPTIONS'
                ),
                'resource_http_options' => array (
                    'GET',
                    'OPTIONS'
                ),
                'resource_identifiers' => array (
                    'VideoGameResource'
                ),
                'page_size' => 10,
                'accept_criteria' => array (
                    'PhlyRestfully\View\RestfulJsonModel' => array (
                        'application/json',
                        'text/json'
                    )
                )
            )
        ),

        'metadata_map' => array (
            'ExampleService\Resource\Heartbeat\Model' => array (
                'hydrator' => 'DataModeling\DataAccess\Model\Hydrator\ArrayHydrator',
                'identifier_name' => 'Id',
                'route' => 'example-service/heartbeat'
            ),
            'ExampleService\Resource\VideoGame\Model' => array (
                'hydrator' => 'DataModeling\DataAccess\Model\Hydrator\ArrayHydrator',
                'identifier_name' => 'GameId',
                'route' => 'example-service/video-game'
            )
        )
    ),

    // The following section is new and should be added to your file
    'router' => array (
        'routes' => array (

            // example-service
            'example-service' => array (
                'type' => 'Segment',
                'may_terminate' => true,
                'options' => array (
                    'route' => '/example-service[/]',
                    'controller' => 'ExampleService\Controller\Index'
                ),
                'child_routes' => array (
                    'heartbeat' => array (
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array (
                            'route' => 'heartbeat[/:id][/]',
                            'defaults' => array (
                                'controller' => 'ExampleService\Resource\Heartbeat',
                                'action' => ''
                            ),
                            'constraints' => array (
                                'id' => '[1-9][0-9]{0,20}'
                            )
                        )
                    ),
                    'video-game' => array (
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array (
                            'route' => 'video-game[/]',
                            'defaults' => array (
                                'controller' => 'ExampleService\Resource\VideoGame',
                                'action' => ''
                            ),
                            'constraints' => array (
                                'id' => '[1-9][0-9]{0,20}'
                            )
                        ),
                        'child_routes' => array (
                            'gameid' => array (
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => array (
                                    'route' => ':id[/]',
                                    'controller' => 'ExampleService\Resource\VideoGame',
                                    'constraints' => array (
                                        'id' => '[1-9][0-9]{0,20}'
                                    )
                                )
                            ),
                            'type' => array (
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => array (
                                    'route' => ':Type[/]',
                                    'controller' => 'ExampleService\Resource\VideoGame',
                                    'constraints' => array (
                                        'Type' => 'rpg|rts|action|all'
                                    )
                                )
                            )
                        )
                    )
                )
            )
        )
    )
);