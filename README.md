# Introduction

This is the Majisti's PHP Backend Scaffolding project. The following is provided out of the box:

- Symfony setup
- Full testing suite configured. Acceptance, Functional, Integration and Unit
- Everything runs under Docker. Minimal global project dependencies: git, docker and docker-compose.
- Gulp with SCSS, Bourbon and Susy for the frontend.
- Everything runs under a Makefile and is ready for Continuous Integration

# Roadmap

## Short term

- CodeCoverage
- SCSS Style check
- Javascript Style check

## Long term
- Installer
- React/Redux scaffold (might be in another repo)

## Experimentation

- **PHPSpec** [Rejected]
    PHPSpec was dropped in favor of Codeception and Mockery. Though we prefer PHPSpec for writing shorter tests,
    the code completion was not enough within Intellij. We were left blind and needed to read the documentation in order
    to develop tests.
- Bowling Kata [WIP]
    - Game Score Calculation [Done]
    - API [Todo]
    - ...

## Known Bugs

- Step debugging: Path Mapping with XDebug fails when using Mockery.

# Installation

## Docker & docker-compose
In order to setup this project without external dependencies, use
docker and docker-compose. If you do not have them installed, you
can follow our wiki for this virtual machine (Ubuntu Setup) at
http://integration.majisti.com:8083/display/COOK/Installing+Docker+on+Ubuntu+14.04+LTS

## Setup
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


##Usage

- Browse the site at `http://majisti.skeleton` or `http://majisti.skeleton:port` if you used a different port.
- You can also boot the tmux terminal using `sh tmux.sh`

# Continuous Integration

To run the entire setup with tests: `make ci`

# Testing with Behaviour Driven Development
You can run all tests using `make test`. This will run the whole testing pyramid, such as Acceptance,
Functional, Integration and Unit tests

The project is setup to be tested using BDD techniques. It is far from perfect
and it is an effort to bring BDD on the table for the codebase. The
tools installed are the following:

## Codeception
- Codeception is a full stack testing framework. It is used to do SpecBDD at the
Unit and Component level.

- We decided to ignore PHPSpec although it is easier to mock objects because
there are no working plugins at the moment with PHPStorm. Code completion is
not provided and makes it harder to know what mocking methods are available.

- Hamcrest is used in order to make tests easier to read.

- Mockery is used for testing interactions between objects and mocking them.

Codeception is good for:

- Unit testing with PHPUnit
- Component testing (multiple classes and Symfony services working together in
order to supply a feature)

## Behat

Behat is used for StoryBDD and functional tests.
application using either:
    - Symfony2 BrowserKit
    - PhantomJs
    - Firefox
    - Chrome

at the acceptance and functional level. Gherkin is used to writes stories.

Behat is good for:

- Testing Web Pages that includes Javascript.
Do not try to test directly your Javascript (Mocha and Chai would be better)
- Symfony Command line testing
- Functional testing (testing that pages work)

Running Functional tests using BrowserKit (fastest, but does not support JavaScript)
```
docker-compose run --rm php bin/behat -vvv
```

Running Functional tests in a real browser
```
docker-compose run --rm php bin/behat -vvv -p firefox
docker-compose run --rm php bin/behat -vvv -p chrome
```

Running Functional tests in phantomjs
```
docker-compose run --rm php bin/behat -vvv -p phantomjs
```

#### Screenshots
- Screenshots will be generated within the concrete project at `var/logs/screenshots/screenshot.png`
- The screenshot will be namespaced according to the profiles, for example `firefox_homepage.png`.
- A screenshot will be generated each time a scenario fails
- **Screenshots will not work using BrowserKit**, they only work for PhantomJs, Firefox and Chrome

## Running Unit tests under IntelliJ

1. Configure a new local PHP interpreter that points to `bin/php7-docker-ide` under `Settings > PHP`
2. Configure under `Settings > PHP > PHPUnit`
    - Use custom autoloader, using the `vendor/autoload.php`
    - Use optional configuration file, located within `./phpunit.xml.dist`

# Code Style
You can checkstyle your PHP code by running a dry-run with `make cs`
or if you want to automatically fix the code, you can run `make cs-fix`

# Debugging

If you want to enable XDebug run your command with the XDEBUG env variable : `XDEBUG=1 docker-compose ...`

if you want to always have XDebug enabled you can add these lines to your `docker-compose.override.yml` :

```yml
fpm:
    environment:
        XDEBUG: 1
        XDEBUG_CONFIG: idekey=PHPSTORM remote_host=YOUR_HOST_IP
        PHP_IDE_CONFIG: serverName=MAJISTI_SKELETON

tools:
    environment:
        XDEBUG: 1
        XDEBUG_CONFIG: idekey=PHPSTORM remote_host=YOUR_HOST_IP
        PHP_IDE_CONFIG: serverName=MAJISTI_SKELETON
```

Where *YOUR_HOST_IP* is the ip of the host machine (not the ip of the VM used for docker).

Now to make step debugging work within PHPStorm, you must do the following:

- Go into settings > Languages and Frameworks > PHP > Debug and change the port to 8000
- Click the *Listen for PHP Debug Connections* button. The icon is at the top right and looks like a phone.
- Go within PHP > Servers and add a new server named CUBE_API. You must also setup the Path Mapping.
    You must point your project to map to /var/www/html

Running your code within the command line will stop at the line where you place a breakpoint.
To debug within your browser, you must install the Chrome XDebug Helper extension. Once installed,
you can click on it and select "debug". Your code will stop on the line.

# Intellij

Plugins
-------
- Symfony Plugin
- PHP Toolbox
    Provides more code completion for PHP. For now, code completion was added for Mockery (see php-toolbox/mockery).
