<?php

namespace quick\urlManager;

use quick\ComponentAbstract;
use quick\RouteNotFoundException;

class UrlManager extends ComponentAbstract implements UrlManagerInterface
{
    protected $routes;

    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
        return $this;
    }

    public function resolve($url)
    {
        if (!isset($this->routes[$url])) {
            throw new RouteNotFoundException('Url 404');
        }
        return $this->routes[$url];
    }
}
