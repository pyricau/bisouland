# CHANGELOG

This file logs the changes between versions.

## 4.0: Second v1 Rollback

v4: 2025 second v1 rollback, refactoring attempt.

* `4.0.23`: Qalin
    * replaced remnants of private messages with system notifications
    * removed old admin backend (news article management)
    * removed admin player
    * removed obsolete database fields (espion, confirmation, etc)
    * made monolith web app stateless (no sessions)
    * SQL performance improvements
    * created Qalin app in apps/qa (Symfony app)
    * created Qalin SignUpNewPlayer action
    * created Qalin InstantFreeUpgrade action
* `4.0.22`: fixed XSS vulnerability affecting auth cookie
* `4.0.21`: migrated from MySQL to PostgreSQL
* `4.0.20`: migrated from PHP 8.4 to PHP 8.5
* `4.0.19`: migrated to composer
* `4.0.18`: created front controller
* `4.0.17`: fixed PHP warning
* `4.0.16`: migrated from PHP 5.6 to PHP 8.4
* `4.0.15`: migrated from deprecated MySQL extension to PDO
* `4.0.14`: fixed Welcome Notification for new signed up players
* `4.0.13`: fixed clouds to start at 1 instead of 100
* `4.0.12`: created Smoke Tests (for monolith logged in player pages) [QA]
* `4.0.11`: removed private messages [Trust and Safety] 
* `4.0.10`: fixed Sign Up
* `4.0.9`: created Static Analysis (monolith planed for future scope) [QA]
* `4.0.8`: created Smoke Tests (for monolith public pages) [QA]
* `4.0.7`: created apps/qa, set PHP CS Fixer rules [QA]
* `4.0.6`: created apps/monolith, moved OG app to it
* `4.0.5`: removed chat [Trust and Safety]
* `4.0.4`: fixed encoding issues (now using UTF-8)
* `4.0.3`: removed email support [Trust and Safety]
* `4.0.2`: removed dead URLs and external Ad / Analytics
* `4.0.1`: migrated to Docker
* `4.0.0`: reset to v1, with added english documentation

## Previously

* v3: 2013 first v1 rollback, strangler fig pattern attempt
* v2: 2012 reboot (totally different)
* v1: 2005 original LAMP app
