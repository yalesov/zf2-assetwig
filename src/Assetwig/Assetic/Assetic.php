<?php
namespace Assetwig\Assetic;

use Assetic\AssetWriter;
use Assetic\Extension\Twig\TwigFormulaLoader;
use Assetic\Extension\Twig\TwigResource;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Assetwig\Twig\Environment;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Assetic implements ServiceManagerAwareInterface
{
    protected $environment;
    protected $lazyAssetManager;
    protected $assetWriter;

    protected $factory;
    protected $sm;

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

    public function setLazyAssetManager(LazyAssetManager $lazyAssetManager)
    {
        $this->lazyAssetManager = $lazyAssetManager;
        return $this;
    }

    public function getLazyAssetManager()
    {
        return $this->lazyAssetManager;
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

    public function setFactory(AssetFactory $factory)
    {
        $this->factory = $factory;
        return $this;
    }

    public function getFactory()
    {
        return $this->factory;
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
        $this->setupFilter();

        $am = $this->getLazyAssetManager();
        $am->setLoader('twig', new TwigFormulaLoader($this->getEnvironment()));
        $am->addResource(
            new TwigResource($this->getEnvironment()->getLoader(), $name),
            'twig');
        $this->getAssetWriter()->writeManagerAssets($am);

        return $this;
    }

    public function setupFilter()
    {
        $fm = $this->getFactory()->getFilterManager();
        $sm = $this->getServiceManager();
        foreach ($this->getFilters() as $name => $class) {
            $fm->set($name, $sm->get($class));
        }

        return $this;
    }
}
