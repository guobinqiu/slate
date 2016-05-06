<?php
namespace Wenwen\AppBundle\Services;

class SopHttpfulClient
{

    public function get($url)
    {
        return \Httpful\Request::get($url)->send();
    }
}
