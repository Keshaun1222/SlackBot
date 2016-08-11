#!/bin/bash
for loop in 1 2 3 4 5 6; do
    php waralert.php &
    sleep 10
done