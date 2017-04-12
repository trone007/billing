<?php

namespace PersonalArea\Bundle\ClientAreaBundle\Entity;

/**
 * BillingBalanceStorage
 */
class BillingBalanceStorage
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
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingClient
     */
    private $client;


    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return BillingBalanceStorage
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
     *
     * @return BillingBalanceStorage
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
}

