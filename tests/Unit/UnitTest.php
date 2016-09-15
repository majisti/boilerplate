<?php

namespace Tests\Unit;

use AspectMock\Test as AspectMock;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit_Framework_TestCase;
use Tests\UnitTester;
use Tests\Utils\Hamcrest;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
abstract class UnitTest extends PHPUnit_Framework_TestCase
{
    use Hamcrest;
    use MockeryPHPUnitIntegration;

    /**
     * The Unit\Bowling Under Test.
     */
    protected $uut;

    /**
     * @var UnitTester
     */
    protected $tester;

    protected function tearDown()
    {
        AspectMock::clean();
    }

    /**
     * @return mixed
     */
    protected function createUnitUnderTest()
    {
        $rc = new \ReflectionClass($this);

        $className = $this->removeUnitPrefixFromClassName($rc->getName());
        $className = $this->removeTestSuffixFromClassName($className);

        if (class_exists($className)) {
            return new $className();
        }

        return null;
    }
    
    protected function getUutNamespace(): string
    {
        $rc = new \ReflectionClass($this->uut());
        return $rc->getNamespaceName();
    }

    private function removeUnitPrefixFromClassName(string $className): string
    {
        return preg_replace('/^Unit\\\/', '', $className);
    }

    private function removeTestSuffixFromClassName(string $className): string
    {
        return preg_replace('/Test$/', '', $className);
    }

    protected function uut()
    {
        if (!$this->uut) {
            $this->uut = $this->createUnitUnderTest();
        }

        return $this->uut;
    }
}
