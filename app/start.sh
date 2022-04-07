#!/bin/bash

# Run SSH daemon
/usr/sbin/sshd -D </dev/null &>/dev/null &

# Run Symfony web server
printf '\nStarting Symfony server...\n\n'
symfony server:stop
symfony server:start -d --port=8080
symfony server:log
