<?php

namespace App\Services\Projects\Exceptions;

use RuntimeException;

/**
 * Domain specific exception used to bubble business rule violations to the controllers.
 */
final class ProjectIdeaException extends RuntimeException
{
}
