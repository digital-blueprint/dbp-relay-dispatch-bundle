<?php

declare(strict_types=1);

namespace Dbp\Relay\DispatchBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="dispatch_request_statuses")
 * @ApiResource(
 *     collectionOperations={
 *         "get" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/dispatch/request-statuses",
 *             "openapi_context" = {
 *                 "tags" = {"Dispatch"}
 *             },
 *         }
 *     },
 *     itemOperations={
 *         "get" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/dispatch/request-statuses/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Dispatch"}
 *             },
 *         },
 *     },
 *     iri="https://schema.org/Status",
 *     shortName="DispatchRequestStatus",
 *     normalizationContext={
 *         "groups" = {"DispatchRequestStatus:output", "DispatchRequest:output"},
 *         "jsonld_embed_context" = true
 *     }
 * )
 */
class RequestStatus
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=50)
     * @ApiProperty(identifier=true)
     * @Groups({"DispatchRequestStatus:output", "DispatchRequest:output"})
     */
    private $identifier;

    /**
     * @ORM\Column(type="datetime")
     * @ApiProperty(iri="https://schema.org/dateCreated")
     * @Groups({"DispatchRequestStatus:output", "DispatchRequest:output"})
     *
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @ORM\ManyToOne(targetEntity="Request", inversedBy="statuses")
     * @ORM\JoinColumn(name="dispatch_request_identifier", referencedColumnName="identifier")
     * @ApiProperty
     * @Groups({"DispatchRequestStatus:output"})
     *
     * @var Request
     */
    private $request;

    /**
     * @ORM\Column(type="string", length=50)
     * @ApiProperty(iri="https://schema.org/identifier")
     * @Groups({"DispatchRequestStatus:output"})
     *
     * @var string
     */
    private $dispatchRequestIdentifier;

    /**
     * @ORM\Column(type="integer")
     * @ApiProperty(iri="https://schema.org/statusType")
     * @Groups({"DispatchRequestStatus:output", "DispatchRequest:output"})
     *
     * @var int
     */
    private $statusType;

    /**
     * @ORM\Column(type="text")
     * @ApiProperty(iri="https://schema.org/description")
     * @Groups({"DispatchRequestStatus:output", "DispatchRequest:output"})
     *
     * @var string
     */
    private $description;

    public function getIdentifier(): string
    {
        return (string) $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getDateCreated(): \DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTime $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    public function getDispatchRequest(): Request
    {
        return $this->request;
    }

    public function getDispatchRequestIdentifier(): string
    {
        return $this->dispatchRequestIdentifier;
    }

    public function setDispatchRequestIdentifier(string $dispatchRequestIdentifier): void
    {
        $this->dispatchRequestIdentifier = $dispatchRequestIdentifier;
    }

    public function getStatusType(): int
    {
        return $this->statusType;
    }

    public function setStatusType(int $statusType): void
    {
        $this->statusType = $statusType;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}
