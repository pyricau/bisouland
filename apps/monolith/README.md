# Monolith

The main (original) BisouLand application.

Requirements (LAMP stack):

* **Linux**: any version will do. I think?
* **Apache**: 2.0+ (full backward compatibility with .htaccess and htpasswd)
* **MySQL**: 4.1 - 5.7 (MySQL 8.0+ breaks compatibility with old mysql extension)
* **PHP**: 4.3 - 5.6 (uses deprecated `mysql_pconnect()`, `mysql_select_db()` functions)

Use GNU Make to run the project's mundane commands:

```console
# 📱 App related rules
## First install / complete reset (Docker build, up, db-reset)
make app-init

# 🐳 Docker related rules
## Build the Docker images
make docker-build

## Start the services (eg database, message queue, etc)
make docker-up

## Check the services logs
make docker-compose arg='logs --tail=0 --follow'

## Stop the services
make docker-down

## Open a bash shell in the container
make docker-bash

# ⛁ Database related rules
## Drops, creates and imports database & schema
make db-reset

# Discover everything you can do
make
```

The website will then be available at: http://localhost:43000

Then follow these 2 small steps to configure it.

#### Configuration

For different environments, copy `.env` (eg into `.env.local`) and change its values:

```bash
# Database
DATABASE_HOST=db
DATABASE_USER=bisouland
DATABASE_PASSWORD=bisouland_pass
DATABASE_NAME=bisouland
# MySQL root password (for Docker)
MYSQL_ROOT_PASSWORD=root_password
```

#### Administration access

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
