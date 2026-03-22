<?php

namespace PMVC\PlugIn\orm;

use PMVC\PlugIn\orm\crud\WhereTrait;
use PMVC\PlugIn\orm\crud\Create;
use PMVC\PlugIn\orm\crud\Read;
use PMVC\PlugIn\orm\crud\Update;
use PMVC\PlugIn\orm\crud\Delete;
use PMVC\PlugIn\orm\Attrs\Table;

class BaseSqlModel
{
    private $_tableSchema;

    public function create($data): Create
    {
        return new Create($this, '', ['data' => $data]);
    }

    public function getAll(): Read
    {
        return new Read($this, 'all');
    }

    public function getOne(): Read
    {
        return new Read($this, 'one');
    }

    public function getVar(string $key): Read
    {
        return new Read($this, 'var', ['key' => $key]);
    }

    public function update($data): Update
    {
      return new Update($this, '', [
        'data' => $data
      ]);
    }

    public function delete($id): Delete
    {
      return new Delete($this, '', [
        'id' => $id
      ]);
    }

    /**
     * Django-style filter shortcut — returns a chainable Read query.
     * e.g. $model->filter('status', 'active')->orderBy('-created_at')->limit(10)->process()
     */
    public function filter(string $fieldOrLookup, $value): Read
    {
        return (new Read($this, 'all'))->filter($fieldOrLookup, $value);
    }

    /**
     * Multi-row INSERT in batches.
     */
    public function bulkCreate(array $objects, int $batchSize = 100): void
    {
        $cols = $this->getColumnKeys();
        foreach (array_chunk($objects, $batchSize) as $batch) {
            $oSql = \PMVC\plug('orm')->sql();
            $valueGroups = [];
            foreach ($batch as $data) {
                $placeholders = [];
                foreach ($cols as $col) {
                    $placeholders[] = $oSql->getBindName($data[$col] ?? null, $col);
                }
                $valueGroups[] = '(' . implode(', ', $placeholders) . ')';
            }
            $sql = \PMVC\plug('orm')->useTpl('bulkInsert', [
                'TABLE' => $this->getTableName(),
                'FIELD_KEYS' => implode(', ', $cols),
                'FIELD_VALUES' => implode(', ', $valueGroups),
            ]);
            $oSql->set($sql)->process('exec');
        }
    }

    /**
     * Batched UPDATE for a list of objects. Each object must have an 'id'.
     * Only $fields columns are updated.
     */
    public function bulkUpdate(array $objects, array $fields, int $batchSize = 100): void
    {
        foreach (array_chunk($objects, $batchSize) as $batch) {
            foreach ($batch as $data) {
                $filtered = array_intersect_key($data, array_flip($fields));
                if (!empty($filtered) && isset($data['id'])) {
                    $this->update($filtered)->exact('id', $data['id'])->process();
                }
            }
        }
    }

    public function getSchema(): Table
    {
        if (!$this->_tableSchema) {
            $pOrm = \PMVC\plug('orm');
            $this->_tableSchema = $pOrm
                ->schema()
                ->fromOneModel($pOrm->parse_model()->fromClass($this));
        }
        return $this->_tableSchema;
    }

    public function getColumns(): array
    {
        $tableSchema = $this->getSchema();
        \PMVC\d($tableSchema['TABLE_COLUMNS']);
        return [];
    }

    public function getColumnKeys(): array
    {
        $tableSchema = $this->getSchema();
        return array_keys($tableSchema['TABLE_COLUMNS']);
    }

    public function getTableName(): string
    {
        $tableSchema = $this->getSchema();
        return $tableSchema['TABLE_NAME'];
    }

    public function getSchemaSql(): string
    {
        $tableSchema = $this->getSchema();
        return (string) $tableSchema;
    }

    public function getSchemaArray(): array
    {
        $tableSchema = $this->getSchema();
        return $tableSchema->toArray();
    }
}





