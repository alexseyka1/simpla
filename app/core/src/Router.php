<?php

namespace Simpla\Core;


class Router
{
    function &context() {
        static $context = [];
        return $context;
    }

    function dispatch(...$args) {

        $verb = strtoupper($_SERVER['REQUEST_METHOD']);
        $path = '/'.trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        # post method override
        if ($verb === 'POST') {
            if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                $verb = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
            } else {
                $verb = isset($_POST['_method']) ? strtoupper($_POST['_method']) : $verb;
            }
        }

        $responder = $this->serve($this->context(), $verb, $path, ...$args);
        $responder();
    }

    function route($verb, $path, callable $func) {
        $context = &$this->context();
        array_push($context, $this->action($verb, $path, $func));
    }

    function action($verb, $path, callable $func) {
        return function ($rverb, $rpath) use ($verb, $path, $func) {
            $rexp = preg_replace('@:(\w+)@', '(?<\1>[^/]+)', $path);
            if (
                strtoupper($rverb) !== strtoupper($verb) ||
                !preg_match("@^{$rexp}$@", $rpath, $caps)
            ) {
                return [];
            }
            return [$func, array_slice($caps, 1)];
        };
    }

    function match(array $actions, $verb, $path) {

        $cverb = strtoupper(trim($verb));
        $cpath = '/'.trim(rawurldecode(parse_url($path, PHP_URL_PATH)), '/');

        # test verb + path against route handlers
        foreach ($actions as $test) {
            $match = $test($cverb, $cpath);
            if (!empty($match)) {
                return $match;
            }
        }

        return [];
    }

    function response($body, $code = 200, array $headers = []) {
        return function () use ($body, $code, $headers) {
            $this->render($body, $code, $headers);
        };
    }

    function redirect($location, $code = 302) {
        return function () use ($location, $code) {
            $this->render('', $code, ['location' => $location]);
        };
    }

    function serve(array $actions, $verb, $path, ...$args) {
        $pair = $this->match($actions, $verb, $path);
        $func = array_shift($pair) ?: function () { return $this->response('', 404, []); };
        $caps = array_shift($pair) ?: null;
        return empty($caps) ? $func(...$args) : $func($caps, ...$args);
    }

    function render($body, $code = 200, $headers = []) {
        http_response_code($code);
        if($code == 404)
            exit($this->phtml(BASE_URL . '/app/views/404'));

        array_walk($headers, function ($value, $key) {
            if (! preg_match('/^[a-zA-Z0-9\'`#$%&*+.^_|~!-]+$/', $key)) {
                throw new \InvalidArgumentException("Invalid header name - {$key}");
            }
            $values = is_array($value) ? $value : [$value];
            foreach ($values as $val) {
                if (
                    preg_match("#(?:(?:(?<!\r)\n)|(?:\r(?!\n))|(?:\r\n(?![ \t])))#", $val) ||
                    preg_match('/[^\x09\x0a\x0d\x20-\x7E\x80-\xFE]/', $val)
                ) {
                    throw new \InvalidArgumentException("Invalid header value - {$val}");
                }
            }
            header($key.': '.implode(',', $values));
        });
        print $body;
    }

    function page($path, array $vars = []) {
        return function () use ($path, $vars) {
            return $this->response($this->phtml($path, $vars));
        };
    }

    function phtml($path, array $vars = []) {
        ob_start();
        extract($vars, EXTR_SKIP);
        require "{$path}.phtml";
        return trim(ob_get_clean());
    }
}