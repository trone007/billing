<?php

namespace PersonalArea\Bundle\ClientAreaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BillingClientDiscount
 */
class BillingClientDiscount
{
    /**
     * @var \DateTime
     */
    private $beginDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingDiscount
     */
    private $discount;

    /**
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingClient
     */
    private $client;


    /**
     * Set beginDate
     *
     * @param \DateTime $beginDate
     * @return BillingClientDiscount
     */
    public function setBeginDate($beginDate)
    {
        $this->beginDate = $beginDate;

        return $this;
    }

    /**
     * Get beginDate
     *
     * @return \DateTime 
     */
    public function getBeginDate()
    {
        return $this->beginDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return BillingClientDiscount
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
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
     * @return BillingClientDiscount
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
     * Set client
     *
     * @param \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingClient $client
     * @return BillingClientDiscount
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
