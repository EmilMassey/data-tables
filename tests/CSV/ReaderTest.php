<?php

namespace App\Tests\CSV;

use App\CSV\Reader;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{
    public function test_throw_if_not_readable()
    {
        $this->expectException(\InvalidArgumentException::class);

        $reader = new Reader();
        $reader->read('/');
    }

    public function test_throw_if_file_not_exist()
    {
        $this->expectException(\InvalidArgumentException::class);

        $reader = new Reader();
        $reader->read('dummy');
    }

    public function test_throw_if_file_empty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/File ".*\/empty.csv" is empty$/');

        $reader = new Reader();
        $reader->read(__DIR__ . '/../assets/empty.csv');
    }

    public function test_throw_if_file_not_csv_extension()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "csv" file extension. Got: "php"');

        $reader = new Reader();
        $reader->read(__FILE__);
    }
}
