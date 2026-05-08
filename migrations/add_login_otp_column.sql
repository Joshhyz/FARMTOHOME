-- Migration: Add login_otp column to users table for one-time OTP functionality
-- This allows storing persistent OTP that can be reused across multiple login attempts

ALTER TABLE users ADD COLUMN login_otp VARCHAR(6) NULL DEFAULT NULL AFTER role;

-- Add index on login_otp for faster lookups (optional but recommended)
-- ALTER TABLE users ADD INDEX idx_login_otp (login_otp);
