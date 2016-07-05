<?php
namespace Yalesov\Assetwig\Exception;

use Yalesov\Assetwig\ExceptionInterface;

class InvalidArgumentException
    extends \InvalidArgumentException
    implements ExceptionInterface
{
}
