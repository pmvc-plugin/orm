# Simple Product Requirements Document
# PMVC ORM — Django Feature Parity Implementation Plan

**Product:** PMVC ORM Plugin (`PMVC\PlugIn\orm`)
**Date:** 2026-03-22
**Version:** 1.0
**Author:** SARAH — Product Owner & Strategic Operations Specialist

---

## Problem Statement

### Problem Definition

The PMVC ORM currently implements a solid foundation — Visitor pattern compilation, PHP 8 attributes for schema definition, 32+ field types, basic CRUD, schema diffing, and a migration infrastructure. However, compared to Django ORM (the industry benchmark for full-featured ORMs), **critical query-building, relationship, and operational features are missing or only partially implemented**. This limits developers to basic INSERT/SELECT/UPDATE/DELETE with only `exact` WHERE filtering, no JOIN support, no aggregation, no transactions, and incomplete CLI tooling.

**Quantified Impact:**
- WHERE clause generation works only for `exact` operator — 14 other defined lookup methods (`contains`, `gt`, `lt`, `startswith`, `regex`, etc.) do not produce SQL
- ORDER BY, GROUP BY, LIMIT/OFFSET templates exist but are never populated
- Zero relationship traversal (no auto-JOINs, no eager/lazy loading)
- Zero transaction management (no `begin`/`commit`/`rollback`)
- CLI commands (`makemigrations`, `migrate`, `showmigrations`) are stubs

### Target Users

PHP developers using the PMVC framework who need a Django-style ORM with attribute-based schema definition, automatic migrations, and a fluent query API — without switching to Eloquent or Doctrine.

### Business Value

Achieving Django feature parity makes PMVC ORM a competitive, self-contained ORM for the PMVC ecosystem, reducing dependency on external packages and enabling developers to handle complex queries, relationships, and migrations natively.

---

## Solution Overview

### Solution Summary

A phased implementation plan that builds Django ORM capabilities into the existing PMVC Visitor pattern architecture. Each phase delivers usable, testable features that compound — starting with the query builder (highest daily-use impact), then relationships, then advanced features.

### Key Features (Gap Analysis — What's Missing)

The following 7 feature areas represent the complete gap between PMVC ORM and Django ORM:

| # | Feature Area | PMVC Status | Django Equivalent | Priority |
|---|-------------|-------------|-------------------|----------|
| 1 | **Query Builder Completion** | Only `exact` WHERE works; ORDER BY/GROUP BY/LIMIT are templates only | Full QuerySet API with chaining, lazy eval, caching | **P0 — Critical** |
| 2 | **Relationships & JOINs** | `OneToOneField` is empty stub; `Relation` attr is stub; no ForeignKey/ManyToMany classes | ForeignKey, ManyToMany, OneToOne with auto-JOINs, reverse relations | **P0 — Critical** |
| 3 | **Transaction Management** | No support | `atomic()`, `on_commit()`, savepoints | **P0 — Critical** |
| 4 | **Aggregation & Expressions** | Not implemented | `aggregate()`, `annotate()`, F expressions, Q objects, `Case`/`When` | **P1 — High** |
| 5 | **Advanced Query Operations** | Not supported | `select_related`, `prefetch_related`, `values()`, `only()`, `defer()`, `distinct()`, `exists()`, `count()`, bulk ops, set operations (UNION/INTERSECT/EXCEPT), subqueries | **P1 — High** |
| 6 | **Field Validation & Constraints** | No validators; basic column-level check/unique only | Validators, `CheckConstraint`, `UniqueConstraint`, composite indexes, partial indexes | **P2 — Medium** |
| 7 | **CLI & Migration Tooling** | Stubs only | `makemigrations`, `migrate`, `showmigrations`, `rollback`, interactive prompts | **P2 — Medium** |

### User Flow (Primary Journey)

```
Developer defines Model with #[Table] and #[Field] attributes
  → Runs CLI `makemigrations` → migration files auto-generated
  → Runs CLI `migrate` → database schema updated
  → Uses fluent query API:
      $model->filter('status', 'active')
            ->orderBy('-created_at')
            ->limit(10)
            ->getAll()
  → Uses relationships:
      $order->selectRelated('customer')  // eager load FK
  → Uses transactions:
      $orm->transaction(function() { ... })
  → Uses aggregation:
      $model->aggregate(['total' => ['sum', 'amount']])
```

---

## Core Requirements

### Must-Have Features (Phase 1 — Query Builder Completion)

**1.1 Complete WHERE Clause Generation**
- All 14 existing lookup operators must generate valid SQL in `getWhere()`: `exact`, `iexact`, `contains`, `icontains`, `startswith`, `istartswith`, `endswith`, `iendswith`, `regex`, `iregex`, `gt`, `gte`, `lt`, `lte` (currently only `exact` has a `case` in the switch)
- Add missing lookups: `in`, `range`, `isnull`
- Add `exclude()` method — negated filtering (Django's `exclude()`, generates `NOT (condition)`)
- Add `get()` method — single-record retrieval that raises error if 0 or 2+ results
- Fix `filter()` to support Django-style field lookup syntax (e.g., `filter('status', 'active')`) in addition to existing AND/OR type setter
- Fix `getWhere()` implode spacing — current `implode($this->_whereType, ...)` produces `col=:valANDcol=:val` without spaces
- All lookups must use bind parameters (no SQL injection)
- Engine-specific regex handling (PostgreSQL `~`/`~*` vs SQLite `REGEXP`)

**1.2 Complete Query Clauses**
- `orderBy($field, $direction)` — populate `[ORDER_BY]` template; support `-field` for DESC
- `groupBy($field)` — populate `[GROUP_BY]` template
- `having($condition)` — HAVING clause support
- `limit($n)` / `offset($n)` — populate `[LIMIT]` template; add `[OFFSET]` placeholder

**1.3 Query Result Methods**
- `count()` — `SELECT COUNT(*)` shorthand
- `exists()` — `SELECT EXISTS(...)` shorthand
- `first()` / `last()` — single-record retrieval with ordering
- `values($fields)` — return arrays instead of model instances
- `valuesList($fields, $flat)` — return flat arrays
- `distinct()` — `SELECT DISTINCT`

**1.4 Bulk Operations**
- `bulkCreate($objects, $batchSize)` — multi-row INSERT
- `bulkUpdate($objects, $fields, $batchSize)` — batched UPDATE

### Must-Have Features (Phase 2 — Relationships & Transactions)

**2.1 Relationship Fields**
- `ForeignKey` field type with `onDelete` referential action (CASCADE, SET NULL, RESTRICT, etc.)
- `ManyToManyField` with auto-created junction table
- Complete `OneToOneField` with automatic JOIN generation
- Reverse relation access (e.g., `$customer->orders`)

**2.2 JOIN Support**
- `selectRelated($fields)` — SQL JOIN for ForeignKey/OneToOne (eager loading)
- `prefetchRelated($fields)` — separate queries + PHP-side joining for ManyToMany
- Add `[JOIN]` placeholder to SELECT template
- Engine-specific JOIN syntax

**2.3 Transaction Management**
- `$orm->beginTransaction()` / `commit()` / `rollback()`
- `$orm->transaction(callable $fn)` — atomic block (auto-commit/rollback)
- Savepoint support: `savepoint($name)` / `releaseSavepoint($name)` / `rollbackToSavepoint($name)`
- `onCommit(callable $fn)` — post-commit hooks

### Must-Have Features (Phase 3 — Aggregation & Expressions)

**3.1 Aggregate Functions**
- `aggregate(['alias' => ['func', 'field']])` — COUNT, SUM, AVG, MAX, MIN
- `annotate(['alias' => ['func', 'field']])` — per-row computed fields
- GROUP BY auto-generation when annotating

**3.2 Expression Objects**
- `F('field')` — reference another field in queries (e.g., `$model->filter('stock__gt', F('reorder_level'))`)
- `Q` objects — complex AND/OR/NOT combinations: `Q::or(Q::exact('a', 1), Q::exact('b', 2))`
- `Case`/`When`/`Then` — conditional expressions in SQL

**3.3 Database Functions**
- `Coalesce`, `Concat`, `Lower`, `Upper`, `Length`, `Substr`, `Trim`
- `Now`, `Extract` (year/month/day from dates)
- `Cast` — type casting in SQL
- Engine-specific function mapping via Visitor pattern

### Technical Constraints

- **Architecture:** All new features MUST use the existing Visitor pattern. New Behaviors implement `Interfaces\Behavior`, new Engine methods added to base `Engine` and overridden per subclass.
- **Backward Compatibility:** Existing `BaseSqlModel` API must not break. New methods are additive.
- **Bind Parameters:** ALL user values MUST use PDO bind parameters. Zero raw value interpolation.
- **PSR-4 Compliance:** New classes follow existing directory conventions (Fields/ with capital F, Behaviors/ for behaviors).
- **Multi-DB:** Every feature must work on PostgreSQL and SQLite.
- **Naming Caution:** `DAO::commit()` is a SQL queue operation, NOT a database transaction. Transaction methods (Phase 2) must use distinct naming (e.g., `beginTransaction()`, `commitTransaction()`) to avoid confusion with the existing DAO queue API.

### Performance Goals

- Bulk operations must handle 1000+ rows without memory exhaustion (batched execution)
- Lazy query evaluation — queries should not execute until results are accessed
- `selectRelated` must produce a single SQL query (JOIN), not N+1

---

## Timeline & Success Metrics

### Major Milestones

| Phase | Scope | Estimated Effort | Dependencies |
|-------|-------|-----------------|--------------|
| **Phase 1** | Query Builder Completion | Foundation | None — builds on existing `WhereTrait` and templates |
| **Phase 2** | Relationships & Transactions | Core | Phase 1 (JOINs need WHERE clause working) |
| **Phase 3** | Aggregation & Expressions | Advanced Queries | Phase 1 (expressions compose with query builder) |
| **Phase 4** | CLI & Migration Tooling | DevEx | Phase 1-2 (migrations need relationship awareness) |

### Detailed Phase Breakdown

#### Phase 1 — Query Builder Completion
```
1.1  Implement all 14 WHERE lookups in getWhere() switch with bind params
1.2  Add new lookups: in(), range(), isnull()
1.3  Add exclude() (negated filter) and get() (single-record retrieval)
1.4  Refactor filter() to support Django-style field lookup syntax
1.5  Fix getWhere() implode spacing (AND/OR needs surrounding spaces)
1.6  Implement orderBy(), groupBy(), having(), limit(), offset()
1.7  Implement count(), exists(), first(), last(), values(), valuesList(), distinct()
1.8  Implement bulkCreate(), bulkUpdate()
1.9  Add integration tests for all operators on PostgreSQL + SQLite
```

#### Phase 2 — Relationships & Transactions
```
2.1  Implement ForeignKey field with referential actions
2.2  Implement ManyToManyField with junction table auto-creation
2.3  Complete OneToOneField with JOIN generation
2.4  Add [JOIN] to SELECT template; implement selectRelated()
2.5  Implement prefetchRelated() with separate query strategy
2.6  Implement reverse relation access
2.7  Implement transaction(), beginTransaction(), commit(), rollback()
2.8  Implement savepoints and onCommit()
2.9  Integration tests for relationships + transactions
```

#### Phase 3 — Aggregation & Expressions
```
3.1  Implement aggregate() with COUNT/SUM/AVG/MAX/MIN
3.2  Implement annotate() with auto GROUP BY
3.3  Implement F() expression class
3.4  Implement Q() object for complex boolean logic
3.5  Implement Case/When/Then conditional expressions
3.6  Implement database functions (Coalesce, Concat, Lower, Upper, etc.)
3.7  Engine-specific function mapping
3.8  Integration tests for all expressions
```

#### Phase 4 — CLI & Migration Tooling
```
4.1  Implement makemigrations command (detect model changes → generate migration files)
4.2  Implement migrate command (apply pending migrations in order)
4.3  Implement showmigrations command (display migration status)
4.4  Implement rollback/reverse migration support
4.5  Relationship-aware migration generation (FK constraints, junction tables)
4.6  Migration dependency resolution
```

### Success Metrics

| Metric | Target |
|--------|--------|
| WHERE lookup operators fully functional | 17/17 (14 existing + 3 new: `in`, `range`, `isnull`) |
| Query clause methods working | 6/6 (orderBy, groupBy, having, limit, offset, distinct) |
| Relationship types supported | 3/3 (ForeignKey, ManyToMany, OneToOne) |
| Aggregate functions available | 5/5 (COUNT, SUM, AVG, MAX, MIN) |
| Transaction methods implemented | 4/4 (begin, commit, rollback, atomic block) |
| Database engines fully supported | 2/2 (PostgreSQL, SQLite) |
| CLI commands operational | 3/3 (makemigrations, migrate, showmigrations) |
| Zero raw SQL injection vectors | 100% bind parameter coverage |

### Launch Criteria

- [ ] Phase 1 complete: All query builder features pass integration tests on PostgreSQL + SQLite
- [ ] Phase 2 complete: Relationships produce correct JOINs; transactions pass ACID tests
- [ ] Phase 3 complete: Aggregate/annotate/F/Q produce correct SQL across engines
- [ ] Phase 4 complete: CLI can generate and apply migrations end-to-end
- [ ] All features follow Visitor pattern architecture
- [ ] No backward-breaking changes to existing `BaseSqlModel` API
- [ ] Documentation and demo files updated for all new features

---

## Appendix A — Complete Gap Matrix

| Django Feature | PMVC ORM Status | Gap |
|---------------|----------------|-----|
| Model fields (30+ types) | 32+ types | **None** — feature parity achieved |
| PHP attributes for schema | `#[Table]`, `#[Field]`, `#[Column]` | **None** — already implemented |
| `filter()` with field lookups | `filter()` only sets AND/OR type; no field lookup syntax | **Critical** — needs Django-style API |
| `exclude()` | Not implemented | **Critical** — negated filtering |
| `get()` (single object) | Not implemented | **High** — single-record retrieval with error on 0/2+ |
| `order_by()` | Template exists, not populated | **Critical** |
| `LIMIT` / `OFFSET` | Template exists, not populated | **Critical** |
| `GROUP BY` / `HAVING` | Template exists, not populated | **Critical** |
| `DISTINCT` | Not supported | **High** |
| `count()` / `exists()` | Not supported | **High** |
| `first()` / `last()` | Not supported | **High** |
| `values()` / `values_list()` | Not supported | **High** |
| `only()` / `defer()` | Not supported | **Medium** |
| `bulk_create()` / `bulk_update()` | Not supported | **High** |
| `ForeignKey` | Not implemented | **Critical** |
| `ManyToManyField` | Not implemented | **Critical** |
| `OneToOneField` (with JOINs) | Empty stub file; engine maps type to integer, no JOIN | **Critical** |
| `select_related()` | Not supported | **Critical** |
| `prefetch_related()` | Not supported | **High** |
| Reverse relations | Not supported | **High** |
| Transactions (`atomic`) | Not supported | **Critical** |
| Savepoints | Not supported | **Medium** |
| `aggregate()` | Not implemented | **High** |
| `annotate()` | Not implemented | **High** |
| `F()` expressions | Not implemented | **High** |
| `Q()` objects | Not implemented | **High** |
| `Case` / `When` | Not implemented | **Medium** |
| Database functions | Not implemented | **Medium** |
| JOINs | Not supported | **Critical** |
| Subqueries | Not supported | **Medium** |
| UNION / INTERSECT / EXCEPT | Not supported | **Low** |
| Field validators | Not implemented | **Medium** |
| `CheckConstraint` / `UniqueConstraint` | Basic column-level only | **Medium** |
| Composite/partial indexes | Not supported | **Medium** |
| `makemigrations` CLI | Stub | **High** |
| `migrate` CLI | Stub | **High** |
| `showmigrations` CLI | Stub | **Medium** |
| Migration rollback | Not supported | **Medium** |
| Lazy QuerySet evaluation | Not implemented | **High** |
| QuerySet caching | Not implemented | **Medium** |
| `iterator()` | Not implemented | **Low** |
| `explain()` | Not implemented | **Low** |
| `select_for_update()` | Not implemented | **Low** |

**Total gaps identified: 41 features across 7 categories**
**Features already at parity: Field types, PHP attributes, schema definition, schema diffing, basic CRUD, migration infrastructure, Visitor pattern architecture**

---

*📋 PRD generated by SARAH — Strategic Artifact Refinement and Alignment Handler*
*🔧 Template Used: create-prd-simple-tmpl*
