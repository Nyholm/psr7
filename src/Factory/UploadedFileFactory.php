<?php

declare(strict_types=1);

namespace Nyholm\Psr7\Factory;

use Interop\Http\Factory\UploadedFileFactoryInterface;
use Nyholm\Psr7\UploadedFile;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * @internal This class does not fall under our BC promise. We will adapt to changes to the http-interop/http-factory.
 * This class will be finalized when the PSR-17 is accepted.
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
        if (is_string($file)) {
            // This is string content
            $content = $file;
            $file = fopen(sys_get_temp_dir().'/'.uniqid('uploaded_file', true), 'w+');
            fwrite($file, $content);
        }

        if (null === $size) {
            $stats = fstat($file);
            $size = $stats['size'];
        }

        return new UploadedFile($file, $size, $error, $clientFilename, $clientMediaType);
    }
}
