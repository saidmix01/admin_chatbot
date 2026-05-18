-- Migration 006: Bot notifications queue (for background alerts)
-- Run: psql -U <user> -d <db> -f migration_006_bot_notifications.sql

CREATE TABLE IF NOT EXISTS bot_notifications (
  bn_id SERIAL PRIMARY KEY,
  us_id INTEGER NOT NULL,
  bn_type VARCHAR(64) NOT NULL,
  bn_status VARCHAR(24) NOT NULL DEFAULT 'pending',
  bn_payload JSONB NOT NULL DEFAULT '{}'::jsonb,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  processed_at TIMESTAMP NULL
);

CREATE INDEX IF NOT EXISTS idx_bot_notifications_us_status_created
  ON bot_notifications (us_id, bn_status, created_at DESC);

