<?php

namespace Cleantalk\Variables;

class ServerTest extends \PHPUnit\Framework\TestCase
{
    private $original_values = array();

    public function setUp()
    {
        if( isset($_SERVER['REQUEST_METHOD']) )
            $this->original_values['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
        if( isset($_SERVER['HTTP_USER_AGENT']) )
            $this->original_values['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        if( isset($_SERVER['HTTP_REFERER']) )
            $this->original_values['HTTP_REFERER']    = $_SERVER['HTTP_REFERER'];
        if( isset($_SERVER['SERVER_NAME']) )
            $this->original_values['SERVER_NAME']     = $_SERVER['SERVER_NAME'];
        if( isset($_SERVER['REQUEST_URI']) )
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

    public function testInUri()
    {
        self::assertTrue( Server::inUri( 'request_uri' ) );
        self::assertFalse( Server::inUri( 'wrong_request_uri' ) );
    }

    public function testInReferer()
    {
        self::assertTrue( Server::inReferer( 'referer' ) );
        self::assertFalse( Server::inReferer( 'wrong_referer' ) );
    }

    protected function tearDown()
    {
        $original_values = $this->original_values;
        if( isset( $original_values['REQUEST_METHOD'] ) ) {
            $_SERVER['REQUEST_METHOD'] = $original_values['REQUEST_METHOD'];
        } else {
            unset( $_SERVER['REQUEST_METHOD'] );
        }
        if( isset( $original_values['HTTP_USER_AGENT'] ) ) {
            $_SERVER['HTTP_USER_AGENT'] = $original_values['HTTP_USER_AGENT'];
        } else {
            unset( $_SERVER['HTTP_USER_AGENT'] );
        }
        if( isset( $original_values['HTTP_REFERER'] ) ) {
            $_SERVER['HTTP_REFERER'] = $original_values['HTTP_REFERER'];
        } else {
            unset( $_SERVER['HTTP_REFERER'] );
        }
        if( isset( $original_values['SERVER_NAME'] ) ) {
            $_SERVER['SERVER_NAME'] = $original_values['SERVER_NAME'];
        } else {
            unset( $_SERVER['SERVER_NAME'] );
        }
        if( isset( $original_values['REQUEST_URI'] ) ) {
            $_SERVER['REQUEST_URI'] = $original_values['REQUEST_URI'];
        } else {
            unset( $_SERVER['REQUEST_URI'] );
        }
    }
}
