# How to install

Requirements:

* [Docker](https://www.docker.com/)
* [GNU Make](https://www.gnu.org/software/make/)

Use the docker containers to install and run the application,
with the help of the Makefile:

```console
# Builds the Docker images, start the Docker services, resets the database
# For all the apps (monolith, qa, etc)
make apps-init

# Runs the full QA pipeline (CS check, static analysis, tests, etc)
# For all the apps (monolith, qa, etc)
make apps-qa
```

The website can then be accessed at this URL: http://localhost:43000.

BisouLand is actually divided into apps:

* `apps/monolith`: the eXtreme Legacy (2005 LAMP) app,
  that's the website you browse at http://localhost:43000
* `apps/qa`: tools to improve and maintain the high quality of BisouLand,
  that'd be tests, coding standards, static analysis, etc

Find more documentation for each app in their respective directories.
