<?php

/**
 * Renders {{ variable }} and {{ function('arg') }} placeholders inside
 * markdown content BEFORE it's passed to @taufik-nurrohman/markdown.
 *
 * Deliberately does NOT use eval() — only whitelisted variables and
 * functions registered via setVar()/setFunction() can be referenced.
 * The argument parser only supports simple quoted-string arguments
 * (e.g. url('path/to/image.jpg')), not full PHP expressions — this is
 * intentional, to keep the surface area small and safe.
 */
class TemplateRenderer {
    private array $vars = [];
    private array $functions = [];

    public function setVar(string $name, $value): void {
        $this->vars[$name] = $value;
    }

    public function setFunction(string $name, callable $fn): void {
        $this->functions[$name] = $fn;
    }

    public function render(string $content): string {
        if (strpos($content, '{{') === false) {
            return $content;
        }
        return preg_replace_callback(
            '/\{\{\s*(.+?)\s*\}\}/',
            fn($m) => $this->resolve(trim($m[1])),
            $content
        );
    }

    private function resolve(string $expr): string {
        // Function call: name('arg1', 'arg2')
        if (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)\((.*)\)$/s', $expr, $m)) {
            $name = $m[1];

            if (!isset($this->functions[$name])) {
                return ''; // unknown function — render as empty, not the raw PHP call
            }

            $args = $this->parseArgs(trim($m[2]));
            return (string) call_user_func_array($this->functions[$name], $args);
        }

        // Plain variable: site_url
        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $expr)) {
            return (string) ($this->vars[$expr] ?? '');
        }

        return ''; // anything else (not in the whitelist grammar) is ignored
    }

    /**
     * Minimal argument parser: comma-separated single- or double-quoted
     * string literals only. Not a full expression parser — that's on purpose.
     */
    private function parseArgs(string $raw): array {
        if ($raw === '') {
            return [];
        }

        preg_match_all('/\'([^\']*)\'|"([^"]*)"/', $raw, $matches, PREG_SET_ORDER);

        $args = [];
        foreach ($matches as $m) {
            $args[] = $m[1] !== '' ? $m[1] : $m[2];
        }

        return $args;
    }
}

/**
 * Returns a configured TemplateRenderer instance with the site's
 * whitelisted vars/functions registered. Built once per request
 * (static cache) since the config never changes mid-request.
 */
function get_template_renderer(): TemplateRenderer {
    global $home_url;

    static $renderer = null;

    if ($renderer === null) {
        $renderer = new TemplateRenderer();

        $renderer->setVar('site_title', STATE['title'] ?? 'My Site');
        $renderer->setVar('site_url', $home_url);
        $renderer->setFunction('url', fn($path) => url($path));
        $renderer->setFunction('img', function ($path, $alt = '', $class = '') {
            $src = url($path);
            $classAttr = $class !== '' ? ' class="' . htmlspecialchars($class, ENT_QUOTES) . '"' : '';
            return '<img src="' . htmlspecialchars($src, ENT_QUOTES) . '" alt="' . htmlspecialchars($alt, ENT_QUOTES) . '"' . $classAttr . '>';
        });
    }

    return $renderer;
}
