<?php

/**
 * Create symlinks for Barnomala Client.
 * Run this once, then delete for security.
 */

$parentDir = dirname(__DIR__);
$target = $parentDir . '/laravel_app/storage/app/public';
$link = $parentDir . '/public_html/storage';

if (file_exists($link)) {
    echo "⚠️ Skipping: Storage Symlink Already Exists.";
} else if (symlink($target, $link)) {
    echo "✅ Storage Symlink Successfully Created.";
} else {
    echo "❌ Failed to Create Storage Symlink.";
}
