#!/bin/bash

if type npm >/dev/null 2>&1 && type node >/dev/null 2>&1
then
    echo 'Node.js installed';
    npm -v
    echo 'Start installing node modules';
    npm install
else
    echo 'Node not installed';
    echo 'Download it from https://nodejs.org';
    exit;
fi
