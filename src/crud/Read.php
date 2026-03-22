<?php

namespace PMVC\PlugIn\orm\crud;

use PMVC\PlugIn\orm\crud\Result;
use PMVC\PlugIn\orm\BaseSqlModel;

class Read extends Result
{
    use WhereTrait;

    private $_orderBy = '';
    private $_groupBy = '';
    private $_having = '';
    private $_limit = '';
    private $_offset = '';
    private $_distinct = false;

    public function __construct(
        BaseSqlModel $model,
        string $type,
        array|null $options = null
    ) {
        parent::__construct($model, $type, $options);
        if ('var' === $type) {
            $this->_fields = $options['key'];
        }
    }

    public function orderBy(string $field, string $direction = 'ASC'): static
    {
        if (str_starts_with($field, '-')) {
            $field = substr($field, 1);
            $direction = 'DESC';
        }
        $this->_orderBy = 'ORDER BY ' . $field . ' ' . strtoupper($direction);
        return $this;
    }

    public function groupBy(string $field): static
    {
        $this->_groupBy = "GROUP BY $field";
        return $this;
    }

    public function having(string $condition): static
    {
        $this->_having = "HAVING $condition";
        return $this;
    }

    public function limit(int $n): static
    {
        $this->_limit = "LIMIT $n";
        return $this;
    }

    public function offset(int $n): static
    {
        $this->_offset = "OFFSET $n";
        return $this;
    }

    public function distinct(): static
    {
        $this->_distinct = true;
        return $this;
    }

    public function values(array $fields): static
    {
        $this->_fields = implode(', ', $fields);
        return $this;
    }

    public function valuesList(array $fields, bool $flat = false): static
    {
        $this->_fields = implode(', ', $fields);
        $this->_options['valuesList'] = true;
        $this->_options['flat'] = $flat;
        return $this;
    }

    public function count(): int
    {
        $m = $this->_model;
        $oSql = \PMVC\plug('orm')->sql();
        $sql = \PMVC\plug('orm')->useTpl('selectQuery', [
            'DISTINCT' => '',
            'FIELD' => 'COUNT(*)',
            'TABLE' => $m->getTableName(),
            'WHERE' => $this->getWhere($oSql),
            'GROUP_BY' => '',
            'HAVING' => '',
            'ORDER_BY' => '',
            'LIMIT' => '',
            'OFFSET' => '',
        ]);
        $result = $oSql->set($sql)->process('one');
        return (int) array_values($result)[0];
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    public function first(): mixed
    {
        $this->_limit = 'LIMIT 1';
        if (!$this->_orderBy) {
            $this->_orderBy = 'ORDER BY id ASC';
        }
        $this->_type = 'one';
        return $this->process();
    }

    public function last(): mixed
    {
        $this->_limit = 'LIMIT 1';
        if (!$this->_orderBy) {
            $this->_orderBy = 'ORDER BY id DESC';
        }
        $this->_type = 'one';
        return $this->process();
    }

    /**
     * Single-record retrieval — throws if 0 or 2+ results.
     */
    public function get(): mixed
    {
        $results = $this->process();
        if (empty($results)) {
            throw new \RuntimeException('No record found matching the query.');
        }
        if (count($results) > 1) {
            throw new \RuntimeException('Multiple records found; expected exactly one.');
        }
        return $results[0];
    }

    /**
     * https://www.w3schools.com/sql/sql_select.asp
     *
     * SELECT
     * https://www.sqlite.org/lang_select.html
     */
    public function process()
    {
        $m = $this->_model;
        $oSql = \PMVC\plug('orm')->sql();
        $sql = \PMVC\plug('orm')->useTpl('selectQuery', [
            'DISTINCT' => $this->_distinct ? 'DISTINCT ' : '',
            'FIELD' => $this->_fields,
            'TABLE' => $m->getTableName(),
            'WHERE' => $this->getWhere($oSql),
            'GROUP_BY' => $this->_groupBy,
            'HAVING' => $this->_having,
            'ORDER_BY' => $this->_orderBy,
            'LIMIT' => $this->_limit,
            'OFFSET' => $this->_offset,
        ]);

        $execType = 'var' === $this->_type ? 'one' : $this->_type;
        $result = $oSql->set($sql)->process($execType);

        if ('var' === $this->_type) {
            return \PMVC\get($result, $this->_fields);
        }

        if (!empty($this->_options['valuesList'])) {
            $fields = array_map('trim', explode(', ', $this->_fields));
            if ($this->_options['flat'] && count($fields) === 1) {
                return array_column($result, $fields[0]);
            }
            return array_map(
                fn($row) => array_intersect_key($row, array_flip($fields)),
                $result
            );
        }

        return $result;
    }
}
