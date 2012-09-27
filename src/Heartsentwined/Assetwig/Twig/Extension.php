<?php
namespace Heartsentwined\Assetwig\Twig;

use Heartsentwined\Assetwig\Twig\Extension\Render\TokenParser as RenderTokenParser;
use Twig_Extension;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Response;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\View\Model\ModelInterface;
use Zend\View\Model\ViewModel;

class Extension extends Twig_Extension implements ServiceManagerAwareInterface
{
    protected $eventManager = null;
    protected $sm;

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;

        return $this;
    }

    public function getServiceManager()
    {
        return $this->sm;
    }

    /**
     * Returns the name of the extension.
     * @return string The extension name
     */
    public function getName()
    {
        return 'Assetwig';
    }

    /**
     * Return a list of token parsers to register with the envirionment
     * @return array
     */
    public function getTokenParsers()
    {
        return array(
            new RenderTokenParser(),
        );
    }

    /**
     * Render an action from a controller and render it's associated template
     * @param  string $expr
     * @param  array  $attributes
     * @param  array  $options
     * @return string
     */
    public function renderAction($expr, array $attributes, array $options)
    {
        ArgValidator::assert($expr, 'string');

        $serviceManager = Module::getServiceManager();
        $application = $serviceManager->get('Application');
        //parse the name of the controller, action and template directory that should be used
        if (strpos($expr, '/') > 0) {
            list($controllerName, $actionName) = explode('/', $expr);
            $templateDir = $controllerName.'/';
        } else {
            list($moduleName, $controllerName, $actionName) = explode(':', $expr);
            $actionName = lcfirst($actionName);
            $actionName = strtolower(preg_replace('/([A-Z])/', '-$1', $actionName));
            $templateDir = lcfirst($moduleName).'/'.lcfirst($controllerName).'/';
            $controllerName = $moduleName.'\\Controller\\'.$controllerName.'Controller';
        }

        //instantiate the controller based on the given name
        $controller = $serviceManager->get('ControllerLoader')->get($controllerName);
        //clone the MvcEvent and route and update them with the provided parameters
        $event = $application->getMvcEvent();
        $routeMatch = clone $event->getRouteMatch();
        $event = clone $event;
        foreach ($attributes as $key => $value) {
            $routeMatch->setParam($key, $value);
        }
        $event->setRouteMatch($routeMatch);

        //inject the new event into the controller
        if ($controller instanceof InjectApplicationEventInterface) {
            $controller->setEvent($event);
        }

        //test if the action exists in the controller and change it to not-found if missing
        $method = AbstractActionController::getMethodFromAction($actionName);
        if (!method_exists($controller, $method)) {
            $method = 'notFoundAction';
            $actionName = 'not-found';
        }
        //call the method on the controller
        $response  = $controller->$method();
        //if the result is an instance of the Response class return it
        if ($response instanceof Response) {
            return $response->getBody();
        }

        //if the response is an instance of ViewModel then render that one
        if ($response instanceof ModelInterface) {
            $viewModel = $response;
        }elseif ($response === null
                || is_array($response)
                || $response instanceof \ArrayAccess
                || $response instanceof \Traversable) {
            $viewModel = new ViewModel($response);
            $viewModel->setTemplate($templateDir . $actionName);
        } else {
            return '';
        }
        $viewModel->terminate();
        $viewModel->setOption('has_parent', true);

        $view = $serviceManager->get('Zend\View\View');
        $output = $view->render($viewModel);

        return $output;
    }
}
