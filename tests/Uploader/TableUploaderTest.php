<?php

namespace App\Tests\Uploader;

use App\Uploader\TableUploader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TableUploaderTest extends TestCase
{
    /**
     * @var string
     */
    private $targetDirectory;

    public function setUp(): void
    {
        $this->targetDirectory = sys_get_temp_dir() . '/uploader';
        @mkdir($this->targetDirectory);
    }

    public function tearDown(): void
    {
        $files = array_diff(scandir($this->targetDirectory), ['.', '..']);
        foreach ($files as $file) {
            @unlink($this->targetDirectory . '/' . $file);
        }
        @rmdir($this->targetDirectory);
    }

    public function test_constructor_throw_if_upload_directory_not_exist()
    {
        $this->expectException(\InvalidArgumentException::class);

        new TableUploader('nonexistent', new AsciiSlugger());
    }

    public function test_constructor_throw_if_upload_directory_not_directory()
    {
        $this->expectException(\InvalidArgumentException::class);

        new TableUploader(__FILE__, new AsciiSlugger());
    }

    public function test_constructor_throw_if_upload_directory_not_writeable()
    {
        $this->expectException(\InvalidArgumentException::class);

        new TableUploader('/', new AsciiSlugger());
    }

    public function test_can_set_filename()
    {
        $uploader = new TableUploader($this->targetDirectory, new AsciiSlugger());
        $uploader->setFilename('file');
        $filepath = $uploader->upload(new File(self::createTempCsvFile()));

        $this->assertRegExp('/file-.+\.csv$/', $filepath);
    }

    public function test_slugified_filename()
    {
        $uploader = new TableUploader($this->targetDirectory, new AsciiSlugger());
        $uploader->setFilename('file nąmę');
        $filepath = $uploader->upload(new File(self::createTempCsvFile()));

        $this->assertRegExp('/file-name-.+\.csv$/', $filepath);
    }

    private static function createTempCsvFile(): string
    {
        $path = sys_get_temp_dir() . '/' . uniqid() . '.csv';
        copy(__DIR__ . '/../assets/sample.csv', $path);

        return $path;
    }
}
