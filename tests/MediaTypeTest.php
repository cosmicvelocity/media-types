<?php

namespace CosmicVelocity\MediaTypes\Tests;

use CosmicVelocity\MediaTypes\InvalidMediaTypeException;
use CosmicVelocity\MediaTypes\MediaType;
use PHPUnit\Framework\TestCase;

/**
 * Class MediaTypeTest
 *
 * @package CosmicVelocity\MediaTypes\Tests
 */
class MediaTypeTest extends TestCase
{

    public function testFromFile()
    {
        $mediaType = MediaType::fromFile('./composer.json');

        $this->assertEquals('text', $mediaType->getType());
        $this->assertEquals('plain', $mediaType->getSubType());
    }

    public function testFromMime()
    {
        $mediaType = MediaType::fromMime('text/plain; charset=iso-2022-jp; format=flowed; delsp=yes');

        $this->assertInstanceOf('CosmicVelocity\MediaTypes\MediaType', $mediaType);
        $this->assertEquals('text', $mediaType->getType());
        $this->assertEquals('plain', $mediaType->getSubType());
        $this->assertEquals('iso-2022-jp', $mediaType->getParameter('charset')->getValue());
    }

    public function testFromMimeInvalidFormat()
    {
        $this->setExpectedException('CosmicVelocity\MediaTypes\InvalidMediaTypeException');

        MediaType::fromMime('text//');
    }

    public function testFromMimeParameterInQuart()
    {
        $mediaType = MediaType::fromMime('text/plain; charset="iso-2022-jp"');

        $this->assertInstanceOf('CosmicVelocity\MediaTypes\MediaType', $mediaType);
        $this->assertEquals('iso-2022-jp', $mediaType->getParameter('charset')->getValue());
    }

    public function testFromMimeWithSuffix()
    {
        $mediaType = MediaType::fromMime('application/calendar+json; charset=utf-8');

        $this->assertInstanceOf('CosmicVelocity\MediaTypes\MediaType', $mediaType);
        $this->assertEquals('application', $mediaType->getType());
        $this->assertEquals('calendar+json', $mediaType->getSubType());
        $this->assertEquals('json', $mediaType->getSuffix());
        $this->assertEquals('utf-8', $mediaType->getParameter('charset')->getValue());
    }

    public function testFromMimeWithTree()
    {
        $mediaType = MediaType::fromMime('application/vnd.adobe.flash-movie');

        $this->assertInstanceOf('CosmicVelocity\MediaTypes\MediaType', $mediaType);
        $this->assertEquals('application', $mediaType->getType());
        $this->assertEquals('vnd.adobe.flash-movie', $mediaType->getSubType());
        $this->assertEquals('vnd', $mediaType->getTree());
    }

    public function testIsUnregistered()
    {
        $this->assertTrue(MediaType::fromMime('application/x.sample+xml')->isUnregistered());
        $this->assertFalse(MediaType::fromMime('application/x-sample+xml')->isUnregistered());
    }

    public function testIsValidSubType()
    {
        $this->assertTrue(MediaType::isValidSubType('json'));
        $this->assertTrue(MediaType::isValidSubType('xml'));
        $this->assertFalse(MediaType::isValidSubType('error/'));
    }

    public function testIsValidSuffix()
    {
        $this->assertTrue(MediaType::isValidSuffix('xml'));
        $this->assertFalse(MediaType::isValidSuffix('txt'));
    }

    public function testIsValidType()
    {
        $this->assertTrue(MediaType::isValidType('text'));
        $this->assertTrue(MediaType::isValidType('image'));
        $this->assertFalse(MediaType::isValidType('sound'));
    }

}
