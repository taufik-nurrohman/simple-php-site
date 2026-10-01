<?php

require_once __DIR__ . '/lib/typecast.php';
require_once __DIR__ . '/lib/sanitizer.php';
require_once __DIR__ . '/lib/validator.php';
require_once __DIR__ . '/lib/response.php';
require_once __DIR__ . '/lib/markdown.php';

/**
 * [E]scape HTML [at]tribute’s value
 */
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_HTML5 | ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * URL helper
 *   url()            -> "https://example.com"
 *   url('/')         -> "https://example.com"
 *   url('blog/foo')  -> "https://example.com/blog/foo"
 *   url('/blog/bar') -> "https://example.com/blog/bar"
 */
function url(string $route = ''): string {
    global $home_url;
    $route = trim($route, '/');
    return $route === '' ? $home_url : $home_url . '/' . $route;
}

/**
 * Format a date string (e.g. '2026-01-01' or '2026-01-01 00:00')
 * using a PHP date() format string. Returns '' if $value is empty/null
 * or can't be parsed.
 */
function format_date(DateTimeInterface|null|string $value, string $format = 'Y-m-d'): string {
    if ($value === null) {
        return '';
    }
    if (is_string($value) && trim($value) === '') {
        return '';
    }

    $timestamp = $value instanceof DateTimeInterface ? $value->getTimestamp() : strtotime($value);

    if ($timestamp === false) {
        return '';
    }

    return date($format, $timestamp);
}

/**
 * Merge a target URL's query params with the CURRENT request's query
 * params ($_GET) — the target URL's own params win on key collisions,
 * everything else from the current URL is preserved.
 *
 * When the target URL changes a filter/sort param without specifying
 * 'page' itself, 'page' is reset to the first page — the current page
 * number usually no longer makes sense once the result set changes.
 */
function merge_query_url(string $url): string {
    $parsed = parse_url($url);
    $path = $parsed['path'] ?? '';

    $newParams = [];
    if (!empty($parsed['query'])) {
        parse_str($parsed['query'], $newParams);
    }

    // Current URL's params, then overlay with the new ones (new wins on conflict)
    $merged = array_merge($_GET, $newParams);

    // Reset to page 1 whenever a filter/sort change is being applied,
    // unless the target URL explicitly sets its own 'page' value.
    if (!array_key_exists('page', $newParams)) {
        unset($merged['page']);
    }

    $query = http_build_query($merged);

    return $path . ($query !== '' ? '?' . $query : '');
}

/**
 * Toggle a single value inside a multi-value query parameter
 * (e.g. ?tags[]=foo&tags[]=bar), based on the CURRENT request's query
 * params ($_GET). If the value is already selected, it's removed; if
 * not, it's added. The field is removed entirely from the query string
 * once its selection becomes empty. Also resets 'page' back to 1, since
 * changing a filter usually invalidates the current page number.
 */
function toggle_query_value(string $url, string $field, string $value): string {
    $parsed = parse_url($url);
    $path   = $parsed['path'] ?? '';

    $params = $_GET;

    $selected = array_values(array_filter(
        (array) ($params[$field] ?? []),
        fn($v) => $v !== null && $v !== ''
    ));

    $selected = in_array($value, $selected, true)
        ? array_values(array_diff($selected, [$value]))
        : array_values(array_unique(array_merge($selected, [$value])));

    if (empty($selected)) {
        unset($params[$field]);
    } else {
        $params[$field] = $selected;
    }

    unset($params['page']);

    $query = http_build_query($params);

    return $path . ($query !== '' ? '?' . $query : '');
}

/**
 * Find the `PAGES` entry whose `route` matches `$route` exactly,
 * tolerant of leading/trailing slash variations ('article', '/article',
 * '/article/' all match the same entry). Does not match dynamic routes
 * with placeholders (e.g. '/blog/[foo]') against a concrete path.
 */
function resolve_page(string $route): ?array {
    $normalized = '/' . trim($route, '/');

    foreach (PAGES as $page) {
        $pageRoute = '/' . trim($page['route'] ?? '', '/');

        if ($pageRoute === $normalized) {
            return $page;
        }
    }

    return null;
}
