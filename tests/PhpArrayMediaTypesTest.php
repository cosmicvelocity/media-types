<?php

namespace CosmicVelocity\MediaTypes\Tests;

use CosmicVelocity\MediaTypes\PhpArrayMediaTypes;
use PHPUnit\Framework\TestCase;

/**
 * Class PhpArrayMediaTypesTest
 *
 * @package CosmicVelocity\MediaTypes\Tests
 */
class PhpArrayMediaTypesTest extends TestCase
{

    public function testGetExtension()
    {
        $mediaTypes = new PhpArrayMediaTypes();
        $extensions = $mediaTypes->matchesExtension('image/jpeg');

        $this->assertEquals(['jpe', 'jpeg', 'jpg'], $extensions);
    }

    public function testGetMediaType()
    {
        $mediaTypes = new PhpArrayMediaTypes();
        $mediaType = $mediaTypes->getMediaType('sample.pdf');

        $this->assertEquals('application', $mediaType->getType());
        $this->assertEquals('pdf', $mediaType->getSubType());
    }

    public function testGetMediaTypeIsExperimental()
    {
        $extension = new PhpArrayMediaTypes();
        $mediaType = $extension->getMediaType('sample.tex');

        $this->assertEquals('application', $mediaType->getType());
        $this->assertEquals('x-tex', $mediaType->getSubType());
        $this->assertTrue($mediaType->isExperimental());
        $this->assertFalse($mediaType->isUnregistered());
    }

    public function testGetMediaTypeWithCustomMapping()
    {
        $mediaTypes = new PhpArrayMediaTypes([
            'hoge' => 'application/prs.hoge+xml'
        ]);
        $mediaType = $mediaTypes->getMediaType('sample.hoge');

        $this->assertEquals('application', $mediaType->getType());
        $this->assertEquals('prs.hoge+xml', $mediaType->getSubType());
        $this->assertEquals('prs', $mediaType->getTree());
        $this->assertEquals('xml', $mediaType->getSuffix());
    }

}
