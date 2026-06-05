<?php

return [
    'directories' => [
        base_path('app' . DIRECTORY_SEPARATOR . 'Models'),
    ],
    'ignore' => [],
    'whitelist' => [],
    'aliases' => [],
    'recursive' => true,
    'use_db_schema' => false,
    'use_column_types' => false,
    'ignore_columns' => [],
    'table' => [
        'header_background_color' => '#d3d3d3',
        'header_font_color' => '#333333',
        'row_background_color' => '#ffffff',
        'row_font_color' => '#333333',
    ],
    'graph' => [
        'style' => 'filled',
        'bgcolor' => '#F7F7F7',
        'fontsize' => 12,
        'labelloc' => 't',
        'concentrate' => true,
        'splines' => 'polyline',
        'overlap' => false,
        'nodesep' => 1,
        'rankdir' => 'LR',
        'pad' => 0.5,
        'ranksep' => 2,
        'esep' => true,
        'fontname' => 'Helvetica Neue',
    ],
    'node' => [
        'margin' => 0,
        'shape' => 'rectangle',
        'fontname' => 'Helvetica Neue',
    ],
    'edge' => [
        'color' => '#003049',
        'penwidth' => 1.8,
        'fontname' => 'Helvetica Neue',
    ],
    'relations' => [
        'HasOne' => [
            'dir' => 'both',
            'color' => '#D62828',
            'arrowhead' => 'tee',
            'arrowtail' => 'none',
        ],
        'BelongsTo' => [
            'dir' => 'both',
            'color' => '#F77F00',
            'arrowhead' => 'tee',
            'arrowtail' => 'crow',
        ],
        'HasMany' => [
            'dir' => 'both',
            'color' => '#FCBF49',
            'arrowhead' => 'crow',
            'arrowtail' => 'none',
        ],
    ],
];
