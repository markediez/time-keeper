#!/bin/bash
# This shell script creates a database for time keeper in a mamp mysql database

echo "Enter username"
read user
echo "Enter password"
read pass
echo "Enter database name"
read db

if [[ -z "$pass" ]]; then
  pass=""
else
  pass="-p$pass"
fi

/Applications/MAMP/Library/bin/mysql -u$user $pass $db < init.sql
