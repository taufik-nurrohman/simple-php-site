<?php

// Find out the folder where this application is located.
//   Example: if the app is at "/myapp/index.php" -> `$base_url` = "/myapp"
//   Example: if the app is at the domain root    -> `$base_url` = "" (empty)
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

// Get the full path the user typed, WITHOUT the query string ("?id=5" etc).
//   Example: "example.com/blog/5?foo=bar" -> `$path` = "/blog/5"
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Strip `$base_url` from the front of $path if present, leaving just the "route".
// Also trim leading/trailing slashes (/) to keep it clean.
//   Example: "/blog/5/"          -> `$route` = "blog/5"
//   Example: "/myapp/blog/5.php" -> `$route` = "blog/5.php"
$route = trim(substr($path, strlen($base_url)), '/');
// If the route still has a ".php" suffix, remove it so the URL stays clean.
//   Example: "/profil.php"            -> `$route` = "profil"
//   Example: "/myapp/admin/index.php" -> `$route` = "admin/index"
$route = preg_replace('/\.php$/', '', $route); // strip .php

// Determine the scheme used: "http" or "https".
// Check HTTPS: if the HTTPS header is set and not "off", it's https.
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

// Get the FULL domain name including its extension.
// `HTTP_HOST` usually already includes the port if it's not the default (e.g. "example.com:8080").
//   Example: "https://www.example.com"  -> "www.example.com"
//   Example: "https://example.com"      -> "example.com"
//   Example: "https://example.com:8080" -> "example.com:8080"
$host = $_SERVER['HTTP_HOST'];

// Strip the port if present, by cutting the string before ":".
//   Example: "https://www.example.com:8080" -> "www.example.com"
$domain = strtok($host, ':');
// Strip leading "www." if present
$domain = preg_replace('/^www\./i', '', $domain);

// Absolute URL to the app's homepage, including scheme, host, and `$base_url`.
// Use `$host` (not `$domain`) so non-standard ports are kept, and so the
// host matches exactly what the user used — e.g. if they're on
// "www.example.com" and links pointed to "example.com" instead, that's a
// different host, and cookies scoped to "www.example.com" specifically
// (rather than ".example.com") wouldn't be sent there.
//   Example: app at root      -> "https://example.com"
//   Example: app at "/myapp"  -> "https://example.com/myapp"
//   Example: dev on port 8080 -> "http://localhost:8080/myapp"
$home_url = $scheme . '://' . $host . $base_url;
