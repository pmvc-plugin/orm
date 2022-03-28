```
/**
 * mysql https://dev.mysql.com/doc/refman/8.0/en/data-types.html
 *   https://wtools.io/generate-sql-create-table
 * sqlite https://www.sqlite.org/datatype3.html
 *   https://www.sqlite.org/syntax/column-constraint.html
 * pgsql https://www.postgresql.org/docs/current/datatype.html
 * django https://docs.djangoproject.com/en/3.2/ref/models/fields/#field-types
 *    backend example https://github.com/django/django/tree/main/django/db/backends
 *    field list: https://github.com/django/django/blob/main/django/db/models/fields/__init__.py#L34-L67
 */
class ColumnType extends ListIterator
{
    protected function getInitialState()
    {
        return [
            STRINGS => [
                BASE => [
                    'char' => 'CHAR',
                    'varchar' => 'VARCHAR',
                    'text' => 'TEXT',
                ],
                MYSQL => [
                    'tinytext' => 'TINYTEXT',
                    'mediumtext' => 'MEDIUMTEXT',
                    'longtext' => 'LONGTEXT',
                    'binary' => 'BINARY',
                    'varbinary' => 'VARBINARY',
                ],
                PGSQL => [
                    'character' => 'CHARACTER',
                    'character_varying' => 'CHARACTER VARYING',
                ],
                SQLITE => [
                    'tinytext' => 'TINYTEXT',
                    'mediumtext' => 'MEDIUMTEXT',
                    'longtext' => 'LONGTEXT',
                    'nchar' => 'NCHAR',
                    'nvarchar' => 'NVARCHAR',
                    'clob' => 'CLOB',
                ],
            ],
            NUMBERS => [
                BASE => [
                    'int' => 'INT',
                    'bigint' => 'BIGINT',
                    'integer' => 'INTEGER',
                    'smallint' => 'SMALLINT',
                    'boolean' => 'BOOLEAN',
                    'real' => 'REAL',
                    'double_precision' => 'DOUBLE PRECISION',
                ],
                MYSQL => [
                    'bit' => 'BIT',
                    'tinyint' => 'TINYINT',
                    'mediumint' => 'MEDIUMINT',
                    'double' => 'DOUBLE',
                    'float' => 'FLOAT',
                    'bool' => 'BOOL',
                ],
                PGSQL => [
                    'bit' => 'BIT',
                    'varbit' => 'VARBIT',
                    'bit_varying' => 'BIT VARYING',
                    'smallserial' => 'SMALLSERIAL',
                    'serial' => 'SERIAL',
                    'bigserial' => 'BIGSERIAL',
                    'money' => 'MONEY',
                    'bool' => 'BOOL',
                ],
                SQLITE => [
                    'tinyint' => 'TINYINT',
                    'mediumint' => 'MEDIUMINT',
                    'int2' => 'INT2',
                    'int4' => 'INT4',
                    'int8' => 'INT8',
                    'double' => 'DOUBLE',
                    'float' => 'FLOAT',
                ],
            ],
            NUMERICS => [
                BASE => [
                    'numeric' => 'NUMERIC',
                    'decimal' => 'DECIMAL',
                ],
                MYSQL => [
                    'identity' => 'IDENTITY',
                    'dec' => 'DEC',
                    'fixed' => 'FIXED',
                ],
                PGSQL => [],
                SQLITE => [],
            ],
            DATE_TIME => [
                BASE => [
                    'date' => 'DATE',
                    'timestamp' => 'TIMESTAMP',
                    'time' => 'TIME',
                ],
                MYSQL => [
                    'datetime' => 'DATETIME',
                    'year' => 'YEAR',
                ],
                PGSQL => [
                    'timestamp_without_time_zone' =>
                        'TIMESTAMP WITHOUT TIME ZONE',
                    'timestamp_with_time_zone' => 'TIMESTAMP WITH TIME ZONE',
                    'time_without_time_zone' => 'TIME WITHOUT TIME ZONE',
                    'time_with_time_zone' => 'TIME WITH TIME ZONE',
                ],
                SQLITE => ['datetime' => 'DATETIME'],
            ],
            OBJECTS => [
                BASE => [],
                MYSQL => [
                    'tinyblob' => 'TINYBLOB',
                    'blob' => 'BLOB',
                    'mediumblob' => 'MEDIUMBLOB',
                    'longtext' => 'LONGTEXT',
                ],
                PGSQL => [],
                SQLITE => [
                    'blob' => 'BLOB',
                ],
            ],
            AUTO_NUMBERS => [
                MYSQL => 'AUTO_INCREMENT',
                PGSQL => 'SERIAL',
                SQLITE => 'AUTOINCREMENT',
            ],
        ];
    }
}
```
