<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\CLI';

class CLI
{
    public $caller;

    public function __invoke()
    {
        return $this;
    }

    public function makemigrations($modelFiles = null, $migrationFolder = '', $options = [])
    {
        $pOrm = $this->caller;
        $result = $pOrm->schema()->diffFromModelToMigration($modelFiles, $migrationFolder);

        $commands = $result['commands'];
        $reverseCommands = $result['reverse'];

        if (empty($commands)) {
            return 'No changes detected.';
        }

        $dryRun = !empty($options['dry-run']);
        $migrationCode = join("\n", $commands);
        $reverseCode = join("\n", $reverseCommands);

        if ($dryRun) {
            return "Migrations to generate:\n" . $migrationCode;
        }

        $migrationName = !empty($options['name']) ? $options['name'] : null;
        $pOrm->migration()->writeMigration(
            [
                'MIGRATION_PROCESS' => $migrationCode,
                'MIGRATION_REVERSE' => $reverseCode,
            ],
            $migrationFolder,
            $migrationName
        );

        return "Migration created successfully.\nOperations:\n" . $migrationCode;
    }

    public function migrate($migrationFolder = '', $target = null, $options = [])
    {
        $pOrm = $this->caller;
        $migration = $pOrm->migration();
        $recorder = $migration->getRecorder();
        $allFiles = $migration->getAllFiles([$migrationFolder]);
        $fake = !empty($options['fake']);

        if ($target === 'zero') {
            return $this->_rollbackAll($allFiles, $recorder, $pOrm, $fake);
        }

        $applied = $this->_getAppliedMigrations($recorder);
        $output = [];

        foreach ($allFiles as $f) {
            $migrationName = $this->_getMigrationName($f);
            if (isset($applied[$migrationName])) {
                continue;
            }

            if ($target && $this->_isAfterTarget($migrationName, $target)) {
                break;
            }

            $output[] = "Applying {$migrationName}...";

            if (!$fake) {
                $r = \PMVC\l($f, _INIT_CONFIG);
                $class = \PMVC\importClass($r);
                $obj = new $class();
                $dao = $pOrm->dao()->getDefault();
                $obj->process($dao);
            }

            $this->_recordMigration($recorder, $migrationName);
            $output[] = " OK";
        }

        if (empty($output)) {
            return 'No migrations to apply.';
        }

        return join("\n", $output);
    }

    public function showmigrations($migrationFolder = '', $options = [])
    {
        $pOrm = $this->caller;
        $migration = $pOrm->migration();
        $allFiles = $migration->getAllFiles([$migrationFolder]);
        $recorder = $migration->getRecorder();
        $applied = $this->_getAppliedMigrations($recorder);
        $output = [];

        foreach ($allFiles as $f) {
            $migrationName = $this->_getMigrationName($f);
            $status = isset($applied[$migrationName]) ? 'X' : ' ';
            $output[] = "[{$status}] {$migrationName}";
        }

        if (empty($output)) {
            return 'No migrations found.';
        }

        return join("\n", $output);
    }

    private function _getAppliedMigrations($recorder)
    {
        $applied = [];
        $results = $recorder->getAll()->process();
        if (!empty($results)) {
            foreach ($results as $row) {
                $applied[$row['name']] = true;
            }
        }
        return $applied;
    }

    private function _getMigrationName($filePath)
    {
        return pathinfo($filePath, PATHINFO_FILENAME);
    }

    private function _isAfterTarget($migrationName, $target)
    {
        return strcmp($migrationName, $target) > 0;
    }

    private function _recordMigration($recorder, $migrationName)
    {
        $recorder->create([
            'prefix' => 'orm',
            'name' => $migrationName,
            'applied' => date('Y-m-d H:i:s'),
        ])->process();
    }

    private function _rollbackAll($allFiles, $recorder, $pOrm, $fake)
    {
        $applied = $this->_getAppliedMigrations($recorder);
        $reversedFiles = array_reverse($allFiles);
        $output = [];

        foreach ($reversedFiles as $f) {
            $migrationName = $this->_getMigrationName($f);
            if (!isset($applied[$migrationName])) {
                continue;
            }

            $output[] = "Unapplying {$migrationName}...";

            if (!$fake) {
                $r = \PMVC\l($f, _INIT_CONFIG);
                $class = \PMVC\importClass($r);
                $obj = new $class();
                if (method_exists($obj, 'reverse')) {
                    $dao = $pOrm->dao()->getDefault();
                    $obj->reverse($dao);
                }
            }

            $this->_unrecordMigration($recorder, $migrationName);
            $output[] = " OK";
        }

        if (empty($output)) {
            return 'No migrations to unapply.';
        }

        return join("\n", $output);
    }

    private function _unrecordMigration($recorder, $migrationName)
    {
        $recorder->delete(null)
            ->exact('name', $migrationName)
            ->process();
    }
}
