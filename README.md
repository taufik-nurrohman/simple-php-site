# Simple PHP Site

Named *Simple PHP Site* because it has no imposed structure — no required folder layout, no conventions to learn. Just the basics: routing, templating, variables, and functions — the minimum needed to build a website. It doesn't dictate how you organize your code — you're free to build on top of it however you like, whether that means a larger application backed by SQLite, MySQL, PostgreSQL, or anything else built to handle bigger data.

Not much to document here. The setup is simple — just [download or clone it](https://github.com/igoynawamreh/simple-php-site/releases), run the examples, and you'll get it.

Everything you'll need is inside `app`. Everything else — routing, templates, content sources — is defined in `config.php`.

`app` is just a folder name, not a rule. Rename it, copy it, or run several side by side. Your files don't even have to live in a folder like `app`: put them in the project root or organize them however you like. The only requirement is that each one is defined in `config.php`, where you tell it what URLs it handles, which templates to use, and where its content comes from.

For the exact list of what's available inside a page — variables, functions — check the source directly: `engine/variable.php`, `engine/function.php`, and `engine/route.php`.

Markdown-based content works out of the box, backed by a simple file cache — fine for a few hundred or even a few thousand pages. Planning for tens of thousands? You'll want to swap in something built for that scale, like SQLite, MySQL, or PostgreSQL.

The frontend is just as flexible. Stick with plain HTML-CSS-JS, or bring in a framework like Alpine.js, Vue, React, or whatever fits your workflow.
