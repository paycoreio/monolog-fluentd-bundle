# musement/monolog-fluentd-bundle

This Symfony bundle enables logging to **fluentd** via monolog.

Fluentd is an open source data collector, it decouples data sources from backend systems by providing a unified logging layer in between.

## Install

    composer require musement/monolog-fluentd-bundle

## Register the bundle in Symfony

    <?php
    // AppKernel.php

    use Symfony\Component\HttpKernel\Kernel;
    use Symfony\Component\Config\Loader\LoaderInterface;

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                ...
                new Musement\MonologFluentdBundle\MusementMonologFluentdBundle(),
            );
            ...
        }
    }

## Configuration

These are the default parameters:

    musement_monolog_fluentd:
      host: localhost
      port: 24224
      options: []
      level: debug
      tag_fmt: '{{channel}}.{{level_name}}'

You can modify them in config.yml or parameters.yml

You may load the handler as a service

    monolog:
      handlers:
        musement_monolog_fluentd:
          type: service
          id: musement_monolog_fluentd.monolog.handler

## How to run the tests

    phpunit -c phpunit.xml.dist

## Copyright

© Musement S.p.A.

