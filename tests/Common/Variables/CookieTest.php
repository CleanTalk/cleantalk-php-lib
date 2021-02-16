<?php

namespace Cleantalk\Common\Variables;

class CookieTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $_COOKIE = array( 'variable' => 'value' );
    }

    public function testGet()
    {
        $var = Cookie::get( 'variable' );
        self::assertEquals($var, $_COOKIE['variable']);
        $wrong_var = Cookie::get( 'wrong_variable' );
        self::assertEmpty($wrong_var);
    }

    protected function tearDown()
    {
        unset( $_COOKIE['variable'] );
    }
}
