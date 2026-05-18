-- Migration 005: Add sto_logo and sto_cover fields for store media
-- Run: psql -U <user> -d <db> -f migration_005_store_logo_cover.sql

ALTER TABLE stores
  ADD COLUMN IF NOT EXISTS sto_logo TEXT,
  ADD COLUMN IF NOT EXISTS sto_cover TEXT;

