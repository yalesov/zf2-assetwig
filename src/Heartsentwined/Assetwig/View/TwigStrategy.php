<?php
namespace Heartsentwined\Assetwig\View;

use Heartsentwined\ArgValidator\ArgValidator;
use Heartsentwined\Assetwig\View\TwigRenderer;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

class TwigStrategy implements ListenerAggregateInterface
{
    protected $renderer;
    protected $listeners = array();

    public function __construct(TwigRenderer $renderer)
    {
        $this->renderer  = $renderer;
    }

    public function setRenderer(TwigRenderer $renderer)
    {
        $this->renderer = $renderer;

        return $this;
    }

    public function attach(EventManagerInterface $events, $priority = null)
    {
        ArgValidator::assert($priority, array('int', 'null'));

        if (null === $priority) {
            $this->listeners[] = $events->attach('renderer', array($this, 'selectRenderer'));
            $this->listeners[] = $events->attach('response', array($this, 'injectResponse'));
        } else {
            $this->listeners[] = $events->attach('renderer', array($this, 'selectRenderer'), $priority);
            $this->listeners[] = $events->attach('response', array($this, 'injectResponse'), $priority);
        }
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function selectRenderer($e = null)
    {
        return $this->renderer;
    }

    public function getRenderer()
    {
        return $this->renderer;
    }

    public function injectResponse($e)
    {
        $response = $e->getResponse();
        $result   = $e->getResult();
        $response->setContent($result);
    }
}
