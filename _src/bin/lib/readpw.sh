#!/bin/bash

function readpass() {
    stty -echo; read -p "$1" ans; stty echo;
    echo $ans;
}
