<?php

return [
    'debug' => true,

    'Security' => [
        'salt' => 'a5f3c6a11b03839d46af9fb43c97c188'
    ],

    'Datasources' => [
        'default' => [
            'driver' => 'Cake\\Database\\Driver\\Sqlite',
            'database' => 'tmp/training_sessions.sqlite',
            'encoding' => 'utf8',
            'url' => env('DATABASE_URL', null),
        ],

        'test' => [
            'driver' => 'Cake\\Database\\Driver\\Sqlite',
            'database' => ':memory:',
            'encoding' => 'utf8',
            'url' => env('DATABASE_TEST_URL', null),
        ],
    ],
];
