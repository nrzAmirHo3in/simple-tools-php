<?php

namespace SimpleTools;

class Router
{
    private array $handlers;
    private const METHOD_POST = "POST";
    private const METHOD_GET = "GET";
    private $notFoundHandler;
    /**
     * @param string $path URI
     * @param callable $handler Callback function with URI request
     * @return void
     */
    public function get(string $path, callable $handler): void
    {
        $this->addHandler(self::METHOD_GET, $path, $handler);
    }
    /**
     * @param string $path URI
     * @param callable $handler Callback function with URI request
     * @return void
     */
    public function post(string $path, callable $handler): void
    {
        $this->addHandler(self::METHOD_POST, $path, $handler);
    }
    public function run()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI']);
        $requestPath = $requestUri["path"];
        $method = $_SERVER['REQUEST_METHOD'];
        $callback = null;
        foreach ($this->handlers as $handler) {
            if ($handler["path"] === $requestPath && $method === $handler["method"]) {
                $callback = $handler["handler"];
            }
        }
        if (!$callback) {
            header("HTTP/1.0 404 Not Found");
            if (!empty($this->notFoundHandler))
                $callback = $this->notFoundHandler;
            else
                return;
        }

        call_user_func_array($callback, [
            array_merge($_GET, $_POST)
        ]);
    }
    /**
     * @param string $method Method of request [GET, POST]
     * @param string $path URI
     * @param callable $handler Callback function with URI request
     * @return void
     */
    private function addHandler(string $method, string $path, callable $handler): void
    {
        $this->handlers[$method . $path] = [
            "path" => $path,
            "method" => $method,
            "handler" => $handler
        ];
    }
    public function addNotFoundHandler(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }
}