<?php

namespace Bowling\Exception;

use InvalidArgumentException;

class MaximumScoreExceededException extends InvalidArgumentException
{
    public static function create(int $currentScore, int $maximumAllowed): MaximumScoreExceededException
    {
        return new static("Maximum score of {$currentScore} was exceeded. Maximum allowed: {$maximumAllowed}");
    }
}
