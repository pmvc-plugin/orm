<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\Diff';

const left = 'left';
const right = 'right';
const both = 'both';

class Diff
{
    public function __invoke()
    {
        return $this;
    }

    public function diffAll($left, $right)
    {
        $tableDiffs = $this->diffKey($left, $right);
        $colDiffs = [];
        if (!empty($tableDiffs[both])) {
            $colDiffs = $this->_diffTableOption($tableDiffs[both], 'OPTION_COLS');
        }
        return [
            'tables' => $tableDiffs,
            'columns' => $colDiffs,
        ];
    }

    private function _diffTableOption($compareArr, $compareKey)
    {
        $result = [];
        if (!empty($compareArr)) {
            foreach ($compareArr as $tKey => $tVal) {
                $result[$tKey] = $this->diffKey(
                    $tVal[left][$compareKey],
                    $tVal[right][$compareKey]
                );
                if (!empty($result[$tKey][both])) {
                    $this->_diffColValue($result[$tKey]);
                }
            }
        }
        return $result;
    }

    private function _diffColValue(&$cols)
    {
        $both = $cols[both];
        if (!empty($both)) {
            foreach ($both as $colK => $colV) {
                $diff = $this->diffValue($colV[left], $colV[right]);
                if (!empty($diff['change'])) {
                    $cols['change'][$colK] = $colV;
                }
            }
        }
    }

    public function diffValue($left, $right)
    {
        $same = [];
        $change = [];
        foreach ($left as $k => $v) {
            if ($v === $right[$k]) {
                $same[$k] = [
                    left => $v,
                    right => $right[$k],
                ];
            } else {
                $change[$k] = [
                    left => $v,
                    right => $right[$k],
                ];
            }
        }
        return compact('same', 'change');
    }

    public function diffKey($left, $right)
    {
        $both = [];
        $diff = [
            left => [],
        ];
        foreach ($left as $tKey => $tVal) {
            if (isset($right[$tKey])) {
                $both[$tKey] = [
                    left => $tVal,
                    right => $right[$tKey],
                ];
                $right[$tKey] = null;
                unset($right[$tKey]);
            } else {
                $diff[left][$tKey] = $tVal;
            }
        }
        if (!empty($right)) {
            $diff[right] = $right;
        }
        return compact(both, 'diff');
    }
}
