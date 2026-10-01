<?php

// ============================================================================
// State configuration
// ============================================================================

const STATE = [
    'debug' => true,
    'title' => 'Site Title',
    'zone'  => 'Asia/Jakarta',
];

// ============================================================================
// Route and template configuration
// ============================================================================

const HOME_PAGE   = [
    'title'    => 'Home',
    'template' => __DIR__ . '/app/home.html.php',
];

const ERROR_PAGE  = [
    'title'    => 'Page Not Found',
    'template' => __DIR__ . '/app/404.html.php',
];

const PAGES = [
    [
        'title'    => 'About',
        'route'    => '/about',
        'template' => __DIR__ . '/app/about/about.html.php',
        // Single file markdown (optional)
        'content'  => __DIR__ . '/app/about/about.md',
    ],
    [
        'title'    => 'About Segment 1',
        'route'    => '/about/[foo]',
        'template' => __DIR__ . '/app/about/about-[foo].html.php',
    ],
    [
        'title'    => 'About Segment 2',
        'route'    => '/about/[foo]/[bar]',
        'template' => __DIR__ . '/app/about/about-[foo]-[bar].html.php',
    ],
    [
        // Example auto list/item routing and directory-based markdown
        'title'    => 'Article',
        'route'    => '/article',
        // `template` as { list, item } (instead of a single file) makes this
        // one entry automatically cover two routes: `/article` (list) and
        // `/article/[slug]` (item)
        // the `[slug]` segment is appended by the router automatically.
        'template' => [
            'list' => __DIR__ . '/app/article/list.html.php',
            'item' => __DIR__ . '/app/article/item.html.php',
        ],
        // `content.dir` reads markdown files from this directory for both
        // routes above.
        'content'  => [
            'dir'       => __DIR__ . '/app/article/content',
            'per_page'  => 5,
            'order_by'  => 'title',
            'order_dir' => 'asc',
            // Allowed filters
            'filter'    => ['category', 'tags'],
        ],
    ],
];

// const CUSTOM_PAGES = [
//     [
//         'name'     => 'custom_page_1',
//         'template' => __DIR__ . '/path/to/file.php',
//     ],
// ];
