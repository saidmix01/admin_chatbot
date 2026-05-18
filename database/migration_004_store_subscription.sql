-- Migration 004: Add subscription dates to stores
-- Run: psql -U <user> -d <db> -f migration_004_store_subscription.sql

ALTER TABLE stores
  ADD COLUMN IF NOT EXISTS sto_plan_start DATE,
  ADD COLUMN IF NOT EXISTS sto_plan_end DATE;

UPDATE stores
SET sto_plan_start = COALESCE(sto_plan_start, CURRENT_DATE)
WHERE sto_plan_start IS NULL;

UPDATE stores
SET sto_plan_end = COALESCE(sto_plan_end, (sto_plan_start + INTERVAL '30 days')::date)
WHERE sto_plan_end IS NULL;

