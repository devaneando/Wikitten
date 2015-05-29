# Development notes

## Updating the CodeMirror syntax highlighter package for Wikitten

The code mirror js file needs combining for Wikitten into one file with the dependencies in the right order. 

Here are the instructions on how this has been achieved previously:

```shell
## Clone CodeMirror repo
git clone git@github.com:codemirror/CodeMirror.git
cd CodeMirror

## Make a tmp workspace
mkdir -p _all/_files
mkdir -p _all/_deps

## Copy in main lib
cd _all
cp ../lib/codemirror.js .
cp ../lib/codemirror.css .

## Copy in all modes for syntax highlighting
cd _files
cp ../../mode/*/*.js .

## Locate test files
grep -i "MT(" *

## Remove test files
rm -rf less_test.js scss_test.js test.js

## Copy in deps for some of the modes
cd ../_deps/
cp ../../addon/edit/matchbrackets.js .
cp ../../addon/mode/simple.js .

## Combine all addons and modes with main lib file
cat *.js >> ../codemirror.js
cd ../_files
cat *.js >> ../codemirror.js

## Copy to Wikitten project
cp ../codemirror.js ../../../Wikitten/static/js/codemirror.min.js
cp ../codemirror.css ../../../Wikitten/static/css/codemirror.css
```

Pulls in the two deps `matchbrackets.js` and `simple.js` before the mode js libs. 

**Please test this new codemirror.min.js before minifying to resolve any new dependencies on addon js files.**

(look in chrome dev tools console for js errors)

Build output:

```shell
 ~/Software  git clone git@github.com:codemirror/CodeMirror.git
Cloning into 'CodeMirror'...
remote: Counting objects: 28054, done.
remote: Compressing objects: 100% (47/47), done.
remote: Total 28054 (delta 19), reused 2 (delta 2), pack-reused 28005
Receiving objects: 100% (28054/28054), 13.88 MiB | 1.83 MiB/s, done.
Resolving deltas: 100% (16377/16377), done.
Checking connectivity... done.
 ~/Software  cd CodeMirror
 ~/Software/CodeMirror   master  mkdir -p _all/_files
 ~/Software/CodeMirror   master  mkdir -p _all/_deps
 ~/Software/CodeMirror   master  cd _all
 ~/Software/CodeMirror/_all   master  cp ../lib/codemirror.js .
 ~/Software/CodeMirror/_all   master  cp ../lib/codemirror.css .
 ~/Software/CodeMirror/_all   master  cd _files
 ~/Software/CodeMirror/_all/_files   master  cp ../../mode/*/*.js .
cp: will not overwrite just-created ‘./test.js’ with ‘../../mode/css/test.js’
cp: will not overwrite just-created ‘./test.js’ with ‘../../mode/gfm/test.js’
cp: will not overwrite just-created ‘./test.js’ with ‘../../mode/haml/test.js’
cp: will not overwrite just-created ‘./test.js’ with ‘../../mode/javascript/test.js’
cp: will not overwrite just-created ‘./test.js’ with ‘../../mode/markdown/test.js’
cp: will not overwrite just-created ‘./test.js’ with ‘../../mode/php/test.js’
cp: will not overwrite just-created ‘./test.js’ with ‘../../mode/ruby/test.js’
cp: will not overwrite just-created ‘./test.js’ with ‘../../mode/shell/test.js’
cp: will not overwrite just-created ‘./test.js’ with ‘../../mode/slim/test.js’
cp: will not overwrite just-created ‘./test.js’ with ‘../../mode/stex/test.js’
cp: will not overwrite just-created ‘./test.js’ with ‘../../mode/textile/test.js’
cp: will not overwrite just-created ‘./test.js’ with ‘../../mode/verilog/test.js’
cp: will not overwrite just-created ‘./test.js’ with ‘../../mode/xml/test.js’
cp: will not overwrite just-created ‘./test.js’ with ‘../../mode/xquery/test.js’
 ✘  ~/Software/CodeMirror/_all/_files   master  grep -i "MT(" *
less_test.js:  function MT(name) { test.mode(name, mode, Array.prototype.slice.call(arguments, 1), "less"); }
less_test.js:  MT("variable",
less_test.js:  MT("amp",
less_test.js:  MT("mixin",
less_test.js:  MT("nest",
less_test.js:  MT("interpolation", ".@{[variable foo]} { [property font-weight]: [atom bold]; }");
scss_test.js:  function MT(name) { test.mode(name, mode, Array.prototype.slice.call(arguments, 1), "scss"); }
scss_test.js:  MT('url_with_quotation',
scss_test.js:  MT('url_with_double_quotes',
scss_test.js:  MT('url_with_single_quotes',
scss_test.js:  MT('string',
scss_test.js:  MT('important_keyword',
scss_test.js:  MT('variable',
scss_test.js:  MT('variable_as_attribute',
scss_test.js:  MT('numbers',
scss_test.js:  MT('number_percentage',
scss_test.js:  MT('selector',
scss_test.js:  MT('singleline_comment',
scss_test.js:  MT('multiline_comment',
scss_test.js:  MT('attribute_with_hyphen',
scss_test.js:  MT('string_after_attribute',
scss_test.js:  MT('directives',
scss_test.js:  MT('basic_structure',
scss_test.js:  MT('nested_structure',
scss_test.js:  MT('mixin',
scss_test.js:  MT('number_without_semicolon',
scss_test.js:  MT('atom_in_nested_block',
scss_test.js:  MT('interpolation_in_property',
scss_test.js:  MT('interpolation_in_selector',
scss_test.js:  MT('interpolation_error',
scss_test.js:  MT("divide_operator",
scss_test.js:  MT('nested_structure_with_id_selector',
scss_test.js:  MT('indent_mixin',
scss_test.js:  MT('indent_nested',
scss_test.js:  MT('indent_parentheses',
scss_test.js:  MT('indent_vardef',
test.js:  function MT(name) { test.mode(name, mode, Array.prototype.slice.call(arguments, 1)); }
test.js:  MT("indent",
test.js:  MT("indent_switch",
test.js:  MT("def",
 ~/Software/CodeMirror/_all/_files   master  rm -rf less_test.js scss_test.js test.js
 ~/Software/CodeMirror/_all/_files   master  cd ../_deps/
 ~/Software/CodeMirror/_all/_deps   master  cp ../../addon/edit/matchbrackets.js .
 ~/Software/CodeMirror/_all/_deps   master  cp ../../addon/mode/simple.js .
 ~/Software/CodeMirror/_all/_deps   master  cat *.js >> ../codemirror.js
 ~/Software/CodeMirror/_all/_deps   master  cd ../_files
 ~/Software/CodeMirror/_all/_files   master  cat *.js >> ../codemirror.js
 ~/Software/CodeMirror/_all/_files   master  cp ../codemirror.js ../../../Wikitten/static/js/codemirror.min.js
 ~/Software/CodeMirror/_all/_files   master  cp ../codemirror.css ../../../Wikitten/static/css/codemirror.css
 ~/Software/CodeMirror/_all/_files   master  
```

Now there are no errors in the console:

![screenshot from 2015-05-29 15 09 21](https://cloud.githubusercontent.com/assets/1327332/7884325/d2f26e70-0614-11e5-9904-eec02e34eb0e.png)

and syntax highlighting seems to be working for php and other languages, now minify locally and not relying on a web based compiler:

```shell
## Install uglify-js
sudo npm install uglify-js -g

## Run the minifier
uglifyjs --compress --mangle --output test.js -- Wikitten/static/js/codemirror.min.js

## Copy resulting file into the project
cd Wikitten/static/js
mv codemirror.min.js codemirror.js
cp ../../../test.js codemirror.min.js
```

Test the new minified version to make sure highlighting is still working.