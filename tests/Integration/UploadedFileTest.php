<?php

namespace Tests\Nyholm\Psr7\Integration;

use Http\Psr7Test\UploadedFileIntegrationTest;
use Nyholm\Psr7\Factory\UploadedFileFactory;

class UploadedFileTest extends UploadedFileIntegrationTest
{
    public function createSubject()
    {
        return (new UploadedFileFactory())->createUploadedFile('writing to tempfile');
    }
}
