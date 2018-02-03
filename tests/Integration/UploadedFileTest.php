<?php

namespace Tests\Nyholm\Psr7\Integration;

use Http\Psr7Test\UploadedFileIntegrationTest;
use Nyholm\Psr7\Factory\UploadedFileFactory;

class UploadedFileTest extends UploadedFileIntegrationTest
{
    public function createSubject()
    {
        $tmpfname = tempnam('/tmp', 'foobar');
        $handle = fopen($tmpfname, 'w');
        fwrite($handle, 'writing to tempfile');
        fclose($handle);

        return (new UploadedFileFactory())->createUploadedFile($tmpfname);
    }
}
