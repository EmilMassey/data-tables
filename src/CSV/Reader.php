<?php

namespace App\CSV;

use Symfony\Component\HttpFoundation\File\File;
use Webmozart\Assert\Assert;

final class Reader implements ReaderInterface
{
    public function read(string $filepath): ContentInterface
    {
        Assert::file($filepath);
        Assert::readable($filepath);

        Assert::same(
            strtolower((new File($filepath))->getExtension()),
            'csv',
            'Expected %2$s file extension. Got: %s'
        );

        $file = fopen($filepath, 'r');

        if (false === $header = fgetcsv($file)) {
            throw new \InvalidArgumentException(\sprintf('File "%s" is empty', $filepath));
        }

        $content = Content::createWithHeader($header);

        while (false !== $row = fgetcsv($file)) {
            $content->addRow($row);
        }

        fclose($file);

        return $content;
    }
}
