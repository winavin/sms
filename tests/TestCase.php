<?php

namespace Winavin\Sms\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Winavin\Sms\SmsServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            SmsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
