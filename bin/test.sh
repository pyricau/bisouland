#!/bin/sh

bin/codecept build -c app/config/codeception.yml
bin/codecept run -c app/config/codeception.yml
