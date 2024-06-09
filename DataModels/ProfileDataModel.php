<?php

namespace DataModels;

class ProfileDataModel
{
    private string $_name;
    private string $_login;
    private string $_phone;

    public function getName(): string
    {
        return $this->_name;
    }

    public function getLogin(): string
    {
        return $this->_login;
    }

    public function getPhone(): string
    {
        return $this->_phone;
    }

    public function __construct(string $name = "", string $login = "", string $phone = ""){
        $this->_login = $login;
        $this->_name = $name;
        $this->_phone = $phone;
    }
}