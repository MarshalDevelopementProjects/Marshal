<?php

namespace Core;

require __DIR__ . "/../vendor/autoload.php";

class View
{
    public static function render(string|int $view, array $args = []): void
    {
        // access the view files relative to Core directory 
        $file = __DIR__ . '/../View/src';
        if (is_string($view)) {
            $file .= $view;
        } else if (is_numeric($view)) {
            $file .= "$view.html";
        }

        $data = json_encode($args);
        if (is_readable($file)) {
            require $file;
        } else {
            require __DIR__ . '/../View/src/errors/500.html';
        }
    }
}
