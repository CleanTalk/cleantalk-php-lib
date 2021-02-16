<?php

namespace Cleantalk\Common\Variables;

class GetTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $_GET = array( 'variable' => 'value' );
    }

    public function testGet()
    {
        $var = Get::get( 'variable' );
        self::assertEquals($var, $_GET['variable']);
        $wrong_var = Get::get( 'wrong_variable' );
        self::assertEmpty($wrong_var);
    }

    protected function tearDown()
    {
        unset( $_GET['variable'] );
    }
}
