<?php

namespace PersonalArea\Bundle\ClientAreaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use External\ServiceBundle\Config;
use External\ServiceBundle\ApiRequisites;
use External\ServiceBundle\CloudServiceClient;
use External\ServiceBundle\CloudServiceClient1,
    External\ServiceBundle\PkiServiceClient,
    External\ServiceBundle\DostecBillingClient;
use External\ServiceBundle\Authentication;
use PersonalArea\Bundle\ClientAreaBundle\Controller\BillingApiKeysController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContext;
use PersonalArea\Bundle\ClientAreaBundle\Controller\UserController;
use PersonalArea\Bundle\ClientAreaBundle\Entity\BillingUsers;
use PersonalArea\Bundle\ClientAreaBundle\Entity\MiscUserLog;

class authController extends Controller
{
    public function indexAction()
    {
        return $this->redirect('/admin');
    }
    public function logoutAction()
    {
        return $this->redirect('/admin');
    }
}
