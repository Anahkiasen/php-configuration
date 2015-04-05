<?php

return [

    // The main configuration of your application
    'foobar' => [

        // foo
        'foo' =>             'foo', // Required

        // bar
        // bar
        'bar' => [
            // Examples:
            // 'bar',
            // 'bar',
        ],

        // baz
        'baz' =>             function ($foobar) {
                             return $foobar + 3;
                         },

        // qux
        'qux' => [
            // Defaults:
            'qux',
            'qux',
        ],
    ],

];