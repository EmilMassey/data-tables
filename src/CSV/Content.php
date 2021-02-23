<?php

namespace App\CSV;

final class Content implements ContentInterface
{
    /**
     * @var array
     */
    private $header;

    /**
     * @var array
     */
    private $rows;

    public static function createWithHeader(array $header): self
    {
        return new self($header, []);
    }

    public function __construct(array $header, array $rows)
    {
        $this->header = $header;
        $this->rows = $rows;
    }

    public function getHeader(): array
    {
        return $this->header;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function addRow(array $row): void
    {
        $this->rows[] = $row;
    }
}
