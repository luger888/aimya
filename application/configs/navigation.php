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
                'label' => 'MY LESSONS',
                'tag' => 'leftMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'lesson',
                'action' => 'index',
                'resource'      => 'lesson',
                'privilege'     => 'index',
            ),
            array(
                'label' => 'Inbox Messages',
                'tag' => 'messageMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'message',
                'action' => 'inbox',
                'resource'      => 'message',
                'privilege'     => 'inbox',
            ),
            array(
                'label' => 'Trash Messages',
                'tag' => 'messageMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'message',
                'action' => 'trash',
                'resource'      => 'message',
                'privilege'     => 'trash',
            ),
            array(
                'label' => 'Sent Messages',
                'tag' => 'messageMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'message',
                'action' => 'sent',
                'resource'      => 'message',
                'privilege'     => 'sent',
            ),
            /*array(
                'label' => 'Archived',
                'tag' => 'messageMenu',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'message',
                'action' => 'archived',
                'resource'      => 'message',
                'privilege'     => 'archived',
            )*/
        )
    )
);