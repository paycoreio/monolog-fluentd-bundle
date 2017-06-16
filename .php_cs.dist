<?php

$header = <<<'EOF'
This file is part of "musement/monolog-fluentd-bundle".

(c) Musement S.p.A. <oss@musement.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,

        'array_syntax' => [
            'syntax' => 'short',
        ],
        'combine_consecutive_unsets' => true,
        'general_phpdoc_annotation_remove' => [
            'expectedExceptionMessage',
            'expectedExceptionMessageRegExp',
            'test',
        ],
        'header_comment' => ['header' => $header],
        'heredoc_to_nowdoc' => true,
        'linebreak_after_opening_tag' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'protected_to_private' => true,

        '@Symfony:risky' => true,
        'dir_constant' => true,
        'psr4' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
    )
;
