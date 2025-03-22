<?php

declare(strict_types=1);

use PhpYabs\ValueObject\ISBN;

function fullisbn(string $isbn): ?string
{
    try {
        return (string) ISBN::fromString($isbn)->version10;
    } catch (InvalidArgumentException $e) {
        return null;
    }
}
