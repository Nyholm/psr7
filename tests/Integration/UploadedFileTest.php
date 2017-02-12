<?php

namespace Tests\Nyholm\Psr7\Integration;

use Http\Psr7Test\UploadedFileIntegrationTest;
use Nyholm\Psr7\UploadedFile;

class UploadedFileTest extends UploadedFileIntegrationTest
{
    public function createSubject()
    {

        return new UploadedFile();
    }
}
