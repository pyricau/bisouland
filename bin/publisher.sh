#!/usr/bin/env bash

dirname="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

for blog in $dirname/../blog/*; do
    cd $blog
    ./vendor/bin/carew build
done
