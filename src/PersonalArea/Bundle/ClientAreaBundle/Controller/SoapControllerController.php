<?php

namespace PersonalArea\Bundle\ClientAreaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Zend\Soap\AutoDiscover,
    Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex,
    Zend\Soap\Server;

use PersonalArea\Bundle\ClientAreaBundle\Soap\DocumentLiteralWrapper;

class SoapControllerController extends Controller
{
    public function BillingServerAction()
    {
	    $response = new Response();
	    $wsdlUrl  = sprintf("http://%s%s", $this->getRequest()->getHost(), $this->generateUrl('_soap_server_billing'));

	    if ($this->getRequest()->query->has('wsdl'))
	    {
	      $strategy = new ArrayOfTypeComplex();

	      $server   = new AutoDiscover($strategy);

	      $server->setClass('\PersonalArea\Bundle\ClientAreaBundle\Soap\Billing')
	             ->setUri($wsdlUrl)
	             ->setServiceName('Billing');

	      $response->setContent($server->generate()->toXml());
	      $response->headers->set('Content-Type', 'text/xml; charset=UTF8');

	    } else {
	        $server = new Server(
	            null,
	            array(
	                'location'  => "$wsdlUrl",
	                'uri'       => "$wsdlUrl?wsdl",
	                'cache_wsdl' => WSDL_CACHE_NONE
	            )
	        );

	        $server->setObject(new DocumentLiteralWrapper(
	        	$this->get('soap_billing'),
	        	$this->getDoctrine()));

	        $response->setContent($server->handle());
	    }

	    return $response;
    }
}
