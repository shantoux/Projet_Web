#!/usr/bin/env bash

while read -r
do [[ $line =~ ^(host) ]] && echo "$line"
done < $1
