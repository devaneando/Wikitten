---
"tags": ["wiki", "wikitten", "personal wiki"],
"author": "Victor Stanciu"
---

# Hello there! Welcome to your personal wiki!

`Wikitten` is a small, fast, PHP wiki that [I][1] made because I really needed a place to store my notes, snippets, ideas, and so on. I've tried a lot of personal wikis and note-taking applications in the past, but since I have peculiar needs, none of them really suited me, so I rolled my own.

  [1]: https://github.com/victorstanciu

The page you are looking at right now is part of the actual wiki, and is written using the [Markdown](http://daringfireball.net/projects/markdown/syntax) syntax. If you're not familiar with Markdown go ahead, press the `Toggle source` button in the upper right corner, or check out the [sample document](Sample%20Markdown%20document.md) in the sidebar. By the way, if you're reading the source, noticed how I linked to another page within the wiki?

Now, there are other Markdown-powered wikis out there, and I've tried some of them, but I wanted something that I could use to store my code snippets too, so `syntax highlighting` was a must. Expand the `Code snippets` folder in the sidebar and take a look at some of the supported file types. I also needed something light enough that I could sync on Dropbox, because I access my notes and snippets on multiple machines.

### Requirements

* PHP `5.3+`
* The Apache webserver (with `mod_rewrite`)

or

* PHP `5.4`
* Inbuilt webserver `php -S 0.0.0.0:8000 routing.php`

### Installation

* [Download](https://github.com/victorstanciu/Wikitten/archive/master.zip) the latest version or clone the [repository on GitHub](https://github.com/victorstanciu/Wikitten)
* After extracting the archive, drop the files somewhere in your DocumentRoot, or make a separate Apache [VirtualHost](http://httpd.apache.org/docs/2.2/mod/core.html#virtualhost) (this is the way I currently use it myself)
* That's it. There's a `library` directory in the installation folder. Everything you place in there will be rendered by the wiki. If there's an `index.md` file (such as the one you are reading now) in that folder, it will be served by default when accessing the wiki.

You can also run the wiki using [Docker](https://github.com/victorstanciu/Wikitten/wiki/Docker-instructions)

### Configure Wikitten

You are able to configure Wikitten by using the config file.
First, copy the `config.php.example` to `config.php` and you are ready to change the settings.   
Some options are disabled with a comment but can be enabled by removing `//` from the option line.

* `define('APP_NAME', 'My Wiki');` - Set the Wiki title
* `define('DEFAULT_FILE', 'index.md');` - Choose the file that should be loaded as the homepage, must be located in library folder
* `define('LIBRARY', '/path/to/wiki/library');` - Set a custom path to the library
* `define('ENABLE_EDITING', true);` - Enable the in-page editing of any files
* `define('USE_PAGE_METADATA', true);` - Enable the JSON Front Matter (meta data), see below for more details
* `define('USE_DARK_THEME', true);` - Enable the dark theme (see below for a screenshot)
* `define('USE_WIKITTEN_LOGO', false);` - Disable the Wikitten logo on the left bottom

### JSON Front Matter (meta data)

Wikitten content can also be tagged using a simple but powerful JSON Front Matter system, akin to [Jekyll's YAML Front Matter](https://github.com/mojombo/jekyll/wiki/YAML-Front-Matter). Defining a custom title, tags, or other
relevant data for a specific page is just a matter of adding a special header at the start of your files, like so:

    ---
    "title": "My Custom Page Title",
    "tags": ["my", "custom", "tags"],
    "author": "Bob"
    ---

    # Hello, world!

    This is my cool wiki page.

Wikitten will intelligently grab this data, and use it for things like meta keywords, the
page title, and maybe eventually search indexing. All the information provided in this
header is passed as-is to the views, so future components and plugins may also make use of it.

**Note:** The JSON header is expected to be a JSON hash, but to simplify things, Wikitten lets you leave out the starting an ending `{ }` brackets if you want. Everything else in the JSON syntax still applies:

- Strings (i.e: `title` must be written within double quotes: `"title"`)
- Values must be seperated with a comma character, even if its the only value in a line.

### Dark Theme

If you are working until midnight it can be a pain to look at bright white backgrounds. That's why Wikitten offers a Dark Theme which can be enabled in the config.php file with the `define('USE_DARK_THEME', true);` option.

It looks like this:
![Screenshot Dark Mode](static/img/screenshot_dark.png)

### Roadmap

Some of the features I plan to implement next:

* [Pastebin](http://pastebin.com/) API integration. I think it would be cool to share snippets on Pastebin (or a similar service) with a single click
* Creating / updating files directly through the web interface. Other wikis place great accent on creating and editing pages in the browser, but since I have my trusty code editor open non-stop anyway, I prefer to update my files manually for now.
* Search in files

### Special thanks go to:

* [Michel Fortin](http://michelf.ca/home/), for the [PHP Markdown parser](http://michelf.ca/projects/php-markdown/).
* [Marijn Haverbeke](http://marijnhaverbeke.nl/), for [CodeMirror](http://codemirror.net/), a JavaScript code editor.
* Twitter, for the [Bootstrap](http://twitter.github.com/bootstrap/) CSS framework.
* All Vectors, for the [free cat vector](http://www.allvectors.com/cats-vector/) silhouette I used in making the logo.
