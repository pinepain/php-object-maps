#!/bin/bash

set -e

echo Installing php-weak PHP extension ...


PHP_WEAK_VERSION=$1

cd $HOME

if [ ! -d "$HOME/php-weak" ]; then
  git clone https://github.com/pinepain/php-weak.git
else
  echo 'Using cached directory.';
  cd $HOME/php-weak
  git fetch
fi

cd $HOME/php-weak
git checkout ${PHP_WEAK_VERSION}

phpize --clean && phpize && ./configure && make

make test
make install
