<?php

namespace PersonalArea\Bundle\ClientAreaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BillingServiceDiscount
 */
class BillingServiceDiscount
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
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingDiscount
     */
    private $discount;

    /**
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingService
     */
    private $service;


    /**
     * Set amount
     *
     * @param string $amount
     * @return BillingServiceDiscount
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
     * Set discount
     *
     * @param \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingDiscount $discount
     * @return BillingServiceDiscount
     */
    public function setDiscount(\PersonalArea\Bundle\ClientAreaBundle\Entity\BillingDiscount $discount = null)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingDiscount 
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set service
     *
     * @param \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingService $service
     * @return BillingServiceDiscount
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
}
