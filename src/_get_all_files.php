<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\GetAllFiles';

class GetAllFiles
{
    public function __invoke($payload, $filePattern = '*.php')
    {
        $files = [];
        if (!is_array($payload)) {
            $payload = [$payload];
        }
        foreach ($payload as $p) {
            if (is_dir($p)) {
                $gFiles = glob(\PMVC\lastSlash($p) . $filePattern);
                $files = array_merge($files, $gFiles);
            }
        }
        return $files;
    }
}
