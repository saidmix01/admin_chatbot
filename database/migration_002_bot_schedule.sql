-- Migration 002: Add bot schedule and messaging fields to stores
-- Run: psql -U <user> -d <db> -f migration_002_bot_schedule.sql

ALTER TABLE stores
  ADD COLUMN IF NOT EXISTS sto_schedule_enabled INTEGER DEFAULT 0,
  ADD COLUMN IF NOT EXISTS sto_schedule_open VARCHAR(5) DEFAULT '09:00',
  ADD COLUMN IF NOT EXISTS sto_schedule_close VARCHAR(5) DEFAULT '18:00',
  ADD COLUMN IF NOT EXISTS sto_schedule_days VARCHAR(50) DEFAULT '1,2,3,4,5',
  ADD COLUMN IF NOT EXISTS sto_offhours_message TEXT,
  ADD COLUMN IF NOT EXISTS sto_menu_message TEXT,
  ADD COLUMN IF NOT EXISTS sto_goodbye_message TEXT,
  ADD COLUMN IF NOT EXISTS sto_description TEXT,
  ADD COLUMN IF NOT EXISTS sto_timezone VARCHAR(50) DEFAULT 'America/Bogota';

-- Copy existing sto_wellcome_message into sto_description for businesses that already set it
UPDATE stores SET sto_description = sto_wellcome_message WHERE sto_wellcome_message IS NOT NULL;
