<?php
namespace Assetwig\Exception;

use Assetwig\ExceptionInterface;

class InvalidArgumentException
    extends \InvalidArgumentException
    implements ExceptionInterface
{
}
