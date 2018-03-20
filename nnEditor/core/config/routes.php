<?php
    
$routes = array(
    '/' => array(
        'use'       => 'Backend@onDisplayDefault',
        'auth'      => true,
        'group'     => 'backend',
        'namespace' => 'nnEditor\Core'
    ),
    '/files/' => array(
        'use'       => 'Backend@onDisplayDefault',
        'auth'      => true,
        'group'     => 'backend',
        'namespace' => 'nnEditor\Core'
    ),
    '/filemanager/' => array(
        'use'       => 'Backend@onDisplayFileManager',
        'auth'      => true,
        'group'     => 'backend',
        'namespace' => 'nnEditor\Core'
    ),
    '/save/filemanager/' => array(
        'use'       => 'Backend@onAjaxFileManager',
        'auth'      => true,
        'group'     => 'backend',
        'namespace' => 'nnEditor\Core'
    ),
    '/history/' => array(
        'use'       => 'Backend@onDisplayHistory',
        'auth'      => true,
        'group'     => 'backend',
        'namespace' => 'nnEditor\Core'
    ),
    '/save/history/' => array(
        'use'       => 'Backend@onAjaxHistory',
        'auth'      => true,
        'group'     => 'backend',
        'namespace' => 'nnEditor\Core'
    ),
    '/content/save/file/' => array(
        'use'       => 'Backend@onAjaxSaveFilePath',
        'auth'      => true,
        'group'     => 'backend',
        'namespace' => 'nnEditor\Core'
    ),
    '/content/edit/' => array(
        'use'       => 'Backend@onDisplayEdit',
        'auth'      => true,
        'group'     => 'backend',
        'namespace' => 'nnEditor\Core'
    ),
    '/login/' => array(
        'use'       => 'Auth@onDisplayLogin',
        'auth'      => false,
        'group'     => 'backend',
        'namespace' => 'nnEditor\Core'
    ),
    '/auth/' => array(
        'use'       => 'Auth@onAuth',
        'auth'      => false,
        'group'     => 'backend',
        'namespace' => 'nnEditor\Core'
    ),
    '/logout/' => array(
        'use'       => 'Auth@onLogout',
        'auth'      => false,
        'group'     => 'backend',
        'namespace' => 'nnEditor\Core'
    ),
    '/load/panel/' => array(
        'use'       => 'Frontend@fetchPanel',
        'auth'      => false,
        'group'     => 'frontend',
        'namespace' => 'nnEditor\Core'
    ),
    '/save/content/' => array(
        'use'       => 'Frontend@onAjaxSaveContent',
        'auth'      => false,
        'group'     => 'frontend',
        'namespace' => 'nnEditor\Core'
    )
);

$rules = [
    'user'  => ['user', 'admin'],
    'admin' => ['admin'],
    'superadmin' => ['superadmin']
];

$groups = [
    'backend' => [
        'prefix' => $GLOBALS['http_base']
    ],
    'frontend' => [
        'prefix' => ''
    ]
];

$data = [
    'routes' => $routes,
    'rules'  => $rules,
    'groups' => $groups
];
return $data;
//TODO: Routing
//Route::get('/test2/', array('use' => 'Main@tests', 'auth'=>false));