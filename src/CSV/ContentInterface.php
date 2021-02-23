<?php

namespace App\CSV;

interface ContentInterface
{
    public function getHeader(): array;
    public function getRows(): array;
    public function addRow(array $row): void;
}
