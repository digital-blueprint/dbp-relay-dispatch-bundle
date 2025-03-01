<?php

declare(strict_types=1);

namespace Dbp\Relay\DispatchBundle\Service;

use Dbp\Relay\DispatchBundle\DualDeliveryApi\DualDeliveryClient;
use Dbp\Relay\DispatchBundle\DualDeliveryApi\Types\DualDelivery\ApplicationID;
use Dbp\Relay\DispatchBundle\DualDeliveryApi\Types\DualDelivery\SenderProfile;
use Dbp\Relay\DispatchBundle\DualDeliveryApi\Types\DualDeliveryNotification\DualNotificationRequestType;
use Dbp\Relay\DispatchBundle\DualDeliveryApi\Types\DualDeliveryNotification\EDeliveryNotificationType;
use Dbp\Relay\DispatchBundle\DualDeliveryApi\Types\DualDeliveryNotification\PostalNotificationType;
use Dbp\Relay\DispatchBundle\DualDeliveryApi\Types\DualDeliveryNotification\StatusRequestType;
use Dbp\Relay\DispatchBundle\Helpers\Tools;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Uid\Uuid;

/**
 * This wraps a fully configured DualDeliveryClient() and some bundle configuration and exposes
 * a simplified API for working with the SOAP client.
 */
class DualDeliveryService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public const DOCUMENT_MIME_TYPE = 'application/pdf';

    private const APPLICATION_ID = 'relay-dispatch-bundle';
    private const APPLICATION_VERSION = '0.1';

    private ?array $config = null;

    public function __construct()
    {
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getClient(): DualDeliveryClient
    {
        $config = $this->config;

        $serviceUrl = $config['service_url'];
        $cert = $config['cert'];
        $certPassword = $config['cert_password'];

        $certFileName = Tools::getTempFileName('.pem');
        file_put_contents($certFileName, $cert);

        return new DualDeliveryClient($serviceUrl, [$certFileName, $certPassword], true);
    }

    public function checkConnection(): void
    {
        $client = $this->getClient();

        $status = new StatusRequestType();
        $response = $client->dualStatusRequestOperation($status);
        // Expect a proper response object with some error status set
        if ($response->getStatus() === null) {
            throw new \RuntimeException('missing status');
        }
    }

    public function getSenderProfile(): SenderProfile
    {
        $config = $this->config;
        $profile = $config['sender_profile'];
        $profileVersion = $config['sender_profile_version'];

        return new SenderProfile($profile, $profileVersion);
    }

    public function getApplicationID(): ApplicationID
    {
        return new ApplicationID(self::APPLICATION_ID, self::APPLICATION_VERSION);
    }

    /**
     * Creates a new AppDeliveryID.
     * Every Request should have its own unique ID, so related requests/responses can be connected.
     */
    public function createAppDeliveryID(): string
    {
        // Use the format suggested in the official docs
        return 'ADID_'.self::APPLICATION_ID.'-'.(string) time().'-'.Uuid::v4()->toRfc4122();
    }

    /**
     * Creates a new RecipientID.
     */
    public function createRecipientID(): string
    {
        return 'RID_'.Uuid::v4()->toRfc4122();
    }

    public static function getErrorTextFromStatusResponse(DualNotificationRequestType $request): ?string
    {
        $result = $request->getResult();

        if (!$result) {
            return '';
        }

        $error = $result->getError();

        if (!$error) {
            return '';
        }

        return $error->getInfo();
    }

    public static function getPdfFromDeliveryNotification(DualNotificationRequestType $request): ?string
    {
        $result = $request->getResult();
        if ($result === null) {
            return null;
        }
        $notificationChannel = $request->getResult()->getNotificationChannel();
        if ($notificationChannel === null) {
            return null;
        }

        // Check for EDeliveryNotification (from eDelivery)
        $notification = $notificationChannel->getEDeliveryNotification();
        if ($notification !== null) {
            return self::getPdfFromEDeliveryNotification($notification);
        }

        // Check for PostalNotification (from postal delivery)
        $notification = $notificationChannel->getPostalNotification();
        if ($notification !== null) {
            return self::getPdfFromPostalDeliveryNotification($notification);
        }

        return null;
    }

    /**
     * When the SOAP presentation of AdditonalPrintResults (and thus getSendingServiceMessageIDFromDeliveryNotification)
     * was broken this acted as a workaround.
     */
    public static function getSendingServiceMessageIDFromDeliveryNotificationXML($xmlContent): ?string
    {
        $xml = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOCDATA);

        $parameters = $xml->xpath('//*[local-name()="Parameter"]');

        foreach ($parameters as $param) {
            $property = $param->xpath('*[local-name()="Property"]')[0];
            $value = $param->xpath('*[local-name()="Value"]')[0];

            if ((string) $property === 'SendingServiceMessageID') {
                return (string) $value;
            }
        }

        return null;
    }

    /**
     * Returns the SendingServiceMessageID from the DualNotificationRequestType.
     * A lot of SOAP classes and the DualeZustellung_Notification.xsd had to be adapted for this to work!
     */
    public static function getSendingServiceMessageIDFromDeliveryNotification(DualNotificationRequestType $request): ?string
    {
        $result = $request->getResult();
        if ($result === null) {
            return null;
        }
        $notificationChannel = $request->getResult()->getNotificationChannel();
        if ($notificationChannel === null) {
            return null;
        }

        $notification = $notificationChannel->getPostalNotification();
        if ($notification === null) {
            return null;
        }

        $additonalPrintResults = $notification->getAdditonalPrintResults();

        foreach ($additonalPrintResults as $result) {
            // I hope these are really always set, the classes say so
            $propertyValuePrintResultSet = $result->getPropertyValuePrintResultSet();
            $parameter = $propertyValuePrintResultSet->getParameter();
            $property = $parameter->getProperty();

            if ($property === 'SendingServiceMessageID') {
                return $parameter->getValue();
            }
        }

        return null;
    }

    /**
     * Returns the description of the status change for unclaimed documents.
     */
    public static function getDeliveryNotificationForUnclaimedDescription(DualNotificationRequestType $request): ?string
    {
        $result = $request->getResult();
        if ($result === null) {
            return null;
        }
        $notificationChannel = $request->getResult()->getNotificationChannel();
        if ($notificationChannel === null) {
            return null;
        }

        // Check for PostalNotification (from postal delivery)
        $notification = $notificationChannel->getPostalNotification();

        if (!$notification) {
            return null;
        }

        $scannedData = $notification->getScannedData();

        if (!$scannedData) {
            return null;
        }

        $metaData = $scannedData->getExtractedMetaData();

        if (!$metaData) {
            return null;
        }

        $xml = new \SimpleXMLElement($metaData->getAny());

        $status = $xml->SendungStatus;

        if (!$status) {
            return null;
        }

        $statusID = (string) $status->StatusID;
        $statusText = (string) $status->StatusText;
        $date = (string) $status->Datum;
        $area = (string) $status->Abgabebereich;
        $barcode = (string) $status->Barcode;

        return 'Status: '.$statusID.' ('.$statusText.")\nDate: ".$date."\nDelivery Area: ".$area."\nBarcode: ".$barcode;
    }

    /**
     * Fetches the PDF from the EDeliveryNotification.
     */
    public static function getPdfFromEDeliveryNotification(EDeliveryNotificationType $notification): ?string
    {
        $binaryNotification = $notification->getBinaryDeliveryNotification();

        $xml = new \SimpleXMLElement($binaryNotification);

        foreach ($xml->getDocNamespaces() as $strPrefix => $strNamespace) {
            if (strlen($strPrefix) === 0) {
                $strPrefix = 'ns';
            }

            $xml->registerXPathNamespace($strPrefix, $strNamespace);
        }

        $binaries = $xml->xpath('//ns:AdditionalFormat');

        foreach ($binaries as $binary) {
            $type = (string) $binary['Type'];

            if ($type === self::DOCUMENT_MIME_TYPE) {
                return base64_decode((string) $binary, true);
            }
        }

        return null;
    }

    /**
     * Fetches the PDF from the PostalNotification.
     */
    public static function getPdfFromPostalDeliveryNotification(PostalNotificationType $notification): ?string
    {
        $scannedData = $notification->getScannedData();

        if (!$scannedData) {
            return null;
        }

        $binaryData = $scannedData->getBinaryDocument();

        if (!$binaryData) {
            return null;
        }

        return $binaryData->getContent();
    }
}
