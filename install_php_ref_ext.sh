#!/bin/bash

set -e

echo Installing php-ref PHP extension ...


PHP_WEAK_VERSION=$1

cd $HOME

if [ ! -d "$HOME/php-ref" ]; then
  git clone https://github.com/pinepain/php-ref.git
else
  echo 'Using cached directory.';
  cd $HOME/php-ref
  git pull
fi

cd $HOME/php-ref
git checkout ${PHP_WEAK_VERSION}

phpize --clean && phpize && ./configure && make

make test
make install
