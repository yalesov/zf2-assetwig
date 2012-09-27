<?php
namespace Heartsentwined\Assetwig\View;

use Heartsentwined\ArgValidator\ArgValidator;
use Heartsentwined\Assetwig\Assetic\Assetic;
use Heartsentwined\Assetwig\Twig\Environment;
use Heartsentwined\Assetwig\Exception;
use Zend\Filter\FilterChain;
use Zend\View\HelperPluginManager;
use Zend\View\Model\ModelInterface;
use Zend\View\Renderer\RendererInterface;
use Zend\View\Renderer\TreeRendererInterface;
use Zend\View\Resolver\ResolverInterface;

class TwigRenderer implements RendererInterface, TreeRendererInterface
{
    protected $environment;
    protected $assetic;

    protected $filterChain = null;
    protected $templates = array();
    protected $renderTrees = false;

    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;

        return $this;
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    public function setAssetic(Assetic $assetic)
    {
        $this->assetic = $assetic;

        return $this;
    }

    public function getAssetic()
    {
        return $this->assetic;
    }

    public function setResolver(ResolverInterface $resolver)
    {
        $this->getEnvironment()->setLoader($resolver);

        return $this;
    }

    public function getResolver()
    {
        if (!$this->getEnvironment()) return null;
        return $this->getEnvironment()->getLoader();
    }

    public function getEngine()
    {
        return $this;
    }

    public function getHelperPluginManager()
    {
        if (!$this->getEnvironment()) return null;
        return $this->getEnvironment()->getHelperPluginManager();
    }

    public function setHelperPluginManager(HelperPluginManager $hpm)
    {
        $hpm->setView($this);
        if ($this->getEnvironment()) {
            $this->getEnvironment()->setHelperPluginManager($hpm);
        }

        return $this;
    }

    public function setFilterChain(FilterChain $filterChain)
    {
        $this->filterChain = $filterChain;

        return $this;
    }

    public function getFilterChain()
    {
        if (null === $this->filterChain) {
            $this->setFilterChain(new FilterChain());
        }

        return $this->filterChain;
    }

    public function setCanRenderTrees($renderTrees)
    {
        $this->renderTrees = (bool) $renderTrees;

        return $this;
    }

    public function canRenderTrees()
    {
        return $this->renderTrees;
    }

    public function plugin($name, array $options = null)
    {
        if (!$this->getEnvironment()) return null;
        return $this->getEnvironment()->plugin($name, $options);
    }

    public function partial($nameOrModel, $vars = null)
    {
        ArgValidator::assert($vars, array('\Traversable', 'null'));

        return $this->render($nameOrModel, $vars);
    }

    public function render($nameOrModel, $vars = null)
    {
        ArgValidator::assert($nameOrModel,
            array('string', '\Zend\View\Model\ModelInterface'));
        ArgValidator::assert($vars, array('\Traversable', 'null'));

        if ($nameOrModel instanceof ModelInterface) {
            $model       = $nameOrModel;
            $nameOrModel = $model->getTemplate();
            if (empty($nameOrModel)) {
                throw new Exception\DomainException(sprintf(
                    '%s: received View Model argument, but template is empty',
                    __METHOD__
                ));
            }
            $options = $model->getOptions();
            foreach ($options as $setting => $value) {
                $method = 'set' . $setting;
                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
                unset($method, $setting, $value);
            }
            unset($options);

            // Give view model awareness via ViewModel helper
            $helper = $this->plugin('view_model');
            $helper->setCurrent($model);

            $vars = $model->getVariables();
            if ($vars instanceof \ArrayObject) {
                $vars = $vars->getArrayCopy();
            }

            unset($model);
        }

        if ($vars === null) $vars = array();

        if (empty($nameOrModel)) {
            throw new Exception\InvalidArgumentException(
                'Invalid template name provided.'
            );
        }

        $this->getAssetic()->setup($nameOrModel);

        $output = $this->getEnvironment()->render($nameOrModel,$vars);

        return $this->getFilterChain()->filter($output);
    }

    public function __call($method, $argv)
    {
        $helper = $this->plugin($method);
        if (is_callable($helper)) {
            return call_user_func_array($helper, $argv);
        }

        return $helper;
    }

    public function __clone()
    {
        $this->environment = clone $this->environment;
    }
}
