# SkySwoon (Bisouland)

SkySwoon (originally Bisouland) is a free browser-based strategy game that turns
romance into competitive gameplay.

Join other players online as you strategically send **Kisses** to capture
**Love Points** and dominate the clouds.

Simple to learn, engaging to master, and completely free to play.

A delightfully retro online strategy game from 2005, lovingly preserved in its
original state. Experience a fascinating time capsule of early web development
and quirky game design.

## Installation

Requirements, the ancient LAMP stack:

* **Linux**: any version will do. I hope
* **Apache**: 2.0+ (full backward compatibility with .htaccess and htpasswd)
* **MySQL**: 4.1 - 5.7 (MySQL 8.0+ breaks compatibility with old mysql extension)
* **PHP**: 4.3 - 5.6 (uses deprecated `mysql_pconnect()`, `mysql_select_db()` functions)

To install SkySwoon, first get its sources:

```console
git clone git://github.com/pyricau/bisouland.git skyswoon
cd skyswoon
```

Use GNU Make to run the project's mundane commands:

```console
# 🐳 Docker related rules
## Build the Docker image
make build

## Start the services (eg database, message queue, etc)
make up

## Check the services logs
make logs

## Stop the services
make down

## Open interactive shell in container
make bash

# 🐘 Project related rules
## Drops, Create and Imports database & schema
make db-rest
```

The website will then be available at: http://localhost:8080

Then follow these 3 small steps to configure it.

### Configuration

For different environments, copy `.env` (eg into `.env.local`) and change its values:

```bash
# Database
DATABASE_HOST=db
DATABASE_USER=skyswoon
DATABASE_PASSWORD=skyswoon_pass
DATABASE_NAME=skyswoon
# MySQL root password (for Docker)
MYSQL_ROOT_PASSWORD=root_password

# Email
EMAIL_ADMIN=admin@skyswoon.local
EMAIL_EXPEDITOR=noreply@skyswoon.local
```

### Administration access

The administration area is protected using
[`.htaccess` and `.htpasswd` files](http://weavervsworld.com/docs/other/passprotect.html).

First of all, create them:

```console
cp web/news/.htaccess.dist web/news/.htaccess
touch web/news/.htpasswd
```

Then simply set the absolute path of the project in the `web/news/.htaccess`
file, and put a
[generated password](http://www.htaccesstools.com/htpasswd-generator/) in the
`web/news/.htpasswd` file.

### Emailing

Now that everything is configured, check email sending for registration and
newsletter.

## Structure

### eXtreme Legacy

The `web` folder contains the original, 2005 LAMP stack, application.

It follows the "Classic PHP eXtreme Legacy Architecture", a single monolithic
file that handles everything:

* **everything in one place**: authentication, database queries, game logic,
  routing, and HTML templating all mixed together in `index.php`
* **mixed concerns**: business logic sits right next to presentation code
* **procedural style**: no classes, no stateless functions,
  just sequential PHP code from top to bottom
* **direct database access**: raw MySQL queries scattered throughout
  with potential security vulnerabilities
* **template includes**: HTML structure with `<?php include($some_file); ?>`
  to pull in page content
* **global state everywhere**: heavy reliance on `$_SESSION`, global variables,
  and direct database mutations
* also everything is in French

Something along the lines of:

```php
<?php
// File: web/index.php

session_start();
include 'phpincludes/bd.php';
bd_connect();

// Cookie-based auth with SQL injection vulnerability
if (isset($_COOKIE['pseudo']) && isset($_COOKIE['mdp'])) {
    $sql = mysql_query("SELECT * FROM membres WHERE pseudo='".$pseudo."'");
    if ($donnees_info['mdp'] == $mdp) {
        $_SESSION['logged'] = true;
    }
}

// Game logic calculations mixed throughout
$amour = calculterAmour($amour, $timeDiff, $nbE[0][0], $nbE[1][0]);
mysql_query("UPDATE membres SET timestamp='".time()."', amour='".$amour."' WHERE id='".$id."'");

// Page routing
$page = (!empty($_GET['page'])) ? htmlentities($_GET['page']) : 'accueil';
$include = 'phpincludes/'.$array_pages[$page];
?>

<!DOCTYPE html>
<html>
<head><title><?php echo $title; ?></title></head>
<body>
  <div><?php echo formaterNombre(floor($amour)); ?> points</div>
  <div id="corps"><?php include($include); ?></div>
</body>
</html>
```

## Further documentation

You can find more documentation at the following links:

* Copyright and Apache 2 license: [LICENSE.md](LICENSE.md)
* change logs: [CHANGELOG.md](CHANGELOG.md)
* versioning and branching models,
  as well as public API: [VERSIONING.md](VERSIONING.md)
* contribution instructions: [CONTRIBUTING.md](CONTRIBUTING.md)

## Project history

* 2025: roll back to version 1, again; now version 4
* 2013: roll back to the version 1, which becomes the version 3
* 2012: release of the version 2
* 2011: Open-sourcing of the project, new team to take over the project with
  Marc Epron, Thomas Gay and Loïc Faugeron
* 2005: creation of the project by **Pierre-Yves Ricau**
