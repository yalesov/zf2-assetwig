<?php
namespace Heartsentwined\Assetwig\Assetic;

use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Extension\Twig\AsseticExtension;
use Assetic\Extension\Twig\TwigFormulaLoader;
use Assetic\Extension\Twig\TwigResource;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Assetic\FilterManager;
use Heartsentwined\ArgValidator\ArgValidator;
use Heartsentwined\Assetwig\Twig\Environment;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Assetic implements ServiceManagerAwareInterface
{
    protected $environment;
    protected $assetWriter;

    protected $lazyAm;
    protected $sm;

    protected $am;
    protected $fm;

    protected $root;
    protected $debug = false;
    protected $filters = array();

    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;

        return $this;
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    public function setAssetWriter(AssetWriter $assetWriter)
    {
        $this->assetWriter = $assetWriter;

        return $this;
    }

    public function getAssetWriter()
    {
        return $this->assetWriter;
    }

    public function setLazyAssetManager(LazyAssetManager $lazyAm)
    {
        $this->lazyAm = $lazyAm;

        return $this;
    }

    public function getLazyAssetManager()
    {
        return $this->lazyAm;
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

    public function setAssetManager(AssetManager $am)
    {
        $this->am = $am;

        return $this;
    }

    public function getAssetManager()
    {
        return $this->am;
    }

    public function setFilterManager(FilterManager $fm)
    {
        $this->fm = $fm;

        return $this;
    }

    public function getFilterManager()
    {
        return $this->fm;
    }

    public function setRoot($root)
    {
        ArgValidator::assert($root, 'string');
        $this->root = $root;

        return $this;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function setDebug($debug)
    {
        $this->debug = (bool) $debug;

        return $this;
    }

    public function getDebug()
    {
        return (bool) $this->debug;
    }

    public function setFilters(array $filters = array())
    {
        $this->filters = $filters;

        return $this;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function setup($name)
    {
        ArgValidator::assert($name, 'string');

        $this->prepare();

        $lazyAm = $this->getLazyAssetManager();
        $lazyAm->setLoader('twig', new TwigFormulaLoader($this->getEnvironment()));
        $lazyAm->addResource(
            new TwigResource($this->getEnvironment()->getLoader(), $name),
            'twig');
        $this->getAssetWriter()->writeManagerAssets($lazyAm);

        return $this;
    }

    public function prepare()
    {
        static $ready;

        if (!$ready) {
            $factory = new AssetFactory($this->getRoot(), $this->getDebug());
            $factory->setAssetManager($this->getAssetManager());
            $factory->setFilterManager($this->getFilterManager());

            $extension = new AsseticExtension($factory);
            $this->getEnvironment()->addExtensionClass('Assetic', $extension);

            $lazyAm = new LazyAssetManager($factory);
            $this->setLazyAssetManager($lazyAm);

            $fm = $this->getFilterManager();
            $sm = $this->getServiceManager();
            foreach ($this->getFilters() as $name => $class) {
                $fm->set($name, $sm->get($class));
            }
            $ready = true;
        }

        return $this;
    }
}
