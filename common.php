<?php

function section($title) {
    $BACKGND = "\033[0;42m*";
    $GREEN = "\033[0;32m*";
    $NC = "\033[0m" . PHP_EOL;

    readline();

    echo str_repeat(PHP_EOL, 50 );
    echo "$BACKGND                                                              $NC";
    echo "$GREEN $title $NC" ;
    echo "$BACKGND                                                              $NC";
}
