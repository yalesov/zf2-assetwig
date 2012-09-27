<?php
namespace Heartsentwined\Assetwig\Twig;

use Heartsentwined\ArgValidator\ArgValidator;
use Heartsentwined\Assetwig\Twig\HelperFunction;
use Twig_Environment;
use Twig_ExtensionInterface;
use Twig_Function_Function as TwigFunction;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\View\HelperPluginManager;
use Zend\View\Resolver\TemplatePathStack;

class Environment extends Twig_Environment implements ServiceManagerAwareInterface
{
    protected $hpm = null;
    protected $sm;

    protected $renderReady = false;

    protected $templateSuffix = 'twig';
    protected $extensionClasses = array();

    public function setHelperPluginManager(HelperPluginManager $hpm)
    {
        $this->hpm = $hpm;

        return $this;
    }

    public function getHelperPluginManager()
    {
        if (null === $this->hpm) {
            $this->hpm = $this->getServiceManager()->get('view_manager')->getHelperManager();
        }

        return $this->hpm;
    }

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;

        return $this;
    }

    public function getServiceManager()
    {
        return $this->sm;
    }

    public function setTemplateSuffix($templateSuffix)
    {
        ArgValidator::assert($templateSuffix, 'string');
        $this->templateSuffix = $templateSuffix;

        return $this;
    }

    public function getTemplateSuffix()
    {
        return $this->templateSuffix;
    }

    public function setExtensionClasses(array $extensionClasses = array())
    {
        foreach ($extensionClasses as $identifier => $class) {
            $this->addExtensionClass($identifier, $class);
        }

        return $this;
    }

    public function addExtensionClass($identifier, $class)
    {
        ArgValidator::assert($identifier, 'string');
        ArgValidator::assert($class, array(
            '\Twig_ExtensionInterface', 'string'));
        $this->extensionClasses[$identifier] = $class;

        return $this;
    }

    public function getExtensionClasses()
    {
        return $this->extensionClasses;
    }

    /**
     * Get a function by name.
     *
     * Subclasses may override this method and load functions differently;
     * so no list of functions is available.
     *
     * @param string $name function name
     *
     * @return Twig_Function|false A Twig_Function instance or false if the function does not exists
     */
    public function getFunction($name)
    {
        ArgValidator::assert($name, 'string');

        //try to get the function from the environment itself
        $function = parent::getFunction($name);
        if (false !== $function) {
            return $function;
        }

        //if not found, try to get it from  the broker and define it in the environment for later usage
        try {
            $helper = $this->plugin($name,array());
            if (null !== $helper) {
                $function = new HelperFunction($name, array('is_safe' => array('html')));
                $this->addFunction($name, $function);

                return $function;
            }
        } catch (\Exception $exception) {
            // ignore the exception and try to use a defined PHP function
        }

        // return any PHP function or any of the defined valid PHP constructs
        $constructs = array('isset', 'empty');
        if ( function_exists($name) || in_array($name, $constructs) ) {
            $function = new TwigFunction($name);
            $this->addFunction($name, $function);

            return $function;
        }

        // no function found
        return false;
    }

    /**
     * Get plugin instance
     *
     * @param  string     $plugin  Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     * @return mixed
     */
    public function plugin($plugin, array $options = null)
    {
        ArgValidator::assert($plugin, 'string');

        $helper = $this->getHelperPluginManager()->get($plugin, $options);

        return $helper;
    }

    public function render($name, array $vars = array())
    {
        ArgValidator::assert($name, 'string');

        $this->prepareRender();

        return parent::render($name, $vars);
    }

    public function prepareRender()
    {
        if (!$this->renderReady) {
            $this->configResolver();
            $this->configExtension();
        }
        $this->renderReady = true;

        return $this;
    }

    public function configResolver()
    {
        $viewResolver = $this->getServiceManager()->get('view_manager')->getResolver();
        $loader = $this->getLoader();
        foreach ($viewResolver->getIterator() as $resolver) {
            if ($resolver instanceof TemplatePathStack) {
                $resolver = clone $resolver;
                $resolver->setDefaultSuffix($this->getTemplateSuffix());
            }
            $loader->attach($resolver);
        }

        return $this;
    }

    public function configExtension()
    {
        $sm = $this->getServiceManager();
        foreach ($this->getExtensionClasses() as $extension) {
            if (!$extension instanceof Twig_ExtensionInterface) {
                $extension = $sm->get($extension);
            }
            if ($extension instanceof Twig_ExtensionInterface) {
                $this->addExtension($extension);
            }
        }

        return $this;
    }
}
