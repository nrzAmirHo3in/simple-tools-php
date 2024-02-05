<?php

require_once __DIR__ . "/vendor/autoload.php";

use SimpleTools\Router;
use SimpleTools\Database;

// Router:
try {
    $router = new Router;

    $router->get("/", function () {
        echo "Home page";
    });

    $router->get("/about-us", function () {
        echo "About us page";
    });

    $router->get("/contact-us", function () {
        require_once __DIR__ . "/test/form.php";
    });

    $router->post("/contact", function ($values) {
        var_dump($values);
    });

    $router->addNotFoundHandler(function () {
        echo "not found";
    });

    $router->run();

} catch (\Throwable $th) {
    echo $th;
}

// Database:

try {
    $db = new Database;
    // First you have to add path of your database in ./src/Database.php
} catch (\Throwable $th) {
    echo $th;
}