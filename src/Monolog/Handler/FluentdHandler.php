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
     * @param int    $level   The minimum logging level at which this handler will be triggered
     * @param bool   $bubble  Whether the messages that are handled can bubble up the stack or not
     * @param FluentLogger $logger An instance of FluentdLogger
     */
    public function __construct(
        $level = Logger::DEBUG,
        $bubble = true,
        FluentLogger $logger = null
    ) {
        parent::__construct($level, $bubble);
        $this->logger = $logger ? : new FluentLogger();
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
