# Acorn

[Acorn](https://acorn.blog/) is a content management system (CMS) for creating easy-to-use and highly-capable websites. You can read a bit more about it [here](https://acorn.blog/).

It's currently in a pre-alpha state and should not be used for stability & security reasons. It's built on top of the file-based [Kirby CMS](https://getkirby.com/) which requires its own [license](https://getkirby.com/buy) if used in production. Kirby's [documentation](https://getkirby.com/docs) is an excellent resource that should make Acorn's structure easier to understand.

[Enter an email address here](https://acorn.blog/) to be notified of Acorn's eventual alpha release.

## Requirements

- Apache2 with URL rewriting (mod_rewrite) or nginx
- PHP 7.0+
- PHP mbstring extension for UTF-8 formatting
- PHP GD library for image processing
- PHP cURL for remote image downloading
- Group-writable (775) `acorn`, `cache`, and `content` folders and subfolders. Files within those directories should be group-writable as well (664).

## Installation

Copy all files to your server's web directory and ensure that the `cache` and `content` folders and subfolders are group-writable. Visit the site and the homepage should appear.

Contact Andy for two additional plugins that add a `(gallery:`) tag and smarter image processing.