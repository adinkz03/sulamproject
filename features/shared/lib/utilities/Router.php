<?php
/**
 * Router
 * Central routing logic for the application
 */

class Router {
    private $routes = [];
    
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }
    
    private function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    public function dispatch($method, $uri) {
        // Parse URI and remove query string
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Remove trailing slash except for root
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri)) {
                return $this->executeHandler($route['handler']);
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        echo '404 - Page Not Found';
        exit();
    }
    
    private function matchPath($routePath, $uri) {
        // Simple exact match for now
        // Can be extended with pattern matching later
        return $routePath === $uri;
    }
    
    private function executeHandler($handler) {
        if (is_callable($handler)) {
            return call_user_func($handler);
        }
        
        if (is_array($handler) && count($handler) === 2) {
            list($controllerClass, $method) = $handler;
            
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $method)) {
                    return $controller->$method();
                }
            }
        }
        
        http_response_code(500);
        echo '500 - Internal Server Error';
        exit();
    }
}
