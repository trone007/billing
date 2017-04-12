<?php

namespace PersonalArea\Bundle\ClientAreaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BillingService
 */
class BillingService
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $code;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \PersonalArea\Bundle\ClientAreaBundle\Entity\BillingSubscriber
     */
    private $subscriber;


    /**
     * Set name
     *
     * @param string $name
     * @return BillingService
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return BillingService
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
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
     * @return BillingService
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

    public function __toString()
    {
        return $this->name;
    }
}
