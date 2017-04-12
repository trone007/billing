<?php

namespace PersonalArea\Bundle\ClientAreaBundle\Entity;

/**
 * BillingAccrual
 */
class BillingAccrual
{
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
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingSubscriber
     */
    private $subscriber;

    /**
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingClient
     */
    private $client;


    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     *
     * @return BillingAccrual
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
     *
     * @return BillingAccrual
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
     * Set subscriber
     *
     * @param \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingSubscriber $subscriber
     *
     * @return BillingAccrual
     */
    public function setSubscriber(\PersonalArea\Bundle\ClientAreaBundle\Entity\BillingSubscriber $subscriber = null)
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    /**
     * Get subscriber
     *
     * @return \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingSubscriber
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * Set client
     *
     * @param \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingClient $client
     *
     * @return BillingAccrual
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

