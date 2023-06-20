<?php
return [
    'block_layouts' => [
        'invokables' => [
            'CarouselBlock' => CarouselBlock\Site\BlockLayout\CarouselBlock::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ]
];
