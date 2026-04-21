<?php

$block = $block ?? [];  // Defaults the block variable to [].- Prevents warnings below.
$preview_image = get_field('preview_image');

if ($preview_image) {
    $src = get_theme_file_uri($preview_image);
    echo "<img src=\"{$src}\" alt=\"{$block['description']}\" style=\"width: 100%; height: 100%; object-fit: contain;\" />";
    return;
}

$blockNameArr = explode('/', $block['name']);
$blockName = end($blockNameArr);

$data = array_merge(
    get_fields() ?: [], // All ACF fields from the block
    [
        // Additional fields from the advanced tab
        'block_name' => $blockName,
        'align' => $block['align'] ?? '',
        'class_name' => $block['className'] ?? '',
        'text_color' => $block['textColor'] ?? null,
        'anchor' => $block['anchor'] ?? $block['id'],
        'background_color' => $block['backgroundColor'] ?? null,
    ]
);


echo view("components.{$blockName}", $data);