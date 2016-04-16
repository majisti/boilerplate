<?php

namespace Tests\Codeception\Extension;

use Codeception\Event\TestEvent;
use Codeception\Extension;
use Mockery\Adapter\Phpunit\TestListener;

class MockeryExtension extends Extension
{
    public static $events = array(
        'test.fail' => 'afterTest',
        'test.success' => 'afterTest',
        'test.error' => 'afterTest',
    );

    /**
     * @var TestListener
     */
    private $mockeryTestListener;

    /**
     * @param $config
     * @param $options
     */
    public function __construct($config, $options)
    {
        parent::__construct($config, $options);

        $this->mockeryTestListener = new TestListener();
    }

    /**
     * {@inheritdoc}
     */
    public function afterTest(TestEvent $event)
    {
        $this->mockeryTestListener->endTest($event->getTest(), $event->getTime());
    }
}
