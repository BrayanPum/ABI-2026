<?php

return [

    'show_warnings' => true,

    'convert_entities' => true,

    'options' => [
        "font_dir" => storage_path('fonts'),
        "font_cache" => storage_path('fonts'),
        "temp_dir" => sys_get_temp_dir(),
        "chroot" => realpath(base_path()),
        'allowed_protocols' => [
            "file://" => ["rules" => []],
            "http://" => ["rules" => []],
            "https://" => ["rules" => []]
        ],
        'enable_php' => true,
        'enable_remote' => true,
        'enable_css_float' => true,
        'enable_javascript' => true,
        'debug_layout' => true,
        'debug_layout_lines' => true,
        'debug_layout_blocks' => true,
        'debug_layout_inline' => true,
        'debug_layout_padding_box' => true,
        'pdfa' => true,
        'pdf_backend' => "CPDF",
        'default_media_type' => "screen",
        'default_paper_size' => "a4",
        'default_font' => "serif",
        'dpi' => 96,
        'font_height_ratio' => 1.1,
        'is_html5_parser_enabled' => true,
        'is_font_subsetting_enabled' => true,
        'debug_keep_temp' => true,
        'debug_css' => false,
        'debug_text' => true,
        'log_output_file' => null,
    ],

];
