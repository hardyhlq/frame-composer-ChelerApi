#!/bin/bash
# 使用方法 /PATH/update.sh proto文件
DIR="$( cd "$( dirname "$0"  )" && pwd  )"
FILE=$1
PROTOC=`which protoc`
if [[ $? -eq 0 ]]; then
    $PROTOC --php_out=$DIR --proto_path=$DIR $FILE
else
    echo "protoc is not install"
fi