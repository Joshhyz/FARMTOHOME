# One-Time OTP Login System - Implementation Guide

## Overview
The login system has been updated to use a **persistent one-time OTP** (OTP = One-Time Password) instead of generating new codes on every login attempt.

## Key Changes

### How It Works Now:

1. **First Login/Signup**: User enters their email and receives an OTP via email
2. **OTP Persistence**: The OTP is stored in the database (`users.login_otp` column)
3. **Subsequent Logins**: When the user logs in again, the system:
   - Checks if an OTP already exists for that email
   - **If OTP exists**: Uses the same OTP (no new email sent)
   - **If OTP doesn't exist**: Generates and sends a new OTP
4. **After Logout**: The OTP remains in the database, so the user can log back in with the same code

## Files Modified

### 1. **configmailer.php**
Added two new functions:
- `getExistingLoginOTP($email)` - Retrieves existing OTP from database
- `saveLoginOTP($email, $code)` - Saves/updates OTP in database

### 2. **registration.php**
- Login flow now checks for existing OTP before sending email
- Only sends email if no OTP exists for that email
- Stores OTP in database on signup

### 3. **verifycode.php**
- Removed "Resend Code" functionality (not needed anymore)
- OTP is no longer deleted after successful login verification
- For signup: OTP is now saved to database during user creation

### 4. **Database Migration**
- Added `login_otp` column to `users` table
- Run the SQL migration file: `migrations/add_login_otp_column.sql`

## Setup Instructions

### Step 1: Run Database Migration
Execute the following SQL query in your database:

```sql
ALTER TABLE users ADD COLUMN login_otp VARCHAR(6) NULL DEFAULT NULL AFTER role;
```

Or simply run the migration file:
```
migrations/add_login_otp_column.sql
```

### Step 2: Test the System

1. **First Login**: User signs up or logs in → Receives OTP via email
2. **Verify OTP**: User enters the code → Successfully logged in
3. **Logout**: User logs out
4. **Second Login**: User logs in with same email → No email sent, uses existing OTP
5. **Verify Same OTP**: User enters the same code again → Successfully logged in

## Benefits

✅ **Reduced Email Load**: OTP sent only once per user
✅ **Better User Experience**: No need to resend codes
✅ **Persistent Authentication**: Same code works across sessions
✅ **Simple & Secure**: Code is stored server-side in database

## Security Notes

- OTP is stored in the database, not in session
- After logout, OTP persists but user must verify with correct password
- Consider adding an expiration timestamp if you want codes to expire

## Future Enhancements (Optional)

1. Add `login_otp_created_at` column for tracking when OTP was created
2. Add code expiration logic (e.g., expire after 30 days)
3. Allow users to manually reset their OTP from account settings
4. Add multiple OTP tracking (generate new one but keep history)
