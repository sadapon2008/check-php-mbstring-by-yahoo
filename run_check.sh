#!/bin/bash

if [ $# -ne 1 ]; then
    exit 1
fi

if [ -e check.json ]; then
  \rm check.json
fi
if [ -e check_merged.json ]; then
  \rm check_merged.json
fi
if [ -e result.json ]; then
  \rm result.json
fi
for i in $(find $1 -type f \( -name '*.php' -o -name '*.ctp' \) -print); do
    echo $i
    php extract_string.php $i >>check.json
done
cat check.json | jq -s add >check_merged.json
php check_mb_sentence.php check_merged.json | tee result.json
