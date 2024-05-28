<?php

namespace API_Models;

class BaseRequest
{
    private string $_requestMethod;

    public function __construct(){
        $this->_requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @return bool
     */
    public function getIsGetRequest(): bool
    {
        return $this->_requestMethod == "get";
    }

    /**
     * @return bool
     */
    public function getIsPostRequest(): bool
    {
        return $this->_requestMethod == "post";
    }
}