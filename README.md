Wikitten
========

Wikitten is a small, fast, PHP wiki, and the perfect place to store your notes, code snippets, ideas, etc.

Check out the **[project website](http://wikitten.vizuina.com)** for more details and features.

[![Wikitten](http://wikitten.vizuina.com/screenshot.png)](http://wikitten.vizuina.com)

Docker
------

You can use docker to run the wiki:

```bash
docker build -t wikitten .
docker run -it --name wikitten -v `pwd`:/var/www -p 9000:9000 wikitten
```

This will build the docker container using the Dockerfile in this repository and then run the container mounting the current directory as the web root and exposing port 9000 from inside the container bac to your machine.

This will enable you to go to http://localhost:9000 and view the wiki.


Also there is a docker-composer file provided.

This will stop you having to run the above commands and just run a single one:
```bash
docker-compose up
```

#### You will have to have docker and docker-compose installed ####
