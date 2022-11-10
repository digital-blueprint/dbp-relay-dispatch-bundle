<?php

declare(strict_types=1);

namespace Dbp\Relay\DispatchBundle\DualDeliveryApi\Types;

class DigestMethodType
{
    /**
     * @var string
     */
    protected $any = null;

    /**
     * @var AnyURI
     */
    protected $Algorithm = null;

    /**
     * @param string $any
     * @param AnyURI $Algorithm
     */
    public function __construct($any, $Algorithm)
    {
        $this->any = $any;
        $this->Algorithm = $Algorithm;
    }

    public function getAny(): string
    {
        return $this->any;
    }

    public function setAny(string $any): self
    {
        $this->any = $any;

        return $this;
    }

    public function getAlgorithm(): AnyURI
    {
        return $this->Algorithm;
    }

    public function setAlgorithm(AnyURI $Algorithm): self
    {
        $this->Algorithm = $Algorithm;

        return $this;
    }
}
