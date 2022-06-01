SELECT *
  FROM INFORMATION_SCHEMA.TABLES
  WHERE table_type = 'BASE TABLE' AND table_catalog = '[DB_NAME]' AND table_schema='public'
  ORDER BY TABLE_NAME
