<?php

function println ($string) {
    $_SERVER['SERVER_PROTOCOL'] ? print "$string<br />" : print "$string\n";
}