<?php

namespace Simpla\Core;

use Simpla\Core\{
    Debug, Router
};

class Simpla
{
    public function run(array $config){
        try{
            if(empty($config))
                throw new \Exception('Не передана конфигурация приложения!');
            $this->_route($config['routes']);
        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }

    private function _route(array $routes){
        try{
            if(empty($routes))
                throw new \Exception('Не передан массив маршрутов');
            if(!is_array($routes))
                throw new \Exception('Передан не массив маршрутов!');

            $router = new Router();
            $rts = [];
            /**
             * $router->action('GET', '/books/:id', $router->page('app/views/book', ['args' => [1,2,3] ]) ),
            $router->action('GET', '/about', $router->page('app/views/about'))
             */
            foreach ($routes as $route) {
                if (empty($route->method))
                    throw new \Exception('В маршруте не указан метод запроса!');
                if (empty($route->url))
                    throw new \Exception('В маршруте не указан url!');
                if (empty($route->func))
                    throw new \Exception('В маршруте нет вызываемой функции!');
                if (!is_callable($route->func))
                    throw new \Exception('Указанная функция в маршруте невызываема (Not collable)!');
                $rts[] = $router->action($route->method, $route->url, $route->func);
            }
            $verb = $_SERVER['REQUEST_METHOD'];
            $path = $_SERVER['REQUEST_URI'];
            $responder = $router->serve($rts, $verb, $path);

            if(is_callable($responder))
                $responder();
            return 1;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }
}