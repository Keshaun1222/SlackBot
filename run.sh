#!/bin/bash

while true; do
    begin=`date +%s`
    php waralert.php
    end=`date +%s`
    if [ $(($end - $begin)) -lt 10 ]; then
        sleep $(($begin + 19 - $end))
    fi
done