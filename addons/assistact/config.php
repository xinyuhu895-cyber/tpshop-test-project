<?php
return [
    'template' => [
        'type' => 'Think',
        'view_path' => '',
        'view_suffix' => 'html',
        'view_depr' => DS,
        'tpl_begin' => '{',
        'tpl_end' => '}',
        'taglib_begin' => '<',
        'taglib_end' => '>'
    ],
    'view_replace_str' => [
        '__PUBLIC__' => '/public',
        '__STATIC__' => '/addons/assistact/view/static',
    ],
];
