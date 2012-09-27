<?php
namespace Heartsentwined\Assetwig\View;

use Heartsentwined\ArgValidator\ArgValidator;
use Heartsentwined\Assetwig\Exception;
use Zend\View\Resolver\AggregateResolver;
use Twig_LoaderInterface as LoaderInterface;

class TwigResolver extends AggregateResolver implements LoaderInterface
{
    public function getSource($name)
    {
        ArgValidator::assert($name, 'string');
        $path = $this->resolve($name);
        if (!$path) {
            throw new Exception\DomainException(sprintf('Template "%s" not found.', $name));
        }

        return file_get_contents($path);
    }

    public function getCacheKey($name)
    {
        ArgValidator::assert($name, 'string');
        $path = $this->resolve($name);

        return $path;
    }

    public function isFresh($name, $time)
    {
        ArgValidator::assert($name, 'string');
        ArgValidator::assert($time, 'int');

        $path = $this->resolve($name);
        if (!$path) {
            return false;
        }

        return filemtime($path) < $time;
    }
}
