<?php

namespace PersonalArea\Bundle\ClientAreaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BillingServicePrice
 */
class BillingServicePrice
{
    /**
     * @var string
     */
    private $amount;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingPrice
     */
    private $price;

    /**
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingService
     */
    private $service;


    /**
     * Set amount
     *
     * @param string $amount
     * @return BillingServicePrice
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
     * Set price
     *
     * @param \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingPrice $price
     * @return BillingServicePrice
     */
    public function setPrice(\PersonalArea\Bundle\ClientAreaBundle\Entity\BillingPrice $price = null)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingPrice 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set service
     *
     * @param \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingService $service
     * @return BillingServicePrice
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
     * @var integer
     */
    private $count;


    /**
     * Set count
     *
     * @param integer $count
     * @return BillingServicePrice
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer 
     */
    public function getCount()
    {
        return $this->count;
    }
}
