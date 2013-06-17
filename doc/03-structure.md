# Project structure

Bisouland v1 has been created in 2005 by a someone who was learning web
development. Therefore it probably contains security holes, bugs, low quality
code and bad design pattern.

The v3 is a roll back to the v1, with the purpose to fix and improve the legacy
code with a progressive rewrite using the full-stack framework Symfony2.

## Application routing

Pages managed by the legacy application are listed in the configuration of the
web server, if the requested page is not among them, the Symfony2 application
will be launched.

## Legacy application

The legacy application is located in the `web` directory, with `web/index.php`
as the entry point
([front controller](http://en.wikipedia.org/wiki/Front_Controller_pattern)).

Example of the flow for the homepage:

1. Request sent with `/accueil.html` as URI;
2. the web server rewrites the query to `/index.php?page=accueil`;
3. the front controller checks if the `page` parameter exists, by looking in
   the predefined pages in `web/phpincludes/pages.php`;
4. includes the `web/phpincludes/accueil.php`.

## Symfony2 application

The whole project structure is a standard distribution of the full-stack
Symfony2 framework.
