<?php

namespace PersonalArea\Bundle\ClientAreaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BillingBill
 */
class BillingBill
{
    /**
     * @var integer
     */
    private $serviceId;

    /**
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @var string
     */
    private $amount;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingClient
     */
    private $client;


    /**
     * Set serviceId
     *
     * @param integer $serviceId
     * @return BillingBill
     */
    public function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;

        return $this;
    }

    /**
     * Get serviceId
     *
     * @return integer 
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     * @return BillingBill
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime 
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return BillingBill
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set client
     *
     * @param \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingClient $client
     * @return BillingBill
     */
    public function setClient(\PersonalArea\Bundle\ClientAreaBundle\Entity\BillingClient $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingClient 
     */
    public function getClient()
    {
        return $this->client;
    }
    /**
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingService
     */
    private $service;


    /**
     * Set service
     *
     * @param \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingService $service
     * @return BillingBill
     */
    public function setService(\PersonalArea\Bundle\ClientAreaBundle\Entity\BillingService $service = null)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingService 
     */
    public function getService()
    {
        return $this->service;
    }
    /**
     * @var boolean
     */
    private $isInvoiceLoaded;


    /**
     * Set isInvoiceLoaded
     *
     * @param boolean $isInvoiceLoaded
     *
     * @return BillingBill
     */
    public function setIsInvoiceLoaded($isInvoiceLoaded)
    {
        $this->isInvoiceLoaded = $isInvoiceLoaded;

        return $this;
    }

    /**
     * Get isInvoiceLoaded
     *
     * @return boolean
     */
    public function getIsInvoiceLoaded()
    {
        return $this->isInvoiceLoaded;
    }
}
