<?php

namespace PMVC\PlugIn\orm\crud;

trait WhereTrait
{
    private $_where = [];
    private $_whereType = 'AND';

    protected function setWhere($op, $col, $val, $negate = false)
    {
        $this->_where[] = [$op, $col, $val, $negate];
    }

    public function exact($col, $val)
    {
        $this->setWhere('exact', $col, $val);
        return $this;
    }

    public function iexact($col, $val)
    {
        $this->setWhere('iexact', $col, $val);
        return $this;
    }

    public function contains($col, $val)
    {
        $this->setWhere('contains', $col, $val);
        return $this;
    }

    public function icontains($col, $val)
    {
        $this->setWhere('icontains', $col, $val);
        return $this;
    }

    public function regex($col, $val)
    {
        $this->setWhere('regex', $col, $val);
        return $this;
    }

    public function iregex($col, $val)
    {
        $this->setWhere('iregex', $col, $val);
        return $this;
    }

    public function gt($col, $val)
    {
        $this->setWhere('gt', $col, $val);
        return $this;
    }

    public function gte($col, $val)
    {
        $this->setWhere('gte', $col, $val);
        return $this;
    }

    public function lt($col, $val)
    {
        $this->setWhere('lt', $col, $val);
        return $this;
    }

    public function lte($col, $val)
    {
        $this->setWhere('lte', $col, $val);
        return $this;
    }

    public function startswith($col, $val)
    {
        $this->setWhere('startswith', $col, $val);
        return $this;
    }

    public function istartswith($col, $val)
    {
        $this->setWhere('istartswith', $col, $val);
        return $this;
    }

    public function endswith($col, $val)
    {
        $this->setWhere('endswith', $col, $val);
        return $this;
    }

    public function iendswith($col, $val)
    {
        $this->setWhere('iendswith', $col, $val);
        return $this;
    }

    public function in($col, array $vals)
    {
        $this->setWhere('in', $col, $vals);
        return $this;
    }

    public function range($col, $min, $max)
    {
        $this->setWhere('range', $col, [$min, $max]);
        return $this;
    }

    public function isnull($col, bool $val = true)
    {
        $this->setWhere('isnull', $col, $val);
        return $this;
    }

    /**
     * Django-style filter: filter('field', value) or filter('field__lookup', value)
     * Backward compat: filter('and'/'or') sets the WHERE join type
     */
    public function filter($fieldOrOp, $value = null)
    {
        if ($value === null) {
            $this->_whereType = strtoupper($fieldOrOp);
            return $this;
        }
        if (strpos($fieldOrOp, '__') !== false) {
            [$col, $op] = explode('__', $fieldOrOp, 2);
        } else {
            $col = $fieldOrOp;
            $op = 'exact';
        }
        $this->setWhere($op, $col, $value);
        return $this;
    }

    /**
     * Negated filter — generates NOT (condition)
     */
    public function exclude($fieldOrLookup, $value = null)
    {
        if (strpos($fieldOrLookup, '__') !== false) {
            [$col, $op] = explode('__', $fieldOrLookup, 2);
        } else {
            $col = $fieldOrLookup;
            $op = 'exact';
        }
        $this->setWhere($op, $col, $value, true);
        return $this;
    }

    public function setMultiFilter($opList)
    {
        return $this;
    }

    private function _buildCondition($oSql, string $op, string $col, $val): string
    {
        switch ($op) {
            case 'exact':
                $k = $oSql->getBindName($val, $col);
                return "$col = $k";

            case 'iexact':
                $k = $oSql->getBindName(strtolower($val), $col);
                return "LOWER($col) = $k";

            case 'contains':
                $k = $oSql->getBindName('%' . $val . '%', $col);
                return "$col LIKE $k";

            case 'icontains':
                $k = $oSql->getBindName('%' . strtolower($val) . '%', $col);
                return "LOWER($col) LIKE $k";

            case 'startswith':
                $k = $oSql->getBindName($val . '%', $col);
                return "$col LIKE $k";

            case 'istartswith':
                $k = $oSql->getBindName(strtolower($val) . '%', $col);
                return "LOWER($col) LIKE $k";

            case 'endswith':
                $k = $oSql->getBindName('%' . $val, $col);
                return "$col LIKE $k";

            case 'iendswith':
                $k = $oSql->getBindName('%' . strtolower($val), $col);
                return "LOWER($col) LIKE $k";

            case 'gt':
                $k = $oSql->getBindName($val, $col);
                return "$col > $k";

            case 'gte':
                $k = $oSql->getBindName($val, $col);
                return "$col >= $k";

            case 'lt':
                $k = $oSql->getBindName($val, $col);
                return "$col < $k";

            case 'lte':
                $k = $oSql->getBindName($val, $col);
                return "$col <= $k";

            case 'regex':
                $k = $oSql->getBindName($val, $col);
                return \PMVC\plug('orm')->behavior()->getEngine()->getRegexSql($col, $k, false);

            case 'iregex':
                $k = $oSql->getBindName($val, $col);
                return \PMVC\plug('orm')->behavior()->getEngine()->getRegexSql($col, $k, true);

            case 'in':
                $vals = (array) $val;
                if (empty($vals)) {
                    return '1=0'; // always false — empty IN set matches nothing
                }
                $placeholders = [];
                foreach ($vals as $v) {
                    $placeholders[] = $oSql->getBindName($v, $col);
                }
                return "$col IN (" . implode(', ', $placeholders) . ")";

            case 'range':
                $kMin = $oSql->getBindName($val[0], $col . '_min');
                $kMax = $oSql->getBindName($val[1], $col . '_max');
                return "$col BETWEEN $kMin AND $kMax";

            case 'isnull':
                return $val ? "$col IS NULL" : "$col IS NOT NULL";

            default:
                $k = $oSql->getBindName($val, $col);
                return "$col = $k";
        }
    }

    public function getWhere($oSql): string
    {
        if (empty($this->_where)) {
            return '';
        }
        $glue = ' ' . $this->_whereType . ' ';
        $resultArr = [];
        foreach ($this->_where as $w) {
            [$op, $col, $val, $negate] = array_pad($w, 4, false);
            $cond = $this->_buildCondition($oSql, $op, $col, $val);
            $resultArr[] = $negate ? "NOT ($cond)" : $cond;
        }
        return 'WHERE ' . implode($glue, $resultArr);
    }
}
