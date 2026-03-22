# PMVC ORM Plugin

## Architecture Overview

This is a PMVC framework ORM plugin (`PMVC\PlugIn\orm`) supporting SQLite, MySQL, and PostgreSQL.

## Visitor Pattern

The ORM uses the **Visitor pattern** as its core compilation mechanism. Understanding this is essential before adding or modifying any Behavior.

### Key Components

- **Visitor (Element):** `Engine` and its subclasses (`PgsqlEngine`, `MysqlEngine`, `SqliteEngine`) — these are the "visitors" that accept Behavior objects and apply engine-specific logic.
- **Visitable (Concrete Element):** Classes implementing `Interfaces\Behavior` in `src/Behaviors/` — these are the "elements" being visited.
- **Compiler:** `BehaviorAction::compile()` in `src/_behavior.php` — orchestrates the visitor traversal.

### How It Works

1. **`BehaviorAction`** (in `src/_behavior.php`) acts as the compiler. Its public methods (`tableToSql`, `tableToArray`, `buildDsn`, etc.) create arrays of `Behavior` objects and pass them to `compile()`.

2. **`compile()`** iterates over each Behavior, calling:
   - `$behavior->accept($engine)` — the Behavior hands itself to the Engine (visitor), which may mutate the Behavior with engine-specific data (e.g., column types, DSN format), then returns the Behavior.
   - `$behavior->process()` — the Behavior produces its output using the data the Engine injected.

3. **`Interfaces\Behavior`** requires two methods:
   ```php
   public function accept(Engine $engine);  // double-dispatch to engine
   public function process();               // produce result after engine visit
   ```

4. **`Engine`** base class defines visitor methods that Behaviors dispatch to:
   - `buildCreateTable(Behavior)`
   - `buildColumn(Behavior)`
   - `buildDsn(Behavior)`
   - `getColumnType(Behavior)`
   - `checkTableExists(Behavior)`

   Subclasses (e.g., `PgsqlEngine`) override these to inject DB-specific behavior.

### Adding a New Behavior

1. Create a class in `src/Behaviors/` implementing `Interfaces\Behavior`.
2. In `accept()`, call the appropriate `Engine` method (existing or new), passing `$this`.
3. In `process()`, return the final result using any data the Engine injected.
4. If a new Engine method is needed, add it to `Engine` base class and override in each engine subclass.
5. Add a public method in `BehaviorAction` (`src/_behavior.php`) that creates and compiles your Behavior.

### Example Flow: `tableToSql`

```
BehaviorAction::tableToSql($table)
  → compile([BuildColumnSql, BuildTableSql])
    → BuildColumnSql->accept($engine)     // Engine injects transform rules
    → BuildColumnSql->process()           // Builds column SQL strings
    → BuildTableSql->accept($engine)      // Engine confirms table creation
    → BuildTableSql->process()            // Uses template to build CREATE TABLE
```

## Project Structure

- `orm.php` — Plugin entry point, engine factory, template loader
- `src/Engine.php` — Base visitor class (default engine)
- `src/_behavior.php` — `BehaviorAction` compiler (orchestrates Visitor pattern)
- `src/Interfaces/Behavior.php` — Visitable interface (`accept` + `process`)
- `src/Behaviors/` — Concrete Behavior implementations
- `src/_engine_pgsql.php`, `_engine_mysql.php`, `_engine_sqlite.php` — DB-specific engine subclasses
- `src/Attrs/` — PHP attributes for Table, Column, Field, Relation
- `src/Fields/` — Field type definitions (CharField, IntegerField, etc.) — **capital F required for PSR-4**
- `src/crud/` — CRUD operations (Create, Read, Update, Delete) via `BaseSqlModel`
- `src/DAO.php` — Migration operations (queue + process SQL via PDO)
- `src/StructureDAO.php` — Structure-only DAO for schema diffing (no DB writes)
- `src/BaseSqlModel.php` — Model base class for CRUD and schema access
- `src/BindTrait.php` — PDO parameter binding
- `src/Tables.php` — Table collection
- `src/_pdo.php` — PDO wrapper with connection management
- `src/_sql.php` — Raw SQL builder (`RawSql`) with bind parameter support
- `src/_schema.php` — Schema diffing and migration generation
- `src/_migration.php` — Migration processing and `MigrationRecorder` model
- `src/_remote.php` — Remote DB operations (create table, check exists)
- `src/_parse_model.php` — Model attribute parsing via reflection
- `src/_get_serial_number.php` — Migration serial number generator
