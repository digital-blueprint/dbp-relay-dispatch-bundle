<?php

declare(strict_types=1);

namespace Dbp\Relay\DispatchBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "post" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/dispatch/requests",
 *             "openapi_context" = {
 *                 "tags" = {"Dispatch"},
 *                 "requestBody" = {
 *                     "content" = {
 *                         "application/json" = {
 *                             "schema" = {"type" = "object"},
 *                             "example" = {"senderGivenName" = "Max", "senderFamilyName" = "Mustermann", "senderPostalAddress" = "Am Grund 1"},
 *                         }
 *                     }
 *                 },
 *             }
 *         },
 *         "get" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/dispatch/requests",
 *             "openapi_context" = {
 *                 "tags" = {"Dispatch"}
 *             },
 *         }
 *     },
 *     itemOperations={
 *         "get" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/dispatch/requests/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Dispatch"}
 *             },
 *         },
 *         "delete" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/dispatch/requests/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Dispatch"}
 *             },
 *         }
 *     },
 *     iri="https://schema.org/Action",
 *     shortName="DispatchRequest",
 *     normalizationContext={
 *         "groups" = {"DispatchRequest:output"},
 *         "jsonld_embed_context" = true
 *     },
 *     denormalizationContext={
 *         "groups" = {"DispatchRequest:input"},
 *         "jsonld_embed_context" = true
 *     }
 * )
 */
class Request
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"DispatchRequest:output"})
     */
    private $identifier;

    /**
     * @ApiProperty(iri="https://schema.org/dateCreated")
     * @Groups({"DispatchRequest:output"})
     *
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @ApiProperty(iri="https://schema.org/identifier")
     * @Groups({"DispatchRequest:output"})
     *
     * @var string
     */
    private $personIdentifier;

    /**
     * @ApiProperty(iri="https://schema.org/givenName")
     * @Groups({"DispatchRequest:output", "DispatchRequest:input"})
     *
     * @var string
     */
    private $senderGivenName;

    /**
     * @ApiProperty(iri="https://schema.org/familyName")
     * @Groups({"DispatchRequest:output", "DispatchRequest:input"})
     *
     * @var string
     */
    private $senderFamilyName;

    /**
     * @ApiProperty(iri="https://schema.org/address")
     * @Groups({"DispatchRequest:output", "DispatchRequest:input"})
     *
     * @var string
     */
    private $senderPostalAddress;

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

    public function getPersonIdentifier(): string
    {
        return $this->personIdentifier;
    }

    public function setPersonIdentifier(string $personIdentifier): void
    {
        $this->personIdentifier = $personIdentifier;
    }

    public function getSenderGivenName(): ?string
    {
        return $this->senderGivenName;
    }

    public function setSenderGivenName(string $senderGivenName): void
    {
        $this->senderGivenName = $senderGivenName;
    }

    public function getSenderFamilyName(): ?string
    {
        return $this->senderFamilyName;
    }

    public function setSenderFamilyName(string $senderFamilyName): void
    {
        $this->senderFamilyName = $senderFamilyName;
    }

    public function getSenderPostalAddress(): ?string
    {
        return $this->senderPostalAddress;
    }

    public function setSenderPostalAddress(string $senderPostalAddress): void
    {
        $this->senderPostalAddress = $senderPostalAddress;
    }

    public static function fromRequestPersistence(RequestPersistence $requestPersistence): Request
    {
        $request = new Request();
        $request->setIdentifier($requestPersistence->getIdentifier());
        $request->setPersonIdentifier($requestPersistence->getPersonIdentifier() === null ? '' : $requestPersistence->getPersonIdentifier());
        $request->setSenderGivenName($requestPersistence->getSenderGivenName() === null ? '' : $requestPersistence->getSenderGivenName());
        $request->setSenderFamilyName($requestPersistence->getSenderFamilyName() === null ? '' : $requestPersistence->getSenderFamilyName());
        $request->setSenderPostalAddress($requestPersistence->getSenderPostalAddress() === null ? '' : $requestPersistence->getSenderPostalAddress());

        if ($requestPersistence->getDateCreated() !== null) {
            $request->setDateCreated($requestPersistence->getDateCreated());
        }

        return $request;
    }

    /**
     * @param RequestPersistence[] $requestPersistences
     *
     * @return Request[]
     */
    public static function fromRequestPersistences(array $requestPersistences): array
    {
        $requests = [];

        foreach ($requestPersistences as $requestPersistence) {
            $requests[] = self::fromRequestPersistence($requestPersistence);
        }

        return $requests;
    }
}
