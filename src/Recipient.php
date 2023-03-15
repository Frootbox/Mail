<?php
/**
 *
 */

namespace Frootbox\Mail;

class Recipient
{
    protected $address;
    protected $name;

    /**
     *
     */
    public function __construct(string $address, string $name = null)
    {
        $this->name = $name;
        $this->address = $address;
    }

    /**
     * Convert recipient to string
     *
     * @return string
     */
    public function __toString(): string
    {
        $string = '<' . $this->getAddress() . '>';

        if (!empty($this->getName())) {
            $string = $this->getName() . ' ' . $string;
        }

        return $string;
    }

    /**
     *
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     *
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set recipients address
     *
     * @param string $address
     * @return void
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }
}
