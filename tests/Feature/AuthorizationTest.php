<?php

use Illuminate\Routing\Router;

it('ensures all api-prefixed routes with auth:sanctum middleware use Gate', function () {
    $router = app(Router::class);

    // Get all routes that have the prefix 'shop' and middleware 'auth:sanctum'
    $routes = collect($router->getRoutes())->filter(function ($route) {
        return str_starts_with($route->uri(), 'api/') && in_array('auth:sanctum', $route->middleware());
    });


    // Ensure that at least one route is found
    expect($routes->isNotEmpty())->toBeTrue();
    $skipControllers = [];

    // Check if the guard statement is used in the route's controller method
    $routes->each(function ($route) use ($skipControllers) {
        $action = $route->getAction();

        // Assert that the route uses a controller
        expect($action['uses'])->toBeString();

        // Fetch the controller and method
        [$controller, $method] = explode('@', $action['uses']);


        // Ensure the method exists in the controller before inspecting it
        if (!method_exists($controller, $method) ||  in_array($controller, $skipControllers)) {
            return; // Skip if the method doesn't exist
        }

        // Use reflection to inspect the controller method
        $reflector = new ReflectionMethod($controller, $method);
        $methodContent = file($reflector->getFileName());

        $methodLines = array_slice($methodContent, $reflector->getStartLine() - 1, $reflector->getEndLine() - $reflector->getStartLine());
        $methodBody = implode('', $methodLines);


         // Now extract only the lines inside the method body braces `{ }`
         preg_match('/{(.*)}/s', $methodBody, $matches);
         $methodBodyInsideBraces = $matches[1] ?? '';


        // Remove comments (single-line and multi-line) and trim
        $codeWithoutComments = trim(preg_replace(['!/\*.*?\*/!s', '/\/\/[^\n]*/'], '', $methodBodyInsideBraces));


        // Skip methods that have an empty body
        if (empty($codeWithoutComments)) {
            return; // Skip if the method body is empty
        }

        // Assert that the method contains the 'Gate' statement

        if (strpos($methodBody, 'Gate') === false) {
            // dd($codeWithoutComments, $methodBodyInsideBraces);
            $message = "Failed asserting that the method [{$controller}::{$method}] contains 'Gate'.";
            throw new Exception($message);
        }
    });
});
