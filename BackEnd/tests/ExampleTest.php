<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {

        $this->get('http://dev.wilsonliu.cn:8000/test')
             ->seeJson([
                 'state'=>2
             ]);
    }
//    public function testBasicTwoExample()
//    {
//        $this->visit('http://dev.wilsonliu.cn:8000/test')
//            ->see('21');
//    }
}
