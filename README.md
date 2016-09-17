# Introduction

This is the Majisti's Scaffolding project. We use it here to bootstrap projects. This is not a generator, just a place
where to centralize the knowledge of creating rich applications with a PHP backend and frontend (JavaScript, SCSS, etc.).

The following is provided out of the box:

- Symfony 3 setup
- Full testing suite configured. Acceptance, Functional, Integration and Unit
- Everything runs under Docker. Minimal global project dependencies: git, docker and docker-compose.
- Gulp with SCSS, Bourbon and Susy for the frontend.
- Everything runs under a Makefile and is ready for Continuous Integration
- Please stay tuned for the React & Redux boilerplate

# Roadmap

## Short term

- CodeCoverage
- SCSS Style check
- Javascript Style check

## Long term
- Installer
- React/Redux scaffold

## Experimentations

- **PHPSpec** [Rejected]
    PHPSpec was dropped in favor of Codeception and Mockery. Though we prefer PHPSpec for writing shorter tests,
    the code completion was not enough within Intellij. We were left blind and needed to read the documentation in order
    to develop tests.
- Bowling Kata
    - Game Score Calculation [Done & Automatically Tested]
    - API with Dunglas API [Todo]
    - ...
- Blackjack Game
    - You can play a game of BlackJack within the Symfony Console! Just run the command `docker-compose run --rm php bin/console majisti:game:blackjack`
    - Splitting Pairs [Todo]
    - Betting [Todo]
    - Persisting Game [Todo]
    - Statistics [Todo]
    
# The BlackJack Game

This boilerplate comes with a BlackJack game, playable within the Symfony Console. It was programmed in order to showcase full stack testing techniques.

- Behat is used for testing the console command
- Codeception is used to test at the Component level. This is were we test the edge cases from the rules.
- PHPUnit is used for Unit testing
- Mockery is used as the mocking framework
- Hamcrest is used as the assertion library
- AspectMock is used for testing some randomness (such as the PHP's rand function)

Here is a little screenshot:

![alt text](https://raw.githubusercontent.com/majisti/boilerplate/develop/web/assets/img/blackjack-symfony-console.png "Symfony Console BlackJack Game")


## Known Bugs

- Step debugging: Path Mapping with XDebug fails when using Mockery.

# Installation

## Docker & docker-compose
In order to setup this project without playing around with global dependencies,
use docker and docker-compose.

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

- Go into settings > Languages and Frameworks > PHP > Debug and change the port to 7500
- Click the *Listen for PHP Debug Connections* button. The icon is at the top right and looks like a phone.
- Go within PHP > Servers and add a new server named CUBE_API. You must also setup the Path Mapping.
    You must point your project to map to /var/www/html

Running your code within the command line will stop at the line where you place a breakpoint.
To debug within your browser, you must install the Chrome XDebug Helper extension. Once installed,
you can click on it and select "debug". Your code will stop on the line.

Note: for now, step debugging using bin/test_env.sh will not work.
You need to use docker-compose run --rm php bin/codecept -v run

# Intellij

Plugins
-------
- Symfony Plugin
- PHP Toolbox
    Provides more code completion for PHP. For now, code completion was added for Mockery (see php-toolbox/mockery).
