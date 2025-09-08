<?php // config/flasher.php

return [
  'plugins' => [
    'notyf' => [
      'scripts' => [
        '/vendor/flasher/flasher-notyf.min.js',
      ],
      'styles' => [
        '/vendor/flasher/flasher-notyf.min.css',
      ],
      'options' => [
        // Optional: Add global options here
        'duration' => 3000,
        'position' => [
          'x' => 'right',
          'y' => 'top'
        ]
        // 'dismissible' => true,
      ],
    ],
  ],
];
