Introduction
============

This is the Majisti's PHP Backend Scaffolding project. The following is provided out of the box:

- Symfony setup
- Full testing suite configured. Acceptance, Functional, Integration and Unit
- Everything runs under Docker. 0 global project dependencies aside from git, docker and docker-compose.
- Gulp with SCSS, Bourbon and Susy for the frontend.
- Everything runs under a Makefile and is ready for Continuous Integration

Roadmap
=======

Short term
----------

- CodeCoverage
- SCSS Style check
- Javascript Style check

Long term
---------
- Installer
- React/Redux scaffold (might be in another repo)


Installation
============

Docker & docker-compose
-----------------------
In order to setup this project in less then two minutes, use
docker and docker-compose. If you do not have them installed, you
can follow our wiki for this virtual machine (Ubuntu Setup) at
http://integration.majisti.com:8083/display/COOK/Installing+Docker+on+Ubuntu+14.04+LTS

Setup
-----
Make sure your nginx proxy is setup to always start on boot:

```
docker run -d -p 80:80 --restart=always -v /var/run/docker.sock:/tmp/docker.sock:ro jwilder/nginx-proxy
```
note: you might want to use *8080:80* or *8081:80* if port 80 or 8080 are already used.

Once this is done, you only need to run:

```
make clean && make
```

Add to your `/etc/host` file:

```
127.0.0.1   majisti.skeleton
```

Browse the site at `http://majisti.skeleton` or `http://majisti.skeleton:port` if you used a different port.

Continuous Integration
======================

To run the entire setup with test: `make ci`

Testing
=======
You can run all tests using `make test`. This will run the whole testing pyramid, such as Acceptance,
Functional, Integration and Unit tests

Running Unit tests under IntelliJ
---------------------------------

1. Configure a new local PHP interpreter that points to `bin/php7-docker-ide` under `Settings > PHP`
2. Configure under `Settings > PHP > PHPUnit`
    - Use custom autoloader, using the `vendor/autoload.php`
    - Use optional configuration file, located within `./phpunit.xml.dist`

Code Style
==========
You can checkstyle your PHP code by running a dry-run with `make cs`
or if you want to automatically fix the code, you can run `make cs-fix`
