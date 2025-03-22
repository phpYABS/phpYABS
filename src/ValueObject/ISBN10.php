<?php

namespace PhpYabs\ValueObject;

final class ISBN10 extends ISBN
{
    public static function fromNineDigits(string $isbn): self
    {
        if (!preg_match('/^[0-9]{9}$/', $isbn)) {
            throw new \InvalidArgumentException('Exactly nine digit expected');
        }

        return new self($isbn . self::calculateISBN10CheckDigit($isbn));
    }

    private static function calculateISBN10CheckDigit(string $isbn): string
    {
        $sum = 0;
        for ($i = 0; $i < 9; ++$i) {
            $weight = 10 - $i;
            $sum += $weight * (int) $isbn[$i];
        }

        $check = (11 - $sum % 11) % 11;

        return 10 === $check ? 'X' : (string) $check;
    }

    public static function from13(ISBN13 $isbn): ISBN10
    {
        $clean = preg_replace('[^0-9]', '', $isbn);

        return self::fromNineDigits(substr((string) $clean, 3, 9));
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public string $withoutChecksum {
        get {
            return substr($this->value, 0, 9);
        }
    }

    public self $version10 {
        get {
            return $this;
        }
    }
    public ISBN13 $version13 {
        get {
            return ISBN13::from10($this);
        }
    }
}
