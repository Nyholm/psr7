<?php

declare(strict_types=1);

namespace Nyholm\Psr7\Factory;

use Interop\Http\Factory\UploadedFileFactoryInterface;
use Nyholm\Psr7\UploadedFile;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class UploadedFileFactory implements UploadedFileFactoryInterface
{
    public function createUploadedFile(
        $file,
        $size = null,
        $error = \UPLOAD_ERR_OK,
        $clientFilename = null,
        $clientMediaType = null
    ) {
        if ($size === null) {
            if (is_string($file)) {
                $size = filesize($file);
            } else {
                $stats = fstat($file);
                $size = $stats['size'];
            }
        }

        return new UploadedFile($file, $size, $error, $clientFilename, $clientMediaType);
    }
}
