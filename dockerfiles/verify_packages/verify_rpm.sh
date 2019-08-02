#!/bin/sh
set -xe
rpm -Uvh /build_src/build/packages/*.rpm

php -m | grep ddtrace
php -r 'echo phpversion("ddtrace") . PHP_EOL;'
php -r 'echo (DDTrace\Bridge\dd_tracing_enabled() ? "TRUE" : "FALSE") . PHP_EOL;'
