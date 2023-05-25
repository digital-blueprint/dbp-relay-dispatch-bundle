<?php

declare(strict_types=1);

namespace Dbp\Relay\DispatchBundle\Helpers;

class Tools
{
    /**
     * Convert binary data to a data url.
     */
    public static function getDataURI(string $data, string $mime): string
    {
        return 'data:'.$mime.';base64,'.base64_encode($data);
    }

    /**
     * Creates a random filename with a given extension.
     */
    public static function getTempFileName($extension = 'tmp'): string
    {
        return sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid('dispatch_').'.'.$extension;
    }

    public static function humanFileSize($bytes, $decimals = 2): string
    {
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = (int) floor((strlen((string) $bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)).@$size[$factor];
    }
}
