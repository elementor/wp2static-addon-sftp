#!/bin/bash

######################################
##
## Build WP2Static Zip Deployment Addon
##
## script archive_name dont_minify
##
## places archive in $HOME/Downloads
##
######################################

# run script from project root
EXEC_DIR=$(pwd)

TMP_DIR=$HOME/plugintmp
rm -Rf $TMP_DIR
mkdir -p $TMP_DIR

rm -Rf $TMP_DIR/wp2static-addon-sftp
mkdir $TMP_DIR/wp2static-addon-sftp


# clear dev dependencies
rm -Rf $EXEC_DIR/vendor/*
# load prod deps and optimize loader
composer install --no-dev --optimize-autoloader


# cp all required sources to build dir
cp -r $EXEC_DIR/wp2static-addon-sftp.php $TMP_DIR/wp2static-addon-sftp/
cp -r $EXEC_DIR/src $TMP_DIR/wp2static-addon-sftp/
cp -r $EXEC_DIR/assets $TMP_DIR/wp2static-addon-sftp/
cp -r $EXEC_DIR/vendor $TMP_DIR/wp2static-addon-sftp/
cp -r $EXEC_DIR/readme.txt $TMP_DIR/wp2static-addon-sftp/
cp -r $EXEC_DIR/views $TMP_DIR/wp2static-addon-sftp/
cp -r $EXEC_DIR/admin $TMP_DIR/wp2static-addon-sftp/
cp -r $EXEC_DIR/js $TMP_DIR/wp2static-addon-sftp/

cd $TMP_DIR

# tidy permissions
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;

# strip comments and whitespace from each PHP file
if [ -z "$2" ]; then
  find .  ! -name 'wp2static-addon-sftp.php' -name \*.php -exec $EXEC_DIR/tools/compress_php_file {} \;
fi

zip -r -9 ./$1.zip ./wp2static-addon-sftp

cd -

mkdir -p $HOME/Downloads/

cp $TMP_DIR/$1.zip $HOME/Downloads/

# reset dev dependencies
cd $EXEC_DIR
# clear dev dependencies
rm -Rf $EXEC_DIR/vendor/*
# load prod deps
composer install
