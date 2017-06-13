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

use Fluent\Logger\Entity;
use Fluent\Logger\FluentLogger;
use Monolog\Logger;
use Musement\MonologFluentdBundle\Monolog\Handler\FluentdHandler;

class FluentdHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $message = 'Test message';
        $context = ['x' => 1];
        $level = Logger::DEBUG;
        $record = $this->getRecord($level, $message, $context);

        $spyLogger = $this
            ->getMockBuilder(FluentLogger::class)
            ->getMock();

        $handler = new FluentdHandler($level, true, $spyLogger);

        $spyLogger->expects($this->once())
            ->method('post2')
            ->with(new Entity(
                'test.'.Logger::getLevelName($level),
                $record,
                $record['datetime']->getTimestamp()
            ))
        ;

        $handler->handle($record);
    }

    protected function getRecord($level = Logger::WARNING, $message = 'test', array $context = [])
    {
        return [
            'channel' => 'test',
            'message' => $message,
            'level' => $level,
            'level_name' => Logger::getLevelName($level),
            'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true))),
            'context' => $context,
            'extra' => [],
        ];
    }
}
