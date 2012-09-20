<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Mptests\Controller\Test' => 'Mptests\Controller\TestController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'simulador' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/test[/:sistema][/:modulo][/:action][/:id]',
                    'constraints' => array(
                        'action'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'sistema'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'modulo'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Mptests\Controller\Test',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'album' => __DIR__ . '/../view',
        ),
    ),
);