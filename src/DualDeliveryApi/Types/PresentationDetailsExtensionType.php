<?php

declare(strict_types=1);

namespace Dbp\Relay\DispatchBundle\DualDeliveryApi\Types;

class PresentationDetailsExtensionType
{
    /**
     * @var PresentationDetailsExtensionType
     */
    protected $PresentationDetailsExtension = null;

    /**
     * @var CustomType
     */
    protected $Custom = null;

    /**
     * @param PresentationDetailsExtensionType $PresentationDetailsExtension
     * @param CustomType                       $Custom
     */
    public function __construct($PresentationDetailsExtension, $Custom)
    {
        $this->PresentationDetailsExtension = $PresentationDetailsExtension;
        $this->Custom = $Custom;
    }

    public function getPresentationDetailsExtension(): PresentationDetailsExtensionType
    {
        return $this->PresentationDetailsExtension;
    }

    public function setPresentationDetailsExtension(PresentationDetailsExtensionType $PresentationDetailsExtension): self
    {
        $this->PresentationDetailsExtension = $PresentationDetailsExtension;

        return $this;
    }

    public function getCustom(): CustomType
    {
        return $this->Custom;
    }

    public function setCustom(CustomType $Custom): self
    {
        $this->Custom = $Custom;

        return $this;
    }
}
