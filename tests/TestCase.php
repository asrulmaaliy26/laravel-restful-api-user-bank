<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{

    use CreatesApplication;


    protected function setUp(): void{
        parent::setUp();
        DB::delete("delete from addresses");
        DB::delete("delete from contacts");
        DB::delete("delete from balances");
        DB::delete("delete from users");
        DB::statement('ALTER TABLE addresses AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE contacts AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE balances AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
    }
}
