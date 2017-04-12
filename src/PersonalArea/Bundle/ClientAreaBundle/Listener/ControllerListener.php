<?php
namespace PersonalArea\Bundle\ClientAreaBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ControllerListener
{
	public function onKernelController(FilterControllerEvent $event)
	{
		$controller = $event->getController();

		if(!is_array($controller))
		{
			return;
		}

		$controllerObject = $controller[0];

		if(is_object($controllerObject) && method_exists($controllerObject,"preExecute") )
		{
			$controllerObject->preExecute();
		}

		if(is_object($controllerObject) && method_exists($controllerObject,"postExecute") )
		{
			$controllerObject->postExecute();
		}
	}
}
