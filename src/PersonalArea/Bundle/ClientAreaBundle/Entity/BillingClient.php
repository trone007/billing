<?php

namespace PersonalArea\Bundle\ClientAreaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use External\ServiceBundle\DostecBillingClient;

/**
 * BillingClient
 */
class BillingClient
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $inn;
    /**
     * @var integer
     */
    private $id;

    /**
     * Set name
     *
     * @param string $name
     * @return BillingClient
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
     * Set inn
     *
     * @param string $inn
     * @return BillingClient
     */
    public function setInn($inn)
    {
        $this->inn = $inn;

        return $this;
    }

    /**
     * Get inn
     *
     * @return string
     */
    public function getInn()
    {
        return $this->inn;
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

    public function __toString()
    {
        return $this->name . ' - ' .$this->inn;
    }
}
