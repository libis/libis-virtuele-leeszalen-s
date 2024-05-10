<?php
return [
    'block_layouts' => [
        'invokables' => [
            'ImageBannerBlock' => ImageBannerBlock\Site\BlockLayout\ImageBannerBlock::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ]
];
