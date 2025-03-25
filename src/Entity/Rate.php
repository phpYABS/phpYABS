<?php

namespace PhpYabs\Entity;

enum Rate: string
{
    case ZERO = 'zero';
    case ROTMED = 'rotmed';
    case ROTSUP = 'rotsup';
    case BUONO = 'buono';
}
