<?php

namespace GoPague\Exceptions;

use InvalidArgumentException;

/**
 * InvalidArgumentException for GoPague
 */
class InvalidArgumentException extends BaseInvalidArgumentException implements GoPagueExceptionInterface
{
}
