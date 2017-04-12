<?php

namespace PersonalArea\Bundle\ClientAreaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BillingSubscriberMethod
 */
class BillingSubscriberMethod
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingMethod
     */
    private $method;

    /**
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingSubscriber
     */
    private $subscriber;


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
     * Set method
     *
     * @param \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingMethod $method
     * @return BillingSubscriberMethod
     */
    public function setMethod(\PersonalArea\Bundle\ClientAreaBundle\Entity\BillingMethod $method = null)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingMethod 
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set subscriber
     *
     * @param \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingSubscriber $subscriber
     * @return BillingSubscriberMethod
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
}
