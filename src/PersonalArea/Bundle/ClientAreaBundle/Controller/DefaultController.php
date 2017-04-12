<?php

namespace PersonalArea\Bundle\ClientAreaBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use PersonalArea\Bundle\ClientAreaBundle\Entity as Entity;

/**
 * Class DefaultController
 * @package PersonalArea\Bundle\ClientAreaBundle\Controller
 *
 * Actions in this controller make help for users to create copies from tariff plans and discounts
 */
class DefaultController extends CRUDController
{
    public function indexAction($name)
    {
        return $this->render('ClientAreaBundle:Default:index.html.twig', array('name' => $name));
    }

    public function copyPlanAction($id = null)
    {
    	$dbms = $this->getDoctrine()->getConnection();

    	try {

            $dbms->beginTransaction();
	    	$em = $this->getDoctrine()->getManager();
	    	$id = $this->get('request')->get($this->admin->getIdParameter());
	   		$price = $this->admin->getObject($id);

	    	$servicePrices = $this->getDoctrine()->getRepository('ClientAreaBundle:BillingServicePrice')
	    		->findByPrice($price);

	    	$newPrice = new Entity\BillingPrice();
	    	$newPrice->setName($price->getName() . '-copy');
	    	$em->persist($newPrice);

	    	foreach ($servicePrices as $servicePrice) {
	    		$newServicePrice = new Entity\BillingServicePrice();

	    		$newServicePrice->setPrice($newPrice);
	    		$newServicePrice->setService($servicePrice->getService());
	    		$newServicePrice->setCount($servicePrice->getCount());
	    		$newServicePrice->setAmount($servicePrice->getAmount());

	    		$em->persist($newServicePrice);
	    	}

            $em->flush();
            $dbms->commit();
            $dbms->close();
	    } catch (\Exception $ex) {
	    	$dbms->rollback();
            $dbms->close();
	    }

    	return $this->redirect('/admin/price/list');
    }

    public function copyDiscountAction($id = null)
    {
    	$dbms = $this->getDoctrine()->getConnection();
    	try {

            $dbms->beginTransaction();
	    	$em = $this->getDoctrine()->getManager();
	    	$id = $this->get('request')->get($this->admin->getIdParameter());
	   		$discount = $this->admin->getObject($id);

	    	$serviceDiscounts = $this->getDoctrine()->getRepository('ClientAreaBundle:BillingServiceDiscount')
	    		->findByDiscount($discount);

	    	$newDiscount = new Entity\BillingDiscount();
	    	$newDiscount->setName($discount->getName() . '-copy');
	    	$em->persist($newDiscount);

	    	foreach ($serviceDiscounts as $serviceDiscount) {
	    		$newServiceDiscount = new Entity\BillingServiceDiscount;
	    		$newServiceDiscount->setDiscount($newDiscount);
	    		$newServiceDiscount->setService($serviceDiscount->getService());
	    		$newServiceDiscount->setAmount($serviceDiscount->getAmount());

	    		$em->persist($newServiceDiscount);
	    	}

            $em->flush();
	    	$dbms->commit();
            $dbms->close();
	    } catch (\Exception $ex) {
	    	$dbms->rollback();
            $dbms->close();
	    }

    	return $this->redirect('/admin/discount/list');
    }
}
