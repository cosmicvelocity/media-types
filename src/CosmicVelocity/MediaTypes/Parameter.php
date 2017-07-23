<?php

namespace CosmicVelocity\MediaTypes;

/**
 * Abstract media type parameter.
 *
 * @package CosmicVelocity\MediaTypes
 */
class Parameter
{

    /**
     * parameter name.
     *
     * @var string
     */
    private $name;

    /**
     * parameter value.
     *
     * @var string
     */
    private $value;

    /**
     * Parameter constructor.
     *
     * @param string $name parameter name.
     * @param string $value parameter value.
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->name . '=' . $this->value;
    }

    /**
     * Gets the parameter name.
     *
     * @return string parameter name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of the parameter.
     *
     * @return string parameter value.
     */
    public function getValue()
    {
        return $this->value;
    }

}
