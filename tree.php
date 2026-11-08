<?php

declare(strict_types=1);

function buildDirectoryTree(string $dir, string $prefix = ''): string
{
    if (!is_dir($dir)) {
        return "Directory does not exist: {$dir}\n";
    }

    $output = '';
    // Ignore dot-directories, vendor packages, git objects, and generated files
    $ignoreList = ['.', '..', '.git', 'vendor', '.vscode', 'folder_tree.txt'];
    $rawItems = scandir($dir);
    if ($rawItems === false) return '';

    $items = array_diff($rawItems, $ignoreList);

    usort($items, function ($a, $b) use ($dir) {
        $pathA = $dir . DIRECTORY_SEPARATOR . $a;
        $pathB = $dir . DIRECTORY_SEPARATOR . $b;
        if (is_dir($pathA) === is_dir($pathB)) {
            return strnatcasecmp($a, $b);
        }
        return is_dir($pathA) ? -1 : 1;
    });

    $items = array_values($items);
    $totalItems = count($items);

    foreach ($items as $index => $item) {
        $isLast = ($index === $totalItems - 1);
        $path = $dir . DIRECTORY_SEPARATOR . $item;

        $branch = $isLast ? '└── ' : '├── ';
        $output .= $prefix . $branch . $item . "\n";

        if (is_dir($path)) {
            $nextPrefix = $prefix . ($isLast ? '    ' : '│   ');
            $output .= buildDirectoryTree($path, $nextPrefix);
        }
    }

    return $output;
}

$targetDirectory = __DIR__;
$outputFilePath  = $targetDirectory . DIRECTORY_SEPARATOR . 'folder_tree.txt';

$treeResult = basename(realpath($targetDirectory) ?: $targetDirectory) . "\n│\n";
$treeResult .= buildDirectoryTree($targetDirectory);

file_put_contents($outputFilePath, $treeResult);

echo "Tree generated successfully: {$outputFilePath}\n";
