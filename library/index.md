---
"tags": ["wiki", "wikitten", "personal wiki"],
"author": "Victor Stanciu"
---

# Hello there! Welcome to your personal wiki!

`Wikitten` is a small, fast, PHP wiki that [I][1] made because I really needed a place to store my notes, snippets, ideas, and so on. I've tried a lot of personal wikis and note-taking applications in the past, but since I have peculiar needs, none of them really suited me, so I rolled my own. With [blackjack](http://youtu.be/z5tZMDBXTRQ) and whatnot.

  [1]: https://github.com/victorstanciu

The page you are looking at right now is part of the actual wiki, and is written using the [Markdown](http://daringfireball.net/projects/markdown/syntax) syntax. If you're not familiar with Markdown go ahead, press the `Toggle source` button in the upper right corner, or check out the [sample document](Sample%20Markdown%20document.md) in the sidebar. By the way, if you're reading the source, noticed how I linked to another page within the wiki?

Now, there are other Markdown-powered wikis out there, and I've tried some of them, but I wanted something that I could use to store my code snippets too, so `syntax highlighting` was a must. Expand the `Code snippets` folder in the sidebar and take a look at some of the supported file types. I also needed something light enough that I could sync on Dropbox, because I access my notes and snippets on multiple machines.

### Requirements

* PHP `5.3+`
* The Apache webserver (with `mod_rewrite`)

### Installation

* [Download](https://github.com/victorstanciu/Wikitten/archive/master.zip) the latest version or clone the [repository on GitHub](https://github.com/victorstanciu/Wikitten)
* After extracting the archive, drop the files somewhere in your DocumentRoot, or make a separate Apache [VirtualHost](http://httpd.apache.org/docs/2.2/mod/core.html#virtualhost) (this is the way I currently use it myself)
* That's it. There's a `library` directory in the installation folder. Everything you place in there will be rendered by the wiki. If there's an `index.md` file (such as the one you are reading now) in that folder, it will be served by default when accessing the wiki.

  You don't have to use the `library` directory if you don't want to, you can configure Wikitten using
  the `config.php` file. Simply copy the `config.php.example` file found in the site root to `config.php`,
  and change the values of the constants defined inside.

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
