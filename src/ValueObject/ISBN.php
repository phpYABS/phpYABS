<?php

declare(strict_types=1);

namespace PhpYabs\ValueObject;

use IsoCodes\Isbn as IsbnValidator;

abstract class ISBN implements \Stringable
{
    protected function __construct(protected readonly string $value)
    {
        if (!IsbnValidator::validate($this->value)) {
            throw new \InvalidArgumentException('Invalid ISBN!');
        }
    }

    final public static function fromString(string $isbn): self
    {
        if (preg_match('/-{2,}/', $isbn)) {
            throw new \InvalidArgumentException('Multiple dashes detected');
        }

        $isbn = str_replace('-', '', $isbn);

        if (preg_match('/^[0-9]{9}$/', $isbn)) {
            return ISBN10::fromNineDigits($isbn);
        }

        if (IsbnValidator::validate($isbn, 10)) {
            return new ISBN10($isbn);
        }

        if (IsbnValidator::validate($isbn, 13)) {
            return new ISBN13($isbn);
        }

        throw new \InvalidArgumentException('Invalid ISBN provided');
    }

    public abstract ISBN10 $version10 {
        get;
    }

    public abstract ISBN13 $version13 {
        get;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
