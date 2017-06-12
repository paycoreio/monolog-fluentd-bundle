<?php

/*
 * This file is part of "musement/monolog-fluentd-bundle".
 *
 * (c) Musement S.p.A. <oss@musement.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Musement\MonologFluentdBundle\Monolog\Handler;

use Fluent\Logger\FluentLogger;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class FluentdHandler extends AbstractProcessingHandler
{
    /**
     * @var FluentLogger
     */
    protected $logger;

    /**
     * Handler constructor.
     *
     * @param string $host
     * @param int    $port
     * @param array  $options FluentLogger options
     * @param int    $level   The minimum logging level at which this handler will be triggered
     * @param bool   $bubble  Whether the messages that are handled can bubble up the stack or not
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $level = Logger::DEBUG,
        $bubble = true,
        $host = FluentLogger::DEFAULT_ADDRESS,
        $port = FluentLogger::DEFAULT_LISTEN_PORT,
        $options = []
    ) {
        parent::__construct($level, $bubble);
        $this->logger = $this->makeFluentLogger($host, $port, $options);

        // By default FluentLogger would write to stderr for every message gone wrong.
        // We find it a bad default (you would probably start to log myriad of data as error).
        // You can reset the same or a different error handler by accessing the logger with getFluentLogger();
        $this->logger->registerErrorHandler(function ($logger, $entity, $error) {});
    }

    /**
     * Get the internal FluentLogger instance.
     *
     * @return FluentLogger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->logger->close();
    }

    /**
     * Create a new instance of FluentLogger.
     *
     * @param string $host
     * @param int    $port
     * @param array  $options FluentLogger options
     *
     * @return FluentLogger
     */
    protected function makeFluentLogger($host, $port, array $options = [])
    {
        return new FluentLogger($host, $port, $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $this->logger->post($this->formatTag($record), $record['context']);
    }

    /**
     * Format a fluentd tag using the record data.
     *
     * @param array $record
     *
     * @return string the tag
     */
    protected function formatTag(array $record)
    {
        return sprintf('%s.%s', $record['channel'], $record['message']);
    }

    /**
     * @return \Monolog\Formatter\FormatterInterface
     */
    protected function getDefaultFormatter()
    {
        return new JsonFormatter();
    }
}
