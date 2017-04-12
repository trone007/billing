<?php
    namespace PersonalArea\Bundle\ClientAreaBundle\Soap;

    use PersonalArea\Bundle\ClientAreaBundle\Entity as Entity;
    use Doctrine\ORM\EntityManager;
    use External\ServiceBundle\ApiRequisites;
    use External\ServiceBundle\PkiServiceClient;
    use External\ServiceBundle\Config;
    use PersonalArea\Bundle\ClientAreaBundle\Controller\UserController;

class Billing {

    const RESERVATION_MONTHLY_FEE = 1,
          SOAP_FAULT_EXIT_CODE = '007',
          LOCAL_TOKEN = 'YYYYYYYYYYYYYYYYYYYYYYYYYYYYY',
          STANDART_PRICE_CODE = 1,
          ROLES_CONSULTING_AGENT = 5;


    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /*
     * Method converting SymfonyStdClass to xml. Usefull for c# or 1C client
     */
    private function toXml($data, $rootNodeName = 'data', $xml=null)
	  {
		  // turn off compatibility mode as simple xml throws a wobbly if you don't.
		  if (ini_get('zend.ze1_compatibility_mode') == 1)
		  {
			  ini_set ('zend.ze1_compatibility_mode', 0);
		  }

		  if ($xml == null)
		  {
			  $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
		  }

		  // loop through the data passed in.
		  foreach($data as $key => $value)
		  {
			  if (is_numeric($key))
			  {
				  $key = "record_". (string) $key;
			  }

			  // replace anything not alpha numeric
			  $key = preg_replace('/[^a-z]/i', '', $key);

			  // if there is another array found recrusively call this function
			  if (is_array($value))
			  {
				  $node = $xml->addChild($key);
				  $this->toXml($value, $rootNodeName, $node);
			  }
			  else
			  {
				  $xml->addChild($key,$value);
			  }

		  }
		  return $xml->asXML();
    }

    private function getSubscriberByToken($token)
    {
        $query = "
        SELECT
         s.id as subscriber
        FROM billing.subscriber AS s
        WHERE
            ENCODE(token, 'hex') = :token";

        $params = array('token' => $token);

        $em = $this->doctrine->getManager();
        $stmt = $em->getConnection()->prepare($query);

        $stmt->execute($params);
        $subscriber = $stmt->fetchAll();

        return $this->doctrine->getRepository('ClientAreaBundle:BillingSubscriber')->findOneById($subscriber[0]);
    }

    private function getRequisitesByInn($inn)
    {
        $client = (new ApiRequisites())->getClient();

        return $client->getByInn(Config::USER_TOKEN, $inn, null);
    }

    private function getClientByInn($inn, $name = '')
    {
        $name = !$name ? '' : $name;
        try {
            $client = $this->doctrine->getRepository('ClientAreaBundle:BillingClient')
                ->findOneByInn($inn);


            $cln = new UserController();

            $em = $this->doctrine->getManager();

            if (!$client) {
                $cln->add($inn, $name, $this->doctrine);

                $client = $this->doctrine->getRepository('ClientAreaBundle:BillingClient')
                    ->findOneByInn($inn);

                $price = $this->doctrine->getRepository('ClientAreaBundle:BillingPrice')
                    ->findOneById(1);

                $clientPrice = new Entity\BillingClientPrice();
                $clientPrice->setClient($client);
                $clientPrice->setPrice($price);
                $clientPrice->setBeginDate(new \DateTime());

                $em->persist($clientPrice);
                $em->flush();
            } else {
                $cln->update($client, $inn, $name,  $this->doctrine);

                $client = $this->doctrine->getRepository('ClientAreaBundle:BillingClient')
                    ->findOneByInn($inn);
            }
        } catch (\Exception $ex){
            throw $ex;
        }

        return $client;
    }

    private function getBillsCountByServiceAndClientAndMonthAndYear($service, $client, $month, $year)
    {
        $qb = $this->doctrine
            ->getManager()
            ->createQueryBuilder();

        $from = new \DateTime(date('Y-'. $month .'-01'));
        $to   = new \DateTime(date('Y-'. $month .'-t'));

        $bills = $qb->select('bb')
            ->from('PersonalArea\Bundle\ClientAreaBundle\Entity\BillingBill', 'bb')
            ->where('bb.client = :client')
            ->andWhere('bb.service = :service')
            ->andWhere($qb->expr()->between('bb.dateTime', ':date_from', ':date_to'))
            ->setParameter('client', $client)
            ->setParameter('service', $service)
            ->setParameter('date_from', $from, \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('date_to', $to, \Doctrine\DBAL\Types\Type::DATETIME)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return count($bills);

    }

    private function getBillsByUser($user)
    {
        $qb = $this->doctrine
            ->getManager()
            ->createQueryBuilder();

        $from = new \DateTime(date('Y-03' . '-01'));
        $to   = new \DateTime(date('Y-03-t'));

        $type = $this->doctrine
            ->getRepository('ClientAreaBundle:BillingServiceType')
            ->findOneById(1);

        $bills = $qb->select('bpt.id')
            ->from('PersonalArea\Bundle\ClientAreaBundle\Entity\BillingBills', 'br')
            ->leftJoin(
                '\PersonalArea\Bundle\ClientAreaBundle\Entity\BillingServiceType',
                'bpt',
                 \Doctrine\ORM\Query\Expr\Join::WITH,
                 'br.type = bpt'
                )
            ->where('br.user = ?1')
            ->andWhere($qb->expr()->between('br.dateTime', ':date_from', ':date_to'))
            ->groupBy('bpt.id')
            ->setParameter(1, $user)
            ->setParameter('date_from', $from, \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('date_to', $to, \Doctrine\DBAL\Types\Type::DATETIME)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $result = [];

        if($bills){
            foreach($bills as $bill){
                $result[] = $bill['id'];
            }
        }

        return $result;
    }

    /**
      * @param  string $token
      * @param  string $inn
      * @param  string $name
      * @param  float  $sum
      * @return int sum
    */
    public function addAccrual($token, $inn, $name, $sum)
    {
        $dbms = $this->doctrine->getConnection();
        $em = $this->doctrine->getManager();
        $dbms->beginTransaction();

        try {
            $subscriber = $this->getSubscriberByToken($token);

            $client = $this->getClientByInn($inn, $name);

            $accrual = new Entity\BillingAccrual();
            $accrual->setClient($client);
            $accrual->setSubscriber($subscriber);
            $accrual->setDateTime(new \DateTime());
            $accrual->setAmount($sum);


            $this->doctrine->getManager()->persist($accrual);
            $this->doctrine->getManager()->flush();

            $dbms->commit();
            $dbms->close();
        } catch(\Exception $ex) {
            $dbms->rollback();
            $dbms->close();
            throw $ex;
        }

        return $accrual->getId();
    }

    /**
      * @param string $token
      * @param string $periodBegin
      * @param string $periodEnd
      * @return string $result
    */
    public function getBillsByPeriod($token, $periodBegin, $periodEnd)
    {
        try {
            $qb = $this->doctrine
                ->getManager()
                ->createQueryBuilder();

            $from = new \DateTime($periodBegin);
            $to   = new \DateTime($periodEnd);

            $bills = $qb
                ->select('bb.id, bc.inn, bb.dateTime, bs.code, bb.amount')
                ->from('PersonalArea\Bundle\ClientAreaBundle\Entity\BillingBill', 'bb')
                ->leftJoin(
                    '\PersonalArea\Bundle\ClientAreaBundle\Entity\BillingService',
                    'bs',
                     \Doctrine\ORM\Query\Expr\Join::WITH,
                     'bs = bb.service'
                    )
                ->leftJoin(
                    '\PersonalArea\Bundle\ClientAreaBundle\Entity\BillingClient',
                    'bc',
                     \Doctrine\ORM\Query\Expr\Join::WITH,
                     'bc = bb.client'
                    )
                ->where($qb->expr()->between('bb.dateTime', ':date_from', ':date_to'))
                ->setParameter('date_from', $from, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('date_to', $to, \Doctrine\DBAL\Types\Type::DATETIME)
                ->getQuery()
                ->getResult();

            foreach ($bills as $bill) {
                $bill['dateTime'] = $bill['dateTime']->format('Y-m-d H:i:s');
                $array[] = $bill;
            }

             $xml = $this->toXml($array, 'bills');
        } catch (\Exception $ex){
            throw $ex;
        }

        return $xml;
    }

    /**
      * @param string $token
      * @param string $inn
      * @param string $periodBegin
      * @param string $periodEnd
      * @return string $result
    */
    public function getBillsByInnAndPeriod($token, $inn, $periodBegin, $periodEnd)
    {
        try {
            $qb = $this->doctrine
                ->getManager()
                ->createQueryBuilder();

            $from = new \DateTime($periodBegin);
            $to   = new \DateTime($periodEnd);

            $client = $this->doctrine
                ->getRepository('ClientAreaBundle:BillingClient')
                ->findOneByInn($inn);
            if(!$client){
                throw new \SoapFault(self::SOAP_FAULT_EXIT_CODE, 'Пользователь не найден');
            }

            $bills = $qb
                ->select('bb.dateTime, bs.name, bb.amount')
                ->from('PersonalArea\Bundle\ClientAreaBundle\Entity\BillingBill', 'bb')
                ->leftJoin(
                    '\PersonalArea\Bundle\ClientAreaBundle\Entity\BillingService',
                    'bs',
                     \Doctrine\ORM\Query\Expr\Join::WITH,
                     'bs = bb.service'
                    )
                ->where('bb.client = :client')
                ->andWhere($qb->expr()->between('bb.dateTime', ':date_from', ':date_to'))
                ->setParameter('client', $client)
                ->setParameter('date_from', $from, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('date_to', $to, \Doctrine\DBAL\Types\Type::DATETIME)
                ->getQuery()
                ->getResult();

        } catch (\Exception $ex){
            throw $ex;
        }

        return $bills;
    }

    /**
      * @param string $token
      * @param string $periodBegin
      * @param string $periodEnd
      * @return string $result
    */
    public function getAccrualsByPeriod($token, $periodBegin, $periodEnd)
    {
        try {
            $qb = $this->doctrine
                ->getManager()
                ->createQueryBuilder();

            $from = new \DateTime($periodBegin);
            $to   = new \DateTime($periodEnd);

            $accruals = $qb
                ->select('ba.id, bc.inn, ba.dateTime, bs.name, ba.amount')
                ->from('PersonalArea\Bundle\ClientAreaBundle\Entity\BillingAccrual', 'ba')
                ->leftJoin(
                    '\PersonalArea\Bundle\ClientAreaBundle\Entity\BillingSubscriber',
                    'bs',
                     \Doctrine\ORM\Query\Expr\Join::WITH,
                     'bs = ba.subscriber'
                    )
                ->leftJoin(
                    '\PersonalArea\Bundle\ClientAreaBundle\Entity\BillingClient',
                    'bc',
                     \Doctrine\ORM\Query\Expr\Join::WITH,
                     'bc = ba.client'
                    )
                ->where($qb->expr()->between('ba.dateTime', ':date_from', ':date_to'))
                ->setParameter('date_from', $from, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('date_to', $to, \Doctrine\DBAL\Types\Type::DATETIME)
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
             $array = [];
             foreach ($accruals as $accrual) {
	      $accrual['dateTime'] = $accrual['dateTime']->format('Y-m-d H:i:s');
	      $array[] = $accrual;
             }

             $xml = $this->toXml($array, 'accruals');

        } catch (\Exception $ex){
            throw $ex;
        }

        return $xml;
    }

    /**
      * @param string $token
      * @param string $inn
      * @param string $periodBegin
      * @param string $periodEnd
      * @return string $result
    */
    public function getAccrualsByInnAndPeriod($token, $inn, $periodBegin, $periodEnd)
    {
        try {
            $qb = $this->doctrine
                ->getManager()
                ->createQueryBuilder();

            $from = new \DateTime($periodBegin);
            $to   = new \DateTime($periodEnd);

            $client = $this->doctrine
                ->getRepository('ClientAreaBundle:BillingClient')
                ->findOneByInn($inn);
            if(!$client){
                throw new \SoapFault(self::SOAP_FAULT_EXIT_CODE, 'Пользователь не найден');
            }

            $accruals = $qb
                ->select('ba.dateTime, bs.name, ba.amount')
                ->from('PersonalArea\Bundle\ClientAreaBundle\Entity\BillingAccrual', 'ba')
                ->leftJoin(
                    '\PersonalArea\Bundle\ClientAreaBundle\Entity\BillingSubscriber',
                    'bs',
                     \Doctrine\ORM\Query\Expr\Join::WITH,
                     'bs = ba.subscriber'
                    )
                ->where('ba.client = :client')
                ->andWhere($qb->expr()->between('ba.dateTime', ':date_from', ':date_to'))
                ->setParameter('client', $client)
                ->setParameter('date_from', $from, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('date_to', $to, \Doctrine\DBAL\Types\Type::DATETIME)
                ->getQuery()
                ->getResult();

        } catch (\Exception $ex){
            throw $ex;
        }

        return $accruals;
    }

    /**
      * получаю ИНН, возвращаю баланс на текущий момент, без резервации
      * @param string $token
      * @param string $inn
      * @return string sum
    */
    public function getBalanceByInn($token, $inn)
    {
        try {
            $client = $this->doctrine->getRepository('ClientAreaBundle:BillingClient')
                            ->findOneByInn($inn);

            $accruals = $this->doctrine->getManager()
                ->createQueryBuilder()
                ->select('SUM(ba.amount) total')
                ->from('ClientAreaBundle:BillingAccrual', 'ba')
                ->where('ba.client = :client')
                ->setParameter('client', $client)
                ->getQuery()
                ->getResult()[0];

            $bills = $this->doctrine->getManager()
                ->createQueryBuilder()
                ->select('SUM(bl.amount) total')
                ->from('ClientAreaBundle:BillingBill', 'bl')
                ->where('bl.client = :client')
                ->setParameter('client', $client)
                ->getQuery()
                ->getResult()[0];

            $balance = $accruals['total'] - $bills['total'];

            if(!$client){
                throw new \SoapFault(static::SOAP_FAULT_EXIT_CODE, 'Для входа в систему пополните баланс');
            }
        } catch (\Exception $ex){
            throw $ex;
        }
        return $balance;
    }

    /**
      * @param string $token
      * @param string $inn
      * @return int $count
    */
    public function checkClientActivity($token, $inn)
    {
        try {
            $month = date('m');
            $from = new \DateTime(date('Y-'. $month .'-01'));
            $to   = new \DateTime(date('Y-'. $month .'-t'));

            $qb = $this->doctrine
                ->getManager()
                ->createQueryBuilder();

            $client = $this->doctrine->getRepository('ClientAreaBundle:BillingClient')
                ->findOneByInn($inn);

            $bills = $this->doctrine
                    ->getRepository('ClientAreaBundle:BillingBill')
                    ->createQueryBuilder('bb')
                    ->select('bb')
                    ->where('bb.client = :client')
                    ->andWhere($qb->expr()->between('bb.dateTime', ':date_from', ':date_to'))
                    ->setParameter('date_from', $from, \Doctrine\DBAL\Types\Type::DATETIME)
                    ->setParameter('date_to', $to, \Doctrine\DBAL\Types\Type::DATETIME)
                    ->setParameter('client', $client)
                    ->getQuery()
                    ->getResult();

            return $bills ? true : false;
        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
      * @param string $token
      * @param string $service
      * @param string $inn
      * @return string $count
    */
    public function getServicePaymentStatus($token, $service, $inn)
    {
        try {
            $month = date('m');
            $from = new \DateTime(date('Y-'. $month .'-01'));
            $to   = new \DateTime(date('Y-'. $month .'-t 23:59:59'));

            $qb = $this->doctrine
                ->getManager()
                ->createQueryBuilder();

            $client = $this->doctrine->getRepository('ClientAreaBundle:BillingClient')
                ->findOneByInn($inn);

            $service = $this->doctrine->getRepository('ClientAreaBundle:BillingService')
                ->findOneByCode($service);

            $bill = $this->doctrine
                    ->getRepository('ClientAreaBundle:BillingBill')
                    ->createQueryBuilder('bb')
                    ->select('bb')
                    ->where('bb.client = :client')
                    ->andWhere('bb.service = :service')
                    ->andWhere($qb->expr()->between('bb.dateTime', ':date_from', ':date_to'))
                    ->setParameter('date_from', $from, \Doctrine\DBAL\Types\Type::DATETIME)
                    ->setParameter('date_to', $to, \Doctrine\DBAL\Types\Type::DATETIME)
                    ->setParameter('client', $client)
                    ->setParameter('service', $service)
                    ->getQuery()
                    ->getResult();
            return $bill ? true : false;
        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
      * @param  string $token
      * @param  string $uid
      * @param  string $serviceCode
      * @return string $result
    */
    public function servicePay($token, $inn, $serviceCode)
    {
        $dbms = $this->doctrine->getConnection();
        $em = $this->doctrine->getManager();

        try {
            $dbms->beginTransaction();

            $client = $this->doctrine->getRepository('ClientAreaBundle:BillingClient')
                ->findOneByInn($inn);

            $service = $this->doctrine->getRepository('ClientAreaBundle:BillingService')
                ->findOneByCode($serviceCode);

            $count = $this->getBillsCountByServiceAndClientAndMonthAndYear($service, $client, (int)date('m'), date('Y'));

            $discount = $this->getDiscountByClientAndService($client, $service);

            $price = $this->getAmountByServiceCodeAndInnAndCount($token, $serviceCode, $inn, $count);

            $price -= $price * $discount;

            $balance = $this->getBalanceByInn($token, $inn);

            if($balance < $price) {
                throw new \SoapFault(static::SOAP_FAULT_EXIT_CODE,
                    'У Вас нехвататет '. ($price - $balance) .' для оплаты счета');
            }

            $bill = new Entity\BillingBill();

            $bill->setClient($client);
            $bill->setService($service);
            $bill->setDateTime(new \DateTime());
            $bill->setAmount($price);

            $em->persist($bill);
            $em->flush();

            $dbms->commit();
            $dbms->close();
        } catch(\Exception $ex) {
            $dbms->rollback();
            $dbms->close();
            throw $ex;
        }

        return true;
    }

    /**
      * @param string $token
      * @return string $prices
    */
    public function getPrices($token)
    {
        try {
            $prices = $this->doctrine->getRepository('ClientAreaBundle:BillingPrice')->findAll();

            $dom = new \DOMDocument('1.0', 'utf-8');

            $priceRoot = $dom->createElement('PRICE');

            $i = 0;

            foreach($prices as $price) {
                $priceList = $dom->createElement('PRICELIST');
                $priceCode = $dom->createElement('CODE', $price->getId());

                $priceName = $dom->createElement('NAME', $price->getName());

                $priceList->appendChild($priceCode);
                $priceList->appendChild($priceName);

                $priceRoot->appendChild($priceList);
            }

            $dom->appendChild($priceRoot);

        } catch (\Exception $ex){
            throw $ex;
        }
        return $dom->saveXML();
    }


    /**
      * @param string $token
      * @param string $service
      * @param string $inn
      * @return float $prices
    */
    public function getAmountByServiceAndInn($token, $service,  $inn)
    {
        try {
            $service = $this->doctrine->getRepository('ClientAreaBundle:BillingService')->findOneByCode($service);

            $client = $this->doctrine->getRepository('ClientAreaBundle:BillingClient')->findOneByInn($inn);

            $clientPrice = $this->doctrine
                    ->getRepository('ClientAreaBundle:BillingClientPrice')
                    ->createQueryBuilder('bcp')
                    ->select('bcp')
                    ->where('bcp.client = :client')
                    ->andWhere(":date > bcp.beginDate AND :date < COALESCE(bcp.endDate, :date1 )")
                    ->setParameter('date', (new \DateTime()), \Doctrine\DBAL\Types\Type::DATETIME)
                    ->setParameter('date1', (new \DateTime())->add(new \DateInterval('P1D')), \Doctrine\DBAL\Types\Type::DATETIME)
                    ->setParameter('client', $client)
                    ->getQuery()
                    ->getResult()[0];

            if(!$clientPrice) {
	            throw new \SoapFault(self::SOAP_FAULT_EXIT_CODE, "Пополните баланс для входа в систему");
            }

            $discount = $this->getDiscountByClientAndService($client, $service);

            $servicePrices = $this->doctrine->getRepository('ClientAreaBundle:BillingServicePrice')->findOneBy([
                'price' => $clientPrice->getPrice(),
                'service' => $service,
                'count' => 1
                ]);

            if(!$servicePrices) {
               throw new \SoapFault(self::SOAP_FAULT_EXIT_CODE, 'Ценовой план для ' . $service . ' не найден');
            }

            $amount = $servicePrices->getAmount();
            $amount -= $amount * $discount;
        } catch (\Exception $ex){
            throw $ex;
        }
        return $amount;
    }
    /**
      * @param string $token
      * @return string $result
    */
    public function updateBalanceStorage($token)
    {
      try{
	  $clients = $this->doctrine->getRepository('ClientAreaBundle:BillingClient')->findAll();
	  $em = $this->doctrine->getManager();

	  foreach($clients as $client) {
	      $balanceStorage = $this->doctrine->getRepository('ClientAreaBundle:BillingBalanceStorage')->findOneByClient($client);

	      if(!$balanceStorage) {
	          $balanceStorage = new Entity\BillingBalanceStorage();
	      }

	      $balanceStorage->setClient($client);
	      $balanceStorage->setAmount($this->getBalanceByInn($token, $client->getInn()));

	      $em->persist($balanceStorage);
	      $em->flush();
	  }
        } catch (\Exception $ex) {
	        return $ex->getMessage();
        }

        return true;
    }

    /**
      * @param string $token
      * @param string $service
      * @param string $inn
      * @return string $prices
    */
    public function getAmountWithDiscountByServiceAndPriceAndInn($token, $service, $inn)
    {
        try {

            $client = $this->doctrine->getRepository('ClientAreaBundle:BillingClient')
                ->findOneByInn($inn);
            $serviceCode = $this->doctrine->getRepository('ClientAreaBundle:BillingService')
                ->findOneByCode($service);

            $count = $this->getBillsCountByServiceAndClientAndMonthAndYear($serviceCode, $client, (int)date('m'), date('Y'));

            $discount = $this->getDiscountByClientAndService($client, $serviceCode);

            $price = $this->getAmountByServiceCodeAndInnAndCount($token, $service, $inn, $count);

            $price -= $price * $discount;
        } catch (\Exception $ex){
            throw $ex;
        }

        return $price;
    }

    /**
      * @param string $token
      * @param int $price
      * @return string $prices
    */
    public function getServicesAmountByPrice($token, $price) {
        try {
            $price = $this->doctrine->getRepository('ClientAreaBundle:BillingPrice')->findOneById($price);
            $servicePrices = $this->doctrine->getRepository('ClientAreaBundle:BillingServicePrice')->findBy([
                'price' => $price,
                'count' => 1
                ]);

            $dom = new \DOMDocument('1.0', 'utf-8');

            $root = $dom->createElement('SERVICEPRICE');

            foreach($servicePrices as $servicePrice) {
                $servicePriceList = $dom->createElement('SERVICEPRICELIST');
                $servicePriceCode = $dom->createElement('CODE', $servicePrice->getService()->getCode());

                $servicePriceCount = $dom->createElement('COUNT', $servicePrice->getCount());
                $servicePriceAmount = $dom->createElement('AMOUNT', $servicePrice->getAmount());

                $servicePriceList->appendChild($servicePriceCode);
                $servicePriceList->appendChild($servicePriceCount);
                $servicePriceList->appendChild($servicePriceAmount);

                $root->appendChild($servicePriceList);
            }

            $dom->appendChild($root);

        } catch (\Exception $ex){
            throw $ex;
        }
        return $dom->saveXML();
    }

    /**
      * @param string $token
      * @param string $code
      * @return string $prices
    */
    public function getServiceNameByCode($token, $code)
    {
        try {

            $service = $this->doctrine->getRepository('ClientAreaBundle:BillingService')
                ->findOneByCode($code);
            $array = [];
            $array['id'] = $service->getId();
            $array['name'] = $service->getName();
            $array['subscriber'] = $service->getSubscriber()->getName();
            $array['code'] = $service->getCode();
        } catch (\Exception $ex){
            throw $ex;
        }
        return  $this->toXml($array, 'service');
    }

    /**
      * @param string $token
      * @param string $inn
      * @return string $prices
    */
    public function getServicesAmountByInn($token, $inn)
    {
        try {

            $client = $this->doctrine->getRepository('ClientAreaBundle:BillingClient')->findOneByInn($inn);

            $clientPrice = $this->doctrine
                    ->getRepository('ClientAreaBundle:BillingClientPrice')
                    ->createQueryBuilder('bcp')
                    ->select('bcp')
                    ->where('bcp.client = :client')
                    ->andWhere(":date > bcp.beginDate AND :date < COALESCE(bcp.endDate, :date1 )")
                    ->setParameter('date', new \DateTime(), \Doctrine\DBAL\Types\Type::DATETIME)
                    ->setParameter('date1', (new \DateTime())->add(new \DateInterval('P1D')), \Doctrine\DBAL\Types\Type::DATETIME)
                    ->setParameter('client', $client)
                    ->getQuery()
                    ->getResult()[0];

            $servicePrices = $this->doctrine->getRepository('ClientAreaBundle:BillingServicePrice')->findBy([
                'price' => $clientPrice->getPrice(),
                'count' => 1
                ]);


            $dom = new \DOMDocument('1.0', 'utf-8');

            $root = $dom->createElement('SERVICEPRICE');

            foreach($servicePrices as $servicePrice) {
                $servicePriceList = $dom->createElement('SERVICEPRICELIST');
                $servicePriceCode = $dom->createElement('CODE', $servicePrice->getService()->getCode());

                $servicePriceCount = $dom->createElement('COUNT', $servicePrice->getCount());
                $servicePriceAmount = $dom->createElement('AMOUNT', $servicePrice->getAmount());

                $servicePriceList->appendChild($servicePriceCode);
                $servicePriceList->appendChild($servicePriceCount);
                $servicePriceList->appendChild($servicePriceAmount);

                $root->appendChild($servicePriceList);
            }

            $dom->appendChild($root);
        } catch (\Exception $ex){
            throw $ex;
        }
        return $dom->saveXML();
    }

    /**
      * @param string $token
      * @param string $serviceCode
      * @param string $inn
      * @param int $count
      * @return string $prices
    */
    public function getAmountByServiceCodeAndInnAndCount($token, $serviceCode, $inn, $count) {
        try {
            $client = $this->getClientByInn($inn);

            $service = $this->doctrine->getRepository('ClientAreaBundle:BillingService')
                ->findOneByCode($serviceCode);

            $clientPrice = $this->doctrine
                    ->getRepository('ClientAreaBundle:BillingClientPrice')
                    ->createQueryBuilder('bcp')
                    ->select('bcp')
                    ->where('bcp.client = :client')
                    ->andWhere(":date > bcp.beginDate AND :date < COALESCE(bcp.endDate, :date1 )")
                    ->setParameter('date', new \DateTime(), \Doctrine\DBAL\Types\Type::DATETIME)
                    ->setParameter('date1', (new \DateTime())->add(new \DateInterval('P1D')), \Doctrine\DBAL\Types\Type::DATETIME)
                    ->setParameter('client', $client)
                    ->getQuery()
                    ->getResult()[0];

            $servicePrice = $this->doctrine
                ->getRepository('ClientAreaBundle:BillingServicePrice')
                ->createQueryBuilder('bsp')
                ->select('bsp')
                ->where('bsp.price = :price')
                ->andWhere('bsp.service = :service')
                ->andWhere('bsp.count <= :count')
                ->orderBy('bsp.count', 'DESC')
                ->setParameter('price', $clientPrice->getPrice())
                ->setParameter('service', $service)
                ->setParameter('count', ($count + 1))
                ->setMaxResults(1)
                ->getQuery()
                ->getResult()[0];

            if(!$servicePrice){
                throw new \SoapFault(self::SOAP_FAULT_EXIT_CODE, 'Цена на услугу не установлена, невозможно воспользоваться.');
            }
        } catch (\Exception $ex){
            throw $ex;
        }

        return $servicePrice->getAmount();
    }

    private function getDiscountByClientAndService($client, $service) {
	$discount = '';

        $clientDiscount = $this->doctrine
                    ->getRepository('ClientAreaBundle:BillingClientDiscount')
                    ->createQueryBuilder('bcd')
                    ->select('bcd')
                    ->where('bcd.client = :client')
                    ->andWhere(":date > bcd.beginDate AND :date < COALESCE(bcd.endDate, :date1 )")
                    ->setParameter('date', new \DateTime(), \Doctrine\DBAL\Types\Type::DATETIME)
                    ->setParameter('date1', (new \DateTime())->add(new \DateInterval('P1D')), \Doctrine\DBAL\Types\Type::DATETIME)
                    ->setParameter('client', $client)
                    ->getQuery()
                    ->getResult();

        if ($clientDiscount) {
            $discount = $this->doctrine->getRepository('ClientAreaBundle:BillingServiceDiscount')
            ->findOneBy([
                'service' => $service,
                'discount' => $clientDiscount[0] ? $clientDiscount[0]->getDiscount() : null
                ]);
        }

        return !$discount ? 0 : $discount->getAmount() / 100;
    }
}