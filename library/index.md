# Hello there! Welcome to your personal wiki!

`Wikitten` is a small, fast, PHP wiki that [I][1] made because I really needed a place to store my notes, snippets, ideas, and so on. I've tried a lot of personal wikis and note-taking applications in the past, but since I have peculiar needs, none of them really suited me, so I rolled my own. With [blackjack](http://youtu.be/z5tZMDBXTRQ) and so on.

  [1]: https://github.com/victorstanciu

The page you are looking at right now is part of the actual wiki, and is written using the [Markdown](http://daringfireball.net/projects/markdown/syntax) syntax. If you're not familiar with Markdown go ahead, press the `Toggle source` button in the upper right corner, or check out the sample document in the sidebar.

Now, there are other Markdown-powered wikis out there, and I've tried some of them, but I wanted something that I could use to store my code snippets too, so `syntax highlighting` was a must. Expand the `Code snippets` folder in the sidebar and take a look at some of the supported file types. I also needed something light enough that I could sync on Dropbox, because I access my notes and snippets on multiple machines.

### Requirements

* PHP `5.3+`
* The Apache webserver (with `mod_rewrite`)

### Installation

* [Download](https://github.com/victorstanciu/Wikitten/archive/master.zip) the latest version or clone the [repository on GitHub](https://github.com/victorstanciu/Wikitten)
* After extracting the archive, drop the files somewhere in your DocumentRoot, or make a separate Apache [VirtualHost](http://httpd.apache.org/docs/2.2/mod/core.html#virtualhost) (this is the way I currently use it myself)
* That's it. There's a `library` directory in the installation folder. Everything you place in there will be rendered by the wiki. If there's an `index.md` file (such as the one you are reading now) in that folder, it will be served by default when accessing the wiki.

  You don't have to use the `library` directory if you don't want to. Simply open the `index.php` file and set another value for the `LIBRARY` constant. That's where your files will be read from.

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