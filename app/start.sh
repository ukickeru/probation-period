#!/bin/bash

# Run SSH daemon
/usr/sbin/sshd -D </dev/null &>/dev/null &

# Run Symfony web server
symfony server:start --port=8080
