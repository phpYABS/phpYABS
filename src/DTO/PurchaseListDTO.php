<?php

declare(strict_types=1);

namespace PhpYabs\DTO;

readonly class PurchaseListDTO
{
    public function __construct(
        public int $id,
        public int $count,
    ) {
    }
}
