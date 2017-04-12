<?php

namespace PersonalArea\Bundle\ClientAreaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    PersonalArea\Bundle\ClientAreaBundle\Entity\BillingClient;

class UserController extends Controller
{
    public function add($inn, $name, $em)
    {
        $em = $em->getManager();
        $client = new BillingClient();
        if($name) {
            $client->setName($name);
        }
        $client->setInn($inn);
        $em->persist($client);
        $em->flush();
    }
    public function update($client, $inn, $name, $em)
    {
        $em = $em->getManager();
        if($name) {
            $client->setName($name);
        }

        $client->setInn($inn);

        $em->persist($client);
        $em->flush();
    }
}
