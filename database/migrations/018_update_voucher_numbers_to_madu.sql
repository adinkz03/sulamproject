USE masjidkamek;

-- Update all payment voucher numbers from PV/2025/XXXX to MADU/2025/XXXX format
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0001' WHERE voucher_number = 'PV/2025/0001';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0002' WHERE voucher_number = 'PV/2025/0002';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0003' WHERE voucher_number = 'PV/2025/0003';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0004' WHERE voucher_number = 'PV/2025/0004';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0005' WHERE voucher_number = 'PV/2025/0005';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0006' WHERE voucher_number = 'PV/2025/0006';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0007' WHERE voucher_number = 'PV/2025/0007';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0008' WHERE voucher_number = 'PV/2025/0008';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0009' WHERE voucher_number = 'PV/2025/0009';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0010' WHERE voucher_number = 'PV/2025/0010';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0011' WHERE voucher_number = 'PV/2025/0011';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0012' WHERE voucher_number = 'PV/2025/0012';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0013' WHERE voucher_number = 'PV/2025/0013';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0014' WHERE voucher_number = 'PV/2025/0014';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0015' WHERE voucher_number = 'PV/2025/0015';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0016' WHERE voucher_number = 'PV/2025/0016';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0017' WHERE voucher_number = 'PV/2025/0017';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0018' WHERE voucher_number = 'PV/2025/0018';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0019' WHERE voucher_number = 'PV/2025/0019';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0020' WHERE voucher_number = 'PV/2025/0020';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0021' WHERE voucher_number = 'PV/2025/0021';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0022' WHERE voucher_number = 'PV/2025/0022';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0023' WHERE voucher_number = 'PV/2025/0023';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0024' WHERE voucher_number = 'PV/2025/0024';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0025' WHERE voucher_number = 'PV/2025/0025';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0026' WHERE voucher_number = 'PV/2025/0026';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0027' WHERE voucher_number = 'PV/2025/0027';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0028' WHERE voucher_number = 'PV/2025/0028';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0029' WHERE voucher_number = 'PV/2025/0029';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0030' WHERE voucher_number = 'PV/2025/0030';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0031' WHERE voucher_number = 'PV/2025/0031';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0032' WHERE voucher_number = 'PV/2025/0032';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0033' WHERE voucher_number = 'PV/2025/0033';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0034' WHERE voucher_number = 'PV/2025/0034';
UPDATE financial_payment_accounts SET voucher_number = 'MADU/2025/0035' WHERE voucher_number = 'PV/2025/0035';

-- Alternative: Use REPLACE function to update all at once
-- UPDATE financial_payment_accounts 
-- SET voucher_number = REPLACE(voucher_number, 'PV/', 'MADU/')
-- WHERE voucher_number LIKE 'PV/%';
