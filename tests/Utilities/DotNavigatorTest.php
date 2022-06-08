<?php

namespace GSpataro\Test;

use GSpataro\Utilities;
use PHPUnit\Framework\TestCase;

final class DotNavigatorTest extends TestCase
{
    /**
     * Return some mockup data
     *
     * @return array
     */

    public function getMockupData(): array
    {
        return [
            "foo" => "bar",
            "multilevel" => [
                "lorem" => "ipsum",
                "int" => 123,
                "null" => null
            ]
        ];
    }

    /**
     * Return a stub for DotNavigator
     *
     * @return object
     */

    public function getDotNavigatorStub(): object
    {
        return $this->getMockForAbstractClass(Utilities\DotNavigator::class);
    }

    /**
     * Set DotNavigator read only property to true
     *
     * @param object $dotNavigator
     * @return void
     */

    public function setReadOnly(object $dotNavigator): void
    {
        $reflection = new \ReflectionClass($dotNavigator);
        $property = $reflection->getProperty("readOnly");
        $property->setAccessible(true);
        $property->setValue($dotNavigator, true);
    }

    /**
     * Read DotNavigator data property
     *
     * @param object $dotNavigator
     * @return array
     */

    public function readDotNavigatorData(object $dotNavigator): array
    {
        $reflection = new \ReflectionClass($dotNavigator);
        $property = $reflection->getProperty("data");
        $property->setAccessible(true);

        return $property->getValue($dotNavigator);
    }

    /**
     * @testdox Test DotNavigator::init() method
     * @covers DotNavigator::init
     * @return void
     */

    public function testInit(): void
    {
        $this->expectException(Utilities\Exception\DataAlreadyInitializedException::class);

        $dotNavigator = $this->getDotNavigatorStub();
        $dotNavigator->init($this->getMockupData());
        $dotNavigator->init($this->getMockupData());
    }

    /**
     * @testdox Test DotNavigator::set() method
     * @covers DotNavigator::set
     * @return void
     */

    public function testSet(): void
    {
        $dotNavigator = $this->getDotNavigatorStub();
        $dotNavigator->set("new.item", "test");
        $dotNavigator->set("foo", "notbar");

        $data = $this->readDotNavigatorData($dotNavigator);
        $this->assertTrue(isset($data['new']['item']));
        $this->assertEquals($data['new']['item'], "test");
        $this->assertTrue(isset($data['foo']));
        $this->assertEquals($data['foo'], "notbar");
    }

    /**
     * @testdox Test DotNavigator::set() method with read only mode enabled
     * @covers DotNavigator::set
     * @return void
     */

    public function testSetReadOnly(): void
    {
        $this->expectException(Utilities\Exception\ReadOnlyEnabledException::class);

        $dotNavigator = $this->getDotNavigatorStub();
        $this->setReadOnly($dotNavigator);

        $dotNavigator->set("foo", "notbar");
    }

    /**
     * @testdox Test DotNavigator::get() method
     * @covers DotNavigator::get
     * @return void
     */

    public function testGet(): void
    {
        $dotNavigator = $this->getDotNavigatorStub();
        $dotNavigator->init($this->getMockupData());

        $this->assertEquals($dotNavigator->get("foo"), "bar");
        $this->assertEquals($dotNavigator->get("multilevel.lorem"), "ipsum");
        $this->assertEquals($dotNavigator->get("multilevel.null"), null);
        $this->assertEquals($dotNavigator->get("nonexisting"), null);
    }

    /**
     * @testdox Test DotNavigator::has() method
     * @covers DotNavigator::has
     * @return void
     */

    public function testHas(): void
    {
        $dotNavigator = $this->getDotNavigatorStub();
        $dotNavigator->init($this->getMockupData());

        $this->assertTrue($dotNavigator->has("foo"));
        $this->assertTrue($dotNavigator->has("multilevel.null"));
        $this->assertFalse($dotNavigator->has("non.existing"));
    }

    /**
     * @testdox Test DotNavigator::unset() method
     * @covers DotNavigator::unset
     * @return void
     */

    public function testUnset(): void
    {
        $dotNavigator = $this->getDotNavigatorStub();
        $dotNavigator->init($this->getMockupData());

        $dataBefore = $this->readDotNavigatorData($dotNavigator);
        $this->assertTrue(isset($dataBefore['foo']));
        $this->assertTrue(isset($dataBefore['multilevel']['lorem']));
        $this->assertTrue(isset($dataBefore['multilevel']['int']));

        $dotNavigator->unset("foo");
        $dotNavigator->unset("multilevel.lorem");

        $dataAfter = $this->readDotNavigatorData($dotNavigator);
        $this->assertFalse(isset($dataAfter['foo']));
        $this->assertFalse(isset($dataAfter['multilevel']['lorem']));
        $this->assertTrue(isset($dataAfter['multilevel']['int']));
    }

    /**
     * @testdox Test DotNavigator::unset() method with read only mode enabled
     * @covers DotNavigator::test
     * @return void
     */

    public function testUnsetReadOnly(): void
    {
        $this->expectException(Utilities\Exception\ReadOnlyEnabledException::class);

        $dotNavigator = $this->getDotNavigatorStub();
        $dotNavigator->init($this->getMockupData());
        $this->setReadOnly($dotNavigator);

        $dotNavigator->unset("foo");
    }

    /**
     * @testdox Test DotNavigator::getAll() method
     * @covers DotNavigator::getAll
     * @return void
     */

    public function testGetAll(): void
    {
        $dotNavigator = $this->getDotNavigatorStub();
        $dotNavigator->init($this->getMockupData());

        $this->assertSame($dotNavigator->getAll(), $this->getMockupData());
    }
}
