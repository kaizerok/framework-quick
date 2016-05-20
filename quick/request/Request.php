<?php

namespace quick\request;
use quick\ComponentAbstract;

/**
 * Class Request
 * @property string $requestUri The request URI portion for the currently requested URL.
 * @package quick\request
 */
class Request extends ComponentAbstract
{
    private $requestUri;

    /**
     * @return mixed
     */
    public function getRequestUri()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $this->requestUri = $_SERVER['REQUEST_URI'];
            if (!empty($_SERVER['HTTP_HOST'])) {
                if (strpos($this->requestUri, $_SERVER['HTTP_HOST']) !== false) {
                    $this->requestUri = preg_replace('/^\w+:\/\/[^\/]+/', '', $this->requestUri);
                }
            } else {
                $this->requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $this->requestUri);
            }
        }
        return $this->requestUri;
    }

    /**
     * Returns the request type, such as GET, POST, HEAD, PUT, PATCH, DELETE.
     * @return string request type.
     */
    public function getRequestType()
    {
        if (isset($_POST['_method'])) {
            return strtoupper($_POST['_method']);
        }

        return strtoupper(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET');
    }
}
