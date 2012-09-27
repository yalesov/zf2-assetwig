<?php
namespace Heartsentwined\Assetwig\Twig;

use Heartsentwined\ArgValidator\ArgValidator;
use Twig_Function;

class HelperFunction extends Twig_Function
{
    protected $name = null;

    public function __construct($name, $options=array())
    {
        ArgValidator::assert($name, 'string');
        parent::__construct($options);
        $this->name = $name;
    }

    /**
     * Compiles a function.
     *
     * @return string The PHP code for the function
     */
    public function compile()
    {
        $name = preg_replace('#[^a-z0-9]+#i', '', $this->name);

        return '$this->getEnvironment()->plugin("' . $name . '")->__invoke';
    }
}
