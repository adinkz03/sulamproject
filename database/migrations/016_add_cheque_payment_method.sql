USE masjidkamek;

-- Add 'cheque' as a payment method option to financial tables
-- This allows tracking of cheque payments separately from bank transfers

-- Update financial_deposit_accounts to include 'cheque' option
ALTER TABLE financial_deposit_accounts 
MODIFY COLUMN payment_method ENUM('cash', 'bank', 'cheque') NOT NULL DEFAULT 'cash';

-- Update financial_payment_accounts to include 'cheque' option
ALTER TABLE financial_payment_accounts 
MODIFY COLUMN payment_method ENUM('cash', 'bank', 'cheque') NOT NULL DEFAULT 'cash';
