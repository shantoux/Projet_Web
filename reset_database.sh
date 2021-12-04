#!/usr/bin/env bash

while read -r; do
  if [[ $REPLY =~ ^(host) ]]; then
    $host = ${REPLY##* }
  elif [[ $REPLY =~ ^(user) ]]; then
    $user = ${REPLY##* }
  elif [[ $REPLY =~ ^(password) ]]; then
    $pw = ${REPLY##* }
  fi
done < $1

echo $host
echo $user
echo $pw
