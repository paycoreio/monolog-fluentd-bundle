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

use Fluent\Logger\Entity;
use Fluent\Logger\FluentLogger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Psr\Log\InvalidArgumentException;

class FluentdHandler extends AbstractProcessingHandler
{
    /**
     * Maps Monolog log levels to PSR-3 (syslog) log values.
     *
     * @see https://tools.ietf.org/html/rfc5424
     */
    protected static $psr3Levels = [
        Logger::DEBUG => LOG_DEBUG,
        Logger::INFO => LOG_INFO,
        Logger::NOTICE => LOG_NOTICE,
        Logger::WARNING => LOG_WARNING,
        Logger::ERROR => LOG_ERR,
        Logger::CRITICAL => LOG_CRIT,
        Logger::ALERT => LOG_ALERT,
        Logger::EMERGENCY => LOG_EMERG,
    ];

    /** @var FluentLogger */
    protected $logger;

    /** @var string */
    protected $tagFormat;

    /**
     * Handler constructor.
     *
     * @param int          $level     The minimum logging level at which this handler will be triggered
     * @param bool         $bubble    Whether the messages that are handled can bubble up the stack or not
     * @param FluentLogger $logger    An instance of FluentdLogger
     * @param string       $tagFormat
     */
    public function __construct(
        $level = Logger::DEBUG,
        $bubble = true,
        FluentLogger $logger = null,
        $tagFormat = null
    ) {
        parent::__construct($level, $bubble);
        $this->logger = $logger ?: new FluentLogger();
        $this->tagFormat = $tagFormat ?: '{{channel}}.{{level_name}}';
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
     * Converts Monolog levels to PSR-3 (Syslog) numeric values.
     *
     * @param string|int Level number (monolog)
     * @param mixed $level
     *
     * @return int Psr-3 level number
     */
    public static function toPsr3Level($level)
    {
        if (isset(static::$psr3Levels[$level])) {
            return static::$psr3Levels[$level];
        }

        throw new InvalidArgumentException(sprintf(
            'Level "%s" is not defined, use one among "%s".',
            $level,
            implode('", "', array_keys(static::$psr3Levels))
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     */
    protected function write(array $record)
    {
        unset($record['formatted']);

        $record['level'] = static::toPsr3Level($record['level']);

        $this->logger->post2(new Entity(
            $this->buildTag($record),
            $record,
            $record['datetime']->getTimestamp()
        ));
    }

    /**
     * @param array $record
     *
     * @throws \LogicException
     *
     * @return string
     */
    protected function buildTag(array $record)
    {
        $tag = $this->tagFormat;
        if (!preg_match_all('/\{\{(.*?)\}\}/', $tag, $matches)) {
            return $tag;
        }

        /** @var array[] $matches */
        foreach ($matches[1] as $match) {
            if (isset($record[$match])) {
                $tag = str_replace("{{{$match}}}", $record[$match], $tag);
                continue;
            }

            throw new \LogicException(sprintf('No such field "%s" in the record', $record[$match]));
        }

        return $tag;
    }
}
