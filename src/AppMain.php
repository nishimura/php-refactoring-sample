<?php

namespace Bbs;

use FastRoute;

class AppMain
{
    public static function run(): void
    {
        $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
            $r->get('/[{id:\d+}]', [Io\Page\MemoPage::class, 'index']);
            $r->post('/', [Io\Page\MemoPage::class, 'create']);
            $r->post('/{id:\d+}', [Io\Page\MemoPage::class, 'update']);
            $r->post('/{id:\d+}/delete', [Io\Page\MemoPage::class, 'delete']);
        });

        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            http_response_code(404);
            echo "404 Not Found";
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            http_response_code(405);
            echo "405 Method Not Allowed";
            break;
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];
            if (!is_callable($handler)){
                http_response_code(404);
                echo "404 Not Found";
                break;
            }

            $ret = $handler($vars);
            if ($ret instanceof Io\Infrastructure\Response\Response){
                $ret->respond();
            }
            break;
        }
    }
}
