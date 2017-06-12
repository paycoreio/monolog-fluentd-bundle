<?php

/*
 * This file is part of "musement/monolog-fluentd-bundle".
 *
 * (c) Musement S.p.A. <oss@musement.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Musement\MonologFluentdBundle\Tests\Monolog\Handler;

use Fluent\Logger\FluentLogger;
use Monolog\Logger;
use Musement\MonologFluentdBundle\Monolog\Handler\FluentdHandler;

class FluentdHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->fluentLoggerMock = $this->getMockBuilder(FluentLogger::class)
        ->getMock();
    }

    public function testHandle()
    {
        $handler = $this->createHandlerInstance();

        $message = 'a.b.c';
        $context = ['x' => 1];
        $record = $this->getRecord(Logger::WARNING, $message, $context);

        $this->fluentLoggerMock->expects($this->once())
            ->method('post')
            ->with('test.a.b.c', $context);

        $handler->handle($record);
    }

    public function testHandleBatchNotWritesToFluentdIfMessagesAreBelowLevel()
    {
        $records = [
            $this->getRecord(Logger::DEBUG, 'debug message 1'),
            $this->getRecord(Logger::DEBUG, 'debug message 2'),
            $this->getRecord(Logger::INFO, 'information'),
        ];

        $handler = $this->createHandlerInstance();
        $handler->setLevel(Logger::ERROR);

        $this->fluentLoggerMock->expects($this->never())
        ->method('post');

        $handler->handleBatch($records);
    }

    protected function getRecord($level = Logger::WARNING, $message = 'test', $context = [])
    {
        return [
            'message' => $message,
            'context' => $context,
            'level' => $level,
            'level_name' => Logger::getLevelName($level),
            'channel' => 'test',
            'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true))),
            'extra' => [],
        ];
    }

    /**
     * Create an instance with default args and `makeFluentLogger` mocked.
     */
    private function createHandlerInstance()
    {
        $hostname = 'foo';
        $port = 1234;
        $options = ['bar' => 'baz'];

        $fluentdHandlerMock = $this->getMockBuilder(FluentdHandler::class)
        ->setMethods(['makeFluentLogger'])
        ->disableOriginalConstructor()
        ->getMock();

        $fluentdHandlerMock->expects($this->once())
        ->method('makeFluentLogger')
        ->willReturn($this->fluentLoggerMock);

        $fluentdHandlerMock->__construct($hostname, $port, $options);

        return $fluentdHandlerMock;
    }
}
