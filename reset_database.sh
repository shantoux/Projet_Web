#!/usr/bin/env bash

while read -r line; do
  if [[ $line =~ ^(host) ]]; then
    $host = ${line##* }
  elif [[ $line =~ ^(user) ]]; then
    $user = ${line##* }
  elif [[ $line =~ ^(password) ]]; then
    $pw = ${line##* }
  fi
done < $1

echo $host
echo $user
echo $pw
