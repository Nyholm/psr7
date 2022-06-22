<?php

namespace Tests\Nyholm\Psr7\Integration;

use Psr\Http\Message\UploadedFileInterface;
use Http\Psr7Test\UploadedFileIntegrationTest;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Stream;

class UploadedFileTest extends UploadedFileIntegrationTest
{
    public function createSubject(): UploadedFileInterface
    {
        return (new Psr17Factory())->createUploadedFile(Stream::create('writing to tempfile'));
    }
}
