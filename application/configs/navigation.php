<?php

return array(
    array
    (
        'label' => 'Home',
        'id' => 'home',
        'module' => 'default',
        'controller' => 'index',
        'action' => 'index',
        'route' => 'default',
        'pages' => array
        (
            array
            (
                'label' => 'Profile',
                'tag' => 'leftMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'account',
                'action' => 'index',
                'resource'      => 'account',
                'privilege'     => 'index',
            ),
            array
            (
                'label' => 'Lesson',
                'tag' => 'leftMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'lesson',
                'action' => 'index',
                'resource'      => 'lesson',
                'privilege'     => 'index',
            )
        )
    )
);