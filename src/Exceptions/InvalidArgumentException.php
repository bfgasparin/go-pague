<?php

namespace GoPague\Exceptions;

use InvalidArgumentException as BaseInvalidArgumentException;

/**
 * InvalidArgumentException for GoPague
 */
class InvalidArgumentException extends BaseInvalidArgumentException implements GoPagueExceptionInterface
{
}
