<?php

return [
    // Global schedule for automatic fetching
    'schedule' => [
        // Supported: hourly, daily, everyThirtyMinutes, etc.
        'frequency' => 'hourly',
    ],

    // Platform-specific defaults and tasks
    'eporner' => [
        'defaults' => [
            'order' => 'latest',
            'per_page' => 50,
            'max_pages' => 1,
            'period' => 'month',
        ],
        // When no CLI params are provided, the command will iterate these tasks
        'tasks' => [
            [
                'query' => 'all', //teens or anal milf
                'order' => 'latest',
                'per_page' => 50,
                'max_pages' => 1,
            ],
            [
                'query' => 'all', 
                'order' => 'longest',
                'per_page' => 50,
                'max_pages' => 1,
            ],
            [
                'query' => 'all', 
                'order' => 'shortest',
                'per_page' => 50,
                'max_pages' => 1,
            ],
            [
                'query' => 'all', 
                'order' => 'top-rated',
                'per_page' => 50,
                'max_pages' => 1,
            ],
            [
                'query' => 'all', 
                'order' => 'most-popular',
                'per_page' => 50,
                'max_pages' => 1,
            ],
            [
                'query' => 'all', 
                'order' => 'top-weekly',
                'per_page' => 50,
                'max_pages' => 1,
            ],
            [
                'query' => 'all', 
                'order' => 'top-monthly',
                'per_page' => 50,
                'max_pages' => 1,
            ],
        ],
    ],
];
