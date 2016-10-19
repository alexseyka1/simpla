<?php
/**
 * Created by PhpStorm.
 * User: alexseyka1
 * Date: 13.10.16
 * Time: 12:48
 */

namespace Simpla\Core\Classes;


class Route
{
    public $method;
    public $url;
    public $func;

    /**
     * Route constructor.
     * @param $method
     * @param $url
     * @param $func
     */
    public function __construct($method, $url, $func)
    {
        $this->method = $method;
        $this->url = $url;
        $this->func = $func;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getFunc()
    {
        return $this->func;
    }

    public function getObject(){
        return new \stdClass([
            'method' => $this->method,
            'url' => $this->url,
            'function' => $this->func
        ]);
    }


}