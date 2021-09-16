#!/bin/bash

cd ../app;
if type composer >/dev/null 2>&1
then
    echo 'Composer installed';
    echo 'Start installing composer components';
    composer install
    echo 'Composer components have been installed';

else
    echo 'Composer is not installed';
    echo 'Download it from https://getcomposer.org/download/';
    exit;
fi

cd ../;
echo 'Git init';
git init

cd source;


echo 'Copy Git post script';
cp scripts/post-merge ../.git/hooks/post-merge


if type bower >/dev/null 2>&1
then
    echo 'Bower installed';
else
    echo 'Installing bower';
    npm install -g bower
    echo 'Bower have been installed';
fi

echo 'Start installing bower components';
bower install
echo 'Bower components have been installed';


if type grunt >/dev/null 2>&1
then
    echo 'Grunt is installed';

else
    echo 'Installing grunt';
    npm install -g grunt-cli
    echo 'Grunt have been installed';
fi

echo 'Start grunt build';
grunt dev
echo 'Grunt build have been finished';