# Bisouland

Bisouland is a free online strategy-game. In order to play you only need a
web browser.

Take your opponents love points by sending them kisses!

## Installation

1. Create the file `web/news/.htpasswd`;
2. configure the database in [web/phpincludes/bd.php](web/phpincludes/bd.php);
3. check email sending for registration;
4. put the admin email in the variable `$destinataire`
   from [web/news/mail.php](web/news/mail.php).

## Structure

Every request will go to the Symfony2 application. If the route is not found
in it, the legacy application will be boostraped.

**Warning**: the legacy application, which lies in the `web` directory,
has been created in 2005 by a someone who was learning web development.
Therefore it probably contains security holes, bugs, low quality code
and bad design pattern.

## Further documentation

You can find more documentation at the following links:

* Copyright and Apache 2 license: [LICENSE.md](LICENSE.md);
* version and change logs: [VERSION.md](VERSION.md)
  and [CHANGELOG.md](CHANGELOG.md);
* versioning and branching models,
  as well as public API: [VERSIONING.md](VERSIONING.md);
* contribution instructions: [CONTRIBUTING.md](CONTRIBUTING.md).

## Project history

* 2013: roll back to the version 1, which becomes the version 3;
* 2012: release of the version 2;
* 2011: Open-sourcing of the project, new team to take over the project with
  Marc Epron, Thomas Gay and Loïc Chardonnet;
* 2005: creation of the project by **Pierre-Yves Ricau**.
