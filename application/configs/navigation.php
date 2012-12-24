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
                'label' => 'LATEST FEATURED',
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
                'label' => 'MY PROFILE',
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
                'label' => 'MY RESUME',
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
                'label' => 'MY INBOX',
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
                'label' => 'MY BOOKING',
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
                'label' => 'MY LESSONS',
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
                'label' => 'ACCOUNTS/PAYMENTS',
                'tag' => 'leftMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'payment',
                'action' => 'index',
                'resource'      => 'payment',
                'privilege'     => 'index',
            ),
			array(
                'label' => 'Inbox',
                'tag' => 'messageMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'message',
                'action' => 'inbox',
                'resource'      => 'message',
                'privilege'     => 'inbox',
            ),
            array(
                'label' => 'Archived',
                'tag' => 'messageMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'message',
                'action' => 'archived',
                'resource'      => 'message',
                'privilege'     => 'archived',
            ),
            array(
                'label' => 'Sent',
                'tag' => 'messageMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'message',
                'action' => 'sent',
                'resource'      => 'message',
                'privilege'     => 'sent',
            ),
            array(
                'label' => 'Trash',
                'tag' => 'messageMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'message',
                'action' => 'trash',
                'resource'      => 'message',
                'privilege'     => 'trash',
            ),
            array(
                'label' => 'Scheduled',
                'tag' => 'lessonMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'lesson',
                'action' => 'index',
                'resource'      => 'lesson',
                'privilege'     => 'index',
            ),
            array(
                'label' => 'On The Air',
                'tag' => 'lessonMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'lesson',
                'action' => 'join',
                'resource'      => 'lesson',
                'privilege'     => 'join',
            ),
            array(
                'label' => 'Lesson Details',
                'tag' => 'lessonMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'lesson',
                'action' => 'details',
                'resource'      => 'lesson',
                'privilege'     => 'details',
            ),
        )
    )
);