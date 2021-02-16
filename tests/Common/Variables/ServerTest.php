<?php

namespace Cleantalk\Common\Variables;

class ServerTest extends \PHPUnit\Framework\TestCase
{
    private $original_values = array();

    public function setUp()
    {
        $this->original_values['REQUEST_METHOD']  = $_SERVER['REQUEST_METHOD'];
        $this->original_values['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        $this->original_values['HTTP_REFERER']    = $_SERVER['HTTP_REFERER'];
        $this->original_values['SERVER_NAME']     = $_SERVER['SERVER_NAME'];
        $this->original_values['REQUEST_URI']     = $_SERVER['REQUEST_URI'];

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_USER_AGENT'] = 'user_agent';
        $_SERVER['HTTP_REFERER'] = 'referer';
        $_SERVER['SERVER_NAME'] = 'server_name';
        $_SERVER['REQUEST_URI'] = 'request_uri';
    }

    public function testGet()
    {
        $var = Server::get( 'REQUEST_METHOD' );
        self::assertEquals($var, $_SERVER['REQUEST_METHOD']);

        $var = Server::get( 'HTTP_USER_AGENT' );
        self::assertEquals($var, $_SERVER['HTTP_USER_AGENT']);

        $var = Server::get( 'HTTP_REFERER' );
        self::assertEquals($var, $_SERVER['HTTP_REFERER']);

        $var = Server::get( 'SERVER_NAME' );
        self::assertEquals($var, $_SERVER['SERVER_NAME']);

        $var = Server::get( 'REQUEST_URI' );
        self::assertEquals($var, $_SERVER['REQUEST_URI']);

        $wrong_var = Server::get( 'wrong_variable' );
        self::assertEmpty($wrong_var);
    }

    public function testIn_uri()
    {
        self::assertTrue( Server::in_uri( 'request_uri' ) );
        self::assertFalse( Server::in_uri( 'wrong_request_uri' ) );
    }

    public function testIn_referer()
    {
        self::assertTrue( Server::in_referer( 'referer' ) );
        self::assertFalse( Server::in_referer( 'wrong_referer' ) );
    }

    protected function tearDown()
    {
        $_SERVER['REQUEST_METHOD'] = $this->original_values['REQUEST_METHOD'];
        $_SERVER['HTTP_USER_AGENT'] = $this->original_values['HTTP_USER_AGENT'];
        $_SERVER['HTTP_REFERER'] = $this->original_values['HTTP_REFERER'];
        $_SERVER['SERVER_NAME'] = $this->original_values['SERVER_NAME'];
        $_SERVER['REQUEST_URI'] = $this->original_values['REQUEST_URI'];
    }
}
