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
                'label' => 'Latest Featured',
                'tag' => 'leftMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'account',
                'action' => 'features',
                'resource'      => 'account',
                'privilege'     => 'features',
            ),
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
                'label' => 'My resume',
                'tag' => 'leftMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'resume',
                'action' => 'index',
                'resource'      => 'resume',
                'privilege'     => 'index',
            ),
            array
            (
                'label' => 'My Inbox',
                'tag' => 'leftMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'message',
                'action' => 'inbox',
                'resource'      => 'message',
                'privilege'     => 'inbox',
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