<?php

namespace Cleantalk\Common\Variables;

class RequestTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $_REQUEST = array( 'variable' => 'value' );
    }

    public function testGet()
    {
        $var = Request::get( 'variable' );
        self::assertEquals($var, $_REQUEST['variable']);
        $wrong_var = Request::get( 'wrong_variable' );
        self::assertEmpty($wrong_var);
    }

    protected function tearDown()
    {
        unset( $_REQUEST['variable'] );
    }
}
