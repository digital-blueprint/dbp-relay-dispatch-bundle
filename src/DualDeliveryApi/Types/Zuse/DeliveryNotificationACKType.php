<?php

declare(strict_types=1);

namespace Dbp\Relay\DispatchBundle\DualDeliveryApi\Types\Zuse;

class DeliveryNotificationACKType
{
    /**
     * @var string
     */
    protected $AppDeliveryID = null;

    /**
     * @var string
     */
    protected $ZSDeliveryID = null;

    /**
     * @var string
     */
    protected $version = null;

    public function __construct(string $AppDeliveryID, string $ZSDeliveryID, string $version)
    {
        $this->AppDeliveryID = $AppDeliveryID;
        $this->ZSDeliveryID = $ZSDeliveryID;
        $this->version = $version;
    }

    public function getAppDeliveryID(): string
    {
        return $this->AppDeliveryID;
    }

    public function setAppDeliveryID(string $AppDeliveryID): void
    {
        $this->AppDeliveryID = $AppDeliveryID;
    }

    public function getZSDeliveryID(): string
    {
        return $this->ZSDeliveryID;
    }

    public function setZSDeliveryID(string $ZSDeliveryID): void
    {
        $this->ZSDeliveryID = $ZSDeliveryID;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }
}
