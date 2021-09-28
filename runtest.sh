#!/bin/bash
#


echo Emptying root tests directory
rm -rf tests/acceptance;
rm -rf tests/functional;
rm -rf tests/unit;
echo Copying tests from $1;
cp -r vendor/xepan/$1/tests/* tests
echo "codecept run ${2:-acceptance} --env local_mac --steps --html"
codecept run ${2:-acceptance} --env local_mac --steps --html
