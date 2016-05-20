<?php
namespace quick\urlManager;

interface UrlManagerInterface
{
    public function setRoutes(array $routes);

    public function resolve($url);
}