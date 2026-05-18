ALTER TABLE stores
  ADD COLUMN IF NOT EXISTS sto_inactivity_minutes INTEGER DEFAULT 15,
  ADD COLUMN IF NOT EXISTS sto_inactivity_message TEXT;

