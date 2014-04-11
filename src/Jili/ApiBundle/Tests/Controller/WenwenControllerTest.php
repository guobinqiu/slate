<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


class WenwenControllerTest extends WebTestCase
{

    public function test91wenwenRegister()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/91wenwen/register' ,array('secret_token'=>'eyJlbWFpbCI6InpoYW5nbW1Adm95YWdlZ3JvdXAuY29tLmNuIiwic2lnbmF0dXJlIjoiODhlZDRlZjEyNGU5MjZlYTFkZjFlYTZjZGRkZjgzNzc3NzEzMjdhYiJ9'));
        echo $client->getResponse()->getStatusCode(),PHP_EOL;
        echo $client->getResponse()->getContent(),PHP_EOL;
    }
}
