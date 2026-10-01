<?php

$site  = [];
$page  = [];
$pages = [];
$pagination = [];
$is = [];

$site['title'] = STATE['title'] ?? null;
$site['url']   = $home_url;

$page['route']       = $route;
$page['route:last']  = basename($route) === '' ? null : basename($route);
$page['route:query'] = substr($_SERVER['REQUEST_URI'], strlen($base_url));
$page['title']       = $site['title'];
$page['content']     = null;

foreach ($_GET as $key => $value) {
    $page['param:' . $key] = $value;
}

$page['param:page'] = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$page['param:per_page'] = max(1, (int) ($_GET['per_page'] ?? 10));

$is['home']      = false;
$is['page']      = false;
$is['page_list'] = false;
$is['page_item'] = false;
$is['custom']    = false;
$is['404']       = false;
$is['markdown']  = false;

/**
 * Home page
 */
if ($route === '') {
    $page['title'] = HOME_PAGE['title'] ?? $page['title'];
    $is['home'] = true;
    if (isset(HOME_PAGE['content'])) {
        $content_file = HOME_PAGE['content'];
        if (is_file($content_file)) {
            $render_md = render_md_from_file($content_file);
            $page = array_merge($page, $render_md);
            $is['markdown'] = true;
        }
    }
    require HOME_PAGE['template'];
    exit;
}

/**
 * Pages
 */
if (defined('PAGES') && !empty(PAGES)) {
    foreach (PAGES as $page_config) {
        // Auto list/item routing is driven by the shape of `template`: a route
        // gets both a list pattern (the route as-is) and an item pattern
        // (route + `/[slug]`) whenever `template` is { list, item } instead of
        // a single file path.
        $hasListItemTemplate = is_array($page_config['template'] ?? null)
            && isset($page_config['template']['list'], $page_config['template']['item']);

        // Whether markdown data actually comes from a directory. Independent
        // of `$hasListItemTemplate` — a list/item template pair could, in
        // principle, source its data from somewhere other than markdown.
        $hasContentDir = isset($page_config['content']['dir']);

        $candidates = $hasListItemTemplate
            ? [
                'list' => $page_config['route'],
                'item' => rtrim($page_config['route'], '/') . '/[slug]',
            ]
            : ['plain' => $page_config['route']];

        foreach ($candidates as $mode => $routePattern) {
            $pattern = trim($routePattern ?? '', '/');

            $regexParts = [];
            foreach (explode('/', $pattern) as $segment) {
                if (preg_match('/^\[([a-zA-Z_][a-zA-Z0-9_]*)\]$/', $segment, $m)) {
                    $regexParts[] = '(?P<' . $m[1] . '>[^/]+)';
                } else {
                    $regexParts[] = preg_quote($segment, '#');
                }
            }
            $regex = '#^' . implode('/', $regexParts) . '$#';

            if (!preg_match($regex, $route, $matches)) {
                continue;
            }

            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $page['route:' . $key] = $value;
                }
            }

            $page['title'] = $page_config['title'] ?? $page['title'];
            $is['page']    = true;

            if ($mode === 'item') {
                $is['page_item'] = true;
            } elseif ($mode === 'list') {
                $is['page_list'] = true;
            }

            // Single file Markdown
            if (isset($page_config['content']) && !is_array($page_config['content'])) {
                $content_file = $page_config['content'];
                if (is_file($content_file)) {
                    $render_md = render_md_from_file($content_file);
                    $page = array_merge($page, $render_md);
                    $is['markdown'] = true;
                }
            }

            // Directory-based Markdown — populates `$page`/`$pages` when this
            // route's data actually comes from a markdown directory.
            if ($hasContentDir) {
                $dir      = rtrim($page_config['content']['dir'], '/');
                $markdown = new Markdown($dir, get_template_renderer());

                $page['route:list'] = '/' . $page_config['route'];
                $page['title:list'] = $page['title'];

                if ($mode === 'item') {
                    $slug = $page['route:slug'] ?? '';
                    $item = $markdown->getPage($page_config['route'], $slug);

                    // No matching file for this slug,
                    // fall through to the next PAGES entry / 404.
                    if ($item === null) {
                        continue 2;
                    }

                    $page = array_merge($page, $item);
                } elseif ($mode === 'list') {
                    // Fields that may be used as filter (per page config, with a default)
                    $allowedFilters = $page_config['content']['filter'] ?? ['category', 'tags'];

                    // Only whitelisted fields from the URL become filters
                    $filters = array_intersect_key($_GET, array_flip($allowedFilters));

                    $page = array_merge($page, [
                        'param:per_page'  => max(1, (int) ($_GET['per_page'] ?? $page_config['content']['per_page'] ?? 10)),
                        'param:order_by'  => $_GET['order_by'] ?? $page_config['content']['order_by'] ?? 'title',
                        'param:order_dir' => $_GET['order_dir'] ?? $page_config['content']['order_dir'] ?? 'asc',
                    ]);

                    // Make sure these keys always exist, even when missing from the URL
                    foreach (array_merge(['q'], $allowedFilters) as $key) {
                        $page['param:' . $key] = $page['param:' . $key] ?? null;
                    }

                    $pages = array_merge($pages, $markdown->getPages(
                        page: $page['param:page'],
                        route: $page_config['route'],
                        perPage: $page['param:per_page'],
                        filters: $filters,
                        search: $page['param:q'],
                        orderBy: $page['param:order_by'],
                        orderDir: $page['param:order_dir'],
                    ));

                    $pagination = array_merge($pagination, generate_pagination(
                        currentPage: $pages['current_page'],
                        lastPage: $pages['last_page'],
                        baseUrl: '/' . trim($page_config['route'], '/'),
                        extraParams: array_merge($filters, [
                            'q' => $page['param:q'],
                        ]),
                    ));
                }
            }

            // Resolve which template file to require
            $template = $hasListItemTemplate
                ? ($page_config['template'][$mode] ?? $page_config['template']['list'])
                : $page_config['template'];

            require $template;
            exit;
        }
    }
}

/**
 * Custom pages
 */
if (defined('CUSTOM_PAGES') && !empty(CUSTOM_PAGES)) {
    foreach (CUSTOM_PAGES as $custom_page) {
        $is['custom'] = true;
        require $custom_page['template'];
    }
}

/**
 * 404 page
 */
http_response_code(404);
$page['title'] = ERROR_PAGE['title'] ?? $page['title'];
$is['404'] = true;
if (isset(ERROR_PAGE['content'])) {
    $content_file = ERROR_PAGE['content'];
    if (is_file($content_file)) {
        $render_md = render_md_from_file($content_file);
        $page = array_merge($page, $render_md);
        $is['markdown'] = true;
    }
}
require ERROR_PAGE['template'];
exit;
