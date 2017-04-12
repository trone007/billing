<?php

namespace PersonalArea\Bundle\ClientAreaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BillingClientPrice
 */
class BillingClientPrice
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
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingPrice
     */
    private $price;

    /**
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingClient
     */
    private $client;


    /**
     * Set beginDate
     *
     * @param \DateTime $beginDate
     * @return BillingClientPrice
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
     * @return BillingClientPrice
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
     * Set price
     *
     * @param \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingPrice $price
     * @return BillingClientPrice
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
     * Set client
     *
     * @param \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingClient $client
     * @return BillingClientPrice
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
