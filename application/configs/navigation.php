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
                'label' => 'latest featured',
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
                'label' => 'my profile',
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
                'label' => 'my resume',
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
                'label' => 'my inbox',
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
                'label' => 'my bookings',
                'tag' => 'leftMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'booking',
                'action' => 'index',
                'resource'      => 'booking',
                'privilege'     => 'index',
            ),
            array
            (
                'label' => 'my lessons',
                'tag' => 'leftMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'lesson',
                'action' => 'index',
                'resource'      => 'lesson',
                'privilege'     => 'index',
            ),
            array
            (
                'label' => 'accounts/payments',
                'tag' => 'leftMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'payment',
                'action' => 'index',
                'resource'      => 'payment',
                'privilege'     => 'index',
            ),
            array
            (
                'label' => 'accounts/payments',
                'tag' => 'leftMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'payment',
                'action' => 'upgrade',
                'resource'      => 'payment',
                'privilege'     => 'upgrade',
            ),
            array
            (
                'label' => 'customer reviews',
                'tag' => 'leftMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'review',
                'action' => 'index',
                'resource'      => 'review',
                'privilege'     => 'index',
            ),
            array
            (
                'label' => 'my admin',
                'tag' => 'leftMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'admin',
                'action' => 'index',
                'resource'      => 'admin',
                'privilege'     => 'index',
            ),
			array(
                'label' => 'inbox',
                'tag' => 'messageMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'message',
                'action' => 'inbox',
                'resource'      => 'message',
                'privilege'     => 'inbox',
            ),
            array(
                'label' => 'archived',
                'tag' => 'messageMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'message',
                'action' => 'archived',
                'resource'      => 'message',
                'privilege'     => 'archived',
            ),
            array(
                'label' => 'sent',
                'tag' => 'messageMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'message',
                'action' => 'sent',
                'resource'      => 'message',
                'privilege'     => 'sent',
            ),
            array(
                'label' => 'trash',
                'tag' => 'messageMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'message',
                'action' => 'trash',
                'resource'      => 'message',
                'privilege'     => 'trash',
            ),
            array(
                'label' => 'scheduled',
                'tag' => 'lessonMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'lesson',
                'action' => 'index',
                'resource'      => 'lesson',
                'privilege'     => 'index',
            ),
            array(
                'label' => 'on the air',
                'tag' => 'lessonMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'lesson',
                'action' => 'join',
                'resource'      => 'lesson',
                'privilege'     => 'join',
            ),
            array(
                'label' => 'lesson details',
                'tag' => 'lessonMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'lesson',
                'action' => 'details',
                'resource'      => 'lesson',
                'privilege'     => 'details',
            ),

            array(
                'label' => 'Aimya Content',
                'tag' => 'adminMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'admin',
                'action' => 'index',
                'resource'      => 'admin',
                'privilege'     => 'index',
            ),
            array(
                'label' => 'Users',
                'tag' => 'adminMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'admin',
                'action' => 'users',
                'resource'      => 'admin',
                'privilege'     => 'users',
            ),
            array(
                'label' => 'Account payments',
                'tag' => 'adminMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'admin',
                'action' => 'payments',
                'resource'      => 'admin',
                'privilege'     => 'payments',
            ),
            array(
                'label' => 'Static pages',
                'tag' => 'adminMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'cms',
                'action' => 'index',
                'resource'      => 'cms',
                'privilege'     => 'index',
            ),
            array(
                'label' => 'Metrics',
                'tag' => 'adminMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'admin',
                'action' => 'metrics',
                'resource'      => 'admin',
                'privilege'     => 'metrics',
            ),
        )

    )
);