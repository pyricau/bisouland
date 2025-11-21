# How to benchmark

1. start a fresh Monolith container
2. sign up and log in a temporary account
3. test load the homepage, as a visitor (not logged in)
4. test load the Brain page (logged in)

```console
# Start fresh
cd apps/monolith
make app-init

BENCH_USER="BisouTest_bench"
BENCH_PASS="SuperSecret123"

# Sign up
curl -X POST 'http://localhost:43000/inscription.html' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -d "Ipseudo=${BENCH_USER}&Imdp=${BENCH_PASS}&Imdp2=${BENCH_PASS}&inscription=S%27inscrire"

# Log in
BENCH_COOKIE=$(curl -X POST 'http://localhost:43000/redirect.php' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -d "pseudo=${BENCH_USER}&mdp=${BENCH_PASS}&connexion=Se+connecter" \
  -i -s | grep -i 'set-cookie: PHPSESSID' | sed 's/.*PHPSESSID=\([^;]*\).*/\1/' | tr -d '\r')

# Test load homepage (not signed in)
ab -l -q -k -c 50 -n 10000 http://localhost:43000/ \
    | grep -E "Complete requests|Failed requests|Exception|Requests per second|Time per request.*across"

# Test load Brain page (signed in)
ab -l -q -k -c 50 -n 10000 -C "PHPSESSID=$BENCH_COOKIE" http://localhost:43000/cerveau.html \
    | grep -E "Complete requests|Failed requests|Exception|Requests per second|Time per request.*across"
```

Example output:

```txt
Complete requests:      10000
Failed requests:        0
Requests per second:    484.54 [#/sec] (mean)
Time per request:       2.064 [ms] (mean, across all concurrent requests)
```

This means:

* `Complete requests`: how many requests were sent
* `Failed requests`: requests with connection errors, timeouts, or unexpected response properties
* `Requests per second`: average total requests handled by the server per second
* `Time per request`: average server response time

---

## 1st benchmark: from PHP 5.6 to PHP 8.4

Upgrading from PHP 5.6 to PHP 8.4, we observe the following performance improvments:

* Homepage: +8.9% improvement
* Brain Page: +30.2% improvement

### PHP 5.6

Homepage (Visitor - Not Logged In):

* Complete requests:      10000
* Failed requests:        0
* Requests per second:    545.67 [#/sec] (mean)
* Time per request:       1.833 [ms] (mean, across all concurrent requests)

Brain Page (Logged In User):

* Complete requests:      10000
* Failed requests:        0
* Requests per second:    313.88 [#/sec] (mean)
* Time per request:       3.186 [ms] (mean, across all concurrent requests)

### PHP 8.4

Homepage (Visitor - Not Logged In):

* Complete requests:      10000
* Failed requests:        0
* Requests per second:    594.49 [#/sec] (mean)
* Time per request:       1.682 [ms] (mean, across all concurrent requests)

Brain Page (Logged In User):

* Complete requests:      10000
* Failed requests:        0
* Requests per second:    408.78 [#/sec] (mean)
* Time per request:       2.446 [ms] (mean, across all concurrent requests)

---

## 2nd benchmark: from MySQL to PostgreSQL

Switching from MySQL to PostgreSQL, with persistent connections enabled,
we observe the following performance improvements:

* Homepage: +13.1% improvement
* Brain Page: +39.6% improvement

### MySQL with persistent connections

Homepage (Visitor - Not Logged In):

* Complete requests:      10000
* Failed requests:        0
* Requests per second:    1683.86 [#/sec] (mean)
* Time per request:       0.594 [ms] (mean, across all concurrent requests)

Brain Page (Logged In User):

* Complete requests:      10000
* Failed requests:        0
* Requests per second:    1309.55 [#/sec] (mean)
* Time per request:       0.764 [ms] (mean, across all concurrent requests)

### PostgreSQL with persistent connections

Homepage (Visitor - Not Logged In):

* Complete requests:      10000
* Failed requests:        0
* Requests per second:    1905.43 [#/sec] (mean)
* Time per request:       0.525 [ms] (mean, across all concurrent requests)

Brain Page (Logged In User):

* Complete requests:      10000
* Failed requests:        0
* Requests per second:    1828.19 [#/sec] (mean)
* Time per request:       0.547 [ms] (mean, across all concurrent requests)

---
