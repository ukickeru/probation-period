#!/bin/bash

# Run SSH daemon
/usr/sbin/sshd -D </dev/null &>/dev/null &

# Run Symfony web server
ps -C symfony

if [[ -$? -ne 0 ]]
then
  >&1 echo 'Start Symfony server...'
  symfony server:start --port=8080
else
  >&1 echo 'Symfony server is already running!'
fi
