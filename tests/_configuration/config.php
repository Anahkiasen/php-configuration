<?php

return [
    // The main configuration of your application
    'foobar' => [

        // foo
        'foo' => 'foo', // Required

        // bar
        // bar
        'bar' => [
            // Examples:
            // 'bar',
            // 'bar',
        ],

        // baz
        'baz' => function ($foobar) {
            foreach (['foo', 'bar'] as $foo) {
                return $foobar + $foo;
            }
        },

        // qux
        'qux' => [
            // Defaults:
            'qux',
            'qux',
        ],
    ],

];
