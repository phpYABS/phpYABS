<?php

use PhpYabs\ValueObject\ISBN;

function fullisbn(string $isbn): ?string
{
    try {
        return (string) ISBN::fromString($isbn)->version10;
    } catch (InvalidArgumentException $e) {
        return null;
    }
}
