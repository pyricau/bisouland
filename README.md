# Bisouland

Bisouland is a free online strategy-game. In order to play you only need a
web browser.

Take your opponents love points by sending them kisses!

## Installation

To install Bisouland, [check the requirements](doc/02-requirements.md) and then
download and use its installation script:

    curl -sS  https://raw.github.com/pyricau/bisouland/master/bin/install.sh | sh

### Administration access

The administration area is protected using
[`.htaccess` and `.htpasswd` files](http://weavervsworld.com/docs/other/passprotect.html).

First of all, create them:

    cp web/news/.htaccess.dist web/news/.htaccess
    touch web/news/.htpasswd

Then simply set the absolute path of the project in the `web/news/.htaccess`
file, and put a
[generated password](http://www.htaccesstools.com/htpasswd-generator/) in the
`web/news/.htpasswd` file.

### Emailing

Now that everything is configured, check email sending for registration and
newsletter.

## Further documentation

You can find more documentation at the following links:

* Copyright and Apache 2 license: [LICENSE.md](LICENSE.md);
* version and change logs: [VERSION.md](VERSION.md)
  and [CHANGELOG.md](CHANGELOG.md);
* versioning and branching models,
  as well as public API: [VERSIONING.md](VERSIONING.md);
* contribution instructions: [CONTRIBUTING.md](CONTRIBUTING.md);
* more can be found in the [doc](doc) directory.

## Project history

* 2013: roll back to the version 1, which becomes the version 3;
* 2012: release of the version 2;
* 2011: Open-sourcing of the project, new team to take over the project with
  Marc Epron, Thomas Gay and Loïc Chardonnet;
* 2005: creation of the project by **Pierre-Yves Ricau**.
