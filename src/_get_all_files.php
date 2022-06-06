<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\GetAllFiles';

class GetAllFiles
{
    public function __invoke($payload, $filePattern = '*.php')
    {
        if (!is_array($payload)) {
            $payload = [$payload];
        }
        return $this->_getFiles($payload, $filePattern);
    }

    private function _getFiles($payload, $filePattern)
    {
        $files = [];
        foreach ($payload as $p) {
            if (is_dir($p)) {
                $gFiles = glob(\PMVC\lastSlash($p) . $filePattern);
                $files = array_merge(
                    $files,
                    $this->_getFiles($gFiles, $filePattern)
                );
            } elseif (is_file($p)) {
                $files[] = $p;
            }
        }
        return $files;
    }
}
