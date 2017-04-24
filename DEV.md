# Development notes

## Updating the CodeMirror syntax highlighter package for Wikitten

The code mirror js file needs combining for Wikitten into one file with the dependencies in the right order. 

Here are the instructions on how this has been achieved previously:

```shell
## Clone CodeMirror repo
git clone git@github.com:codemirror/CodeMirror.git
cd CodeMirror

## Build lib/codemirror.js
npm install
npm run build

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

## Remove test files (watch output above for new test files)
rm -rf gss_test.js less_test.js mscgen_test.js msgenny_test.js scss_test.js test.js xu_test.js

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

**Please test this new codemirror.min.js before minifying to resolve any new dependencies on addon js files.**

(look in chrome dev tools console for js errors)



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