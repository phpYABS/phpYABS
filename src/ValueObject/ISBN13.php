<?php

namespace PhpYabs\ValueObject;

final class ISBN13 extends ISBN
{
    public static function from10(ISBN10 $isbn10): self
    {
        // Convert to ISBN-13
        $isbn13 = '978' . substr($isbn10, 0, 9);

        // Calculate check digit
        $sum = 0;
        for ($i = 0; $i < 12; ++$i) {
            $sum += (int) $isbn13[$i] * (0 === $i % 2 ? 1 : 3);
        }
        $checkDigit = (10 - ($sum % 10)) % 10;

        return new self($isbn13 . $checkDigit);
    }

    public ISBN10 $version10 {
        get {
            return ISBN10::from13($this);
        }
    }
    public ISBN13 $version13 {
        get {
            return $this;
        }
    }
}
