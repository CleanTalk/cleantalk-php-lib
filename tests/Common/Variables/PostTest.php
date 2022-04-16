<?php

namespace Cleantalk\Variables;

class PostTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $_POST = array( 'variable' => 'value' );
    }

    public function testGet()
    {
        $var = Post::get( 'variable' );
        self::assertEquals($var, $_POST['variable']);
        $wrong_var = Post::get( 'wrong_variable' );
        self::assertEmpty($wrong_var);
    }

    protected function tearDown()
    {
        unset( $_POST['variable'] );
    }
}
