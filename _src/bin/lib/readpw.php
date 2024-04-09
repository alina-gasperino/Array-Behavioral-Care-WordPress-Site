<?php

class Input
{
    static public function password($prompt = '') {
        !empty($prompt) && (print $prompt);
        system('stty -echo');
        $pw = trim(fgets(STDIN));
        system('stty echo');
        return $pw;
    }
}
