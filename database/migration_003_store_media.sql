-- Migration 003: Add store media fields (profile/cover) and public slug
-- Run: psql -U <user> -d <db> -f migration_003_store_media.sql

ALTER TABLE stores
  ADD COLUMN IF NOT EXISTS sto_profile_image TEXT,
  ADD COLUMN IF NOT EXISTS sto_cover_image TEXT,
  ADD COLUMN IF NOT EXISTS sto_slug VARCHAR(200);

CREATE UNIQUE INDEX IF NOT EXISTS idx_stores_sto_slug_unique ON stores (sto_slug);

UPDATE stores
SET sto_slug = COALESCE(NULLIF(sto_slug, ''), 'store-' || sto_id::text)
WHERE sto_slug IS NULL OR sto_slug = '';

