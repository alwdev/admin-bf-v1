<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;


class TransferController extends Controller
{
    public function generateMockupTransfers()
    {
        // รายการธนาคารและชื่อผู้ฝาก
        $banks = ['ธนาคารกสิกรไทย', 'ธนาคารไทยพาณิชย์', 'ธนาคารกรุงไทย', 'ธนาคารกรุงเทพ', 'ธนาคารทหารไทย'];
        $bankAccounts = ['2011073193', '1234567890', '0987654321', '5432167890', '9876543210'];
        $names = ['กวินทรา ใช้ฮวดเจริญ', 'ปิยธิดา ทวีวัฒน์', 'พิมพ์ชนก สุขจิตร', 'นัทธมนต์ สุริยะ', 'วินิจ สุรยุทธ', 'ภัทราภรณ์ โพธิ์ทอง', 'ธนัญชัย รุ่งเรือง', 'กนกวรรณ เจริญวงค์'];

        // ข้อมูลที่ต้องการเพิ่ม
        $targetTotalDeposit = 20000000;  // เป้าหมายยอดฝากรวมที่ต้องการให้เกิน 15 ล้าน
        $totalDeposit = 0;
        $totalWithdraw = 0;  // ตัวแปรเพื่อคำนวณยอดถอนรวม
        $createdAtStart = '2025-06-01 00:00:00';
        $createdAtEnd = '2025-06-17 23:59:59';

        $denyCount = 0;  // ตัวแปรนับจำนวนรายการ "ปฏิเสธ"
        $maxWithdraw = 1500000;  // กำหนดยอดถอนสูงสุดไม่เกิน 1.5 ล้าน

        // --- Loop สำหรับยอดถอน (withdraw) ---
        for ($i = 1; $i <= 5; $i++) {
            // เลือกผู้ฝากและข้อมูลที่เกี่ยวข้อง
            $withdrawBankName = $banks[array_rand($banks)];
            $withdrawBankAccount = $bankAccounts[array_rand($bankAccounts)];
            $withdrawBankNo = '1234567890';  // ตัวอย่างเลขบัญชีถอน
            $withdrawBankId = 'W' . rand(1000, 9999);
            $withdrawSlip = 'withdraw_slip_example.jpg';

            // สุ่มวันที่
            $createdAt = date('Y-m-d H:i:s', mt_rand(strtotime($createdAtStart), strtotime($createdAtEnd)));
            $updatedAt = date('Y-m-d H:i:s', strtotime($createdAt . ' + 10 minutes'));

            // ยอดถอนสุ่มไม่เกิน 1.5 ล้าน
            $withdrawAmount = rand(1, $maxWithdraw / 1000) * 1000;  // ยอดถอนไม่เกิน 1.5 ล้าน และให้เป็นเลขกลมๆ

            // เช็คว่ายอดถอนรวมยังไม่เกิน 1.5 ล้าน
            if ($totalWithdraw + $withdrawAmount > $maxWithdraw) {
                // ถ้ายอดถอนรวมเกิน 1.5 ล้าน ให้ปรับยอดถอนให้ไม่เกิน 1.5 ล้าน
                $withdrawAmount = $maxWithdraw - $totalWithdraw;
            }
            $totalWithdraw += $withdrawAmount;

            // SQL Query to insert withdraw data
            DB::table('transfer')->insert([
                'member_id' => rand(1, 10),  // member_id เป็นค่าที่สุ่ม
                'type' => 'withdraw',
                'ref_id' => null,
                'order_id' => null,
                'deposit_type' => 'โอนผ่านธนาคารภายในประเทศ',
                'deposit_from_bank_no' => null,
                'deposit_from_bank_type' => null,
                'deposit_from_bank_name' => null,
                'deposit_to_bank_no' => $withdrawBankNo,
                'deposit_to_bank_type' => 'ธนาคารกสิกรไทย',
                'deposit_to_bank_name' => 'นพพล อึ้งเท้ง',
                'deposit_slip' => null,
                'withdraw_bank_name' => $withdrawBankName,
                'withdraw_bank_account' => $withdrawBankAccount,
                'withdraw_bank_no' => $withdrawBankNo,
                'withdraw_bank_id' => $withdrawBankId,
                'withdraw_slip' => $withdrawSlip,
                'otp' => null,
                'refNo' => '',
                'promotion' => null,
                'promotion_id' => 0,
                'status' => 2,
                'status_code' => 'BOT.อนุมัติ',
                'transfer_date' => strtotime($createdAt),
                'amount' => $withdrawAmount,
                'old_balance' => 0.00,
                'new_balance' => $withdrawAmount,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
                'turnover_on' => 0,
                'turnover' => 0.00,
                'turnover_balance' => 0.00,
            ]);
        }

        // --- Loop สำหรับยอดฝาก (deposit) ---
        for ($i = 1; $i <= 100; $i++) {
            // เลือกผู้ฝากและข้อมูลที่เกี่ยวข้อง
            $depositFromBankNo = $bankAccounts[array_rand($bankAccounts)];
            $depositFromBankType = $banks[array_rand($banks)];
            $depositFromBankName = $names[array_rand($names)];

            // ระบบฝากเงิน
            $depositToBankNo = '2011073773';
            $depositToBankType = 'ธนาคารกสิกรไทย';
            $depositToBankName = 'ปรัชญา สัมฤทธิ์';

            // ยอดฝากสุ่มเป็นเลขกลมๆ เช่น 1,000 / 10,000 / 100,000
            $depositAmount = rand(1, 200) * 1000;
            $totalDeposit += $depositAmount;

            // ตรวจสอบยอดฝากให้เกิน 15 ล้าน
            if ($totalDeposit > $targetTotalDeposit) {
                $difference = $totalDeposit - $targetTotalDeposit;
                $depositAmount -= $difference;
                $totalDeposit = $targetTotalDeposit;
            }

            // สุ่มวันที่และเวลา
            $createdAt = date('Y-m-d H:i:s', mt_rand(strtotime($createdAtStart), strtotime($createdAtEnd)));
            $updatedAt = date('Y-m-d H:i:s', strtotime($createdAt . ' + 10 minutes'));

            // คำนวณ timestamp จาก created_at
            $transferDateTimestamp = strtotime($createdAt);  // ใช้ฟังก์ชั่น strtotime เพื่อแปลงเป็น timestamp

            // กำหนดการสุ่มค่าของ status และ status_code
            $status = 'BOT.อนุมัติ';
            $statusCode = 2;  // ค่าเริ่มต้นคืออนุมัติ

            // ถ้า count ของรายการ "ปฏิเสธ" ยังไม่ถึง 5 ครั้ง
            if ($denyCount < 5 && rand(1, 100) <= 5) {  // มีโอกาส 5% ที่จะเป็น "ปฏิเสธ"
                $status = 'ปฏิเสธ';
                $statusCode = 3;  // ปฏิเสธ
                $denyCount++;  // เพิ่มจำนวนรายการปฏิเสธ
            }

            // SQL Query to insert deposit data
            DB::table('transfer')->insert([
                'member_id' => rand(1, 10),  // member_id เป็นค่าที่สุ่ม
                'type' => 'deposit',
                'ref_id' => null,
                'order_id' => null,
                'deposit_type' => 'โอนผ่านธนาคารภายในประเทศ',
                'deposit_from_bank_no' => $depositFromBankNo,
                'deposit_from_bank_type' => $depositFromBankType,
                'deposit_from_bank_name' => $depositFromBankName,
                'deposit_to_bank_no' => $depositToBankNo,
                'deposit_to_bank_type' => $depositToBankType,
                'deposit_to_bank_name' => $depositToBankName,
                'deposit_slip' => null,
                'withdraw_bank_name' => null,
                'withdraw_bank_account' => null,
                'withdraw_bank_no' => null,
                'withdraw_bank_id' => null,
                'withdraw_slip' => null,
                'otp' => null,
                'refNo' => '',
                'promotion' => null,
                'promotion_id' => 0,
                'status' => $statusCode,
                'status_code' => $status,
                'transfer_date' => $transferDateTimestamp,
                'amount' => $depositAmount,
                'old_balance' => 0.00,
                'new_balance' => $depositAmount,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
                'turnover_on' => 0,
                'turnover' => 0.00,
                'turnover_balance' => 0.00,
            ]);
        }


        // ส่งยอดฝากรวมและยอดถอนรวมกลับ
        return response()->json([
            'message' => 'Generated Mockup Transfer Data successfully!',
            'total_deposit' => $totalDeposit,
            'total_withdraw' => $totalWithdraw,  // ส่งยอดถอนรวมกลับ
            'deny_count' => $denyCount,  // ส่งจำนวน "ปฏิเสธ" กลับ
        ]);
    }

  public function generateMockupMembers()
{
    // รายการข้อมูลสมาชิก (ตัวอย่าง)
$members = [
    [
        'member_id' => 'bhm516zhlqcG',
        'username' => '0999999999',
        'password' => '$2y$12$44.wjKRcKC3F6by9E5GFnevZSfgiMBySpNON/.UwzyaLtoVlOXDBe',
        'wallet_balance' => '128.42',
        'fullname' => 'กวินทรา ใช้ฮวดเจริญ',
        'bank_name' => 'ธนาคารกสิกรไทย',
        'bank_number' => '0999999999',
        'bank_code' => 'bank-0',
        'account_name' => 'กวินทรา ใช้ฮวดเจริญ',
        'phone' => '0999999999',
        'status_code' => 2,  // BOT.อนุมัติ
        'level' => 'Bronze',
    ],
    [
        'member_id' => 'bhm516igVqNm',
        'username' => '0649174824',
        'password' => '$2y$12$Gscd8Yz4Nw95R9TPGLiPQeBHjkSFH3zUW1lylNdXLrnGkOzBWR/MW',
        'wallet_balance' => '0',
        'fullname' => 'จักรพันธ์ มณีปกรณ์',
        'bank_name' => 'ธนาคารกสิกรไทย',
        'bank_number' => '0811854956',
        'bank_code' => 'bank-0',
        'account_name' => 'จักรพันธ์ มณีปกรณ์',
        'phone' => '0649174824',
        'status_code' => 1,  // BOT.ปฏิเสธ
        'level' => 'Bronze',
    ],
    [
        'member_id' => 'bhm516YvcvL4',
        'username' => '0876543210',
        'password' => '$2y$12$Afjjs8I1FEws9jv8tIwiMe21gxwASL8DA2W2bDe1g7We3kHS1zYpu',
        'wallet_balance' => '1,200.00',
        'fullname' => 'พิมพ์ชนก สุขจิตร',
        'bank_name' => 'ธนาคารกรุงไทย',
        'bank_number' => '0876543210',
        'bank_code' => 'bank-1',
        'account_name' => 'พิมพ์ชนก สุขจิตร',
        'phone' => '0876543210',
        'status_code' => 2,  // BOT.อนุมัติ
        'level' => 'Bronze',
    ],
    [
        'member_id' => 'bhm516zfgTht',
        'username' => '0898765432',
        'password' => '$2y$12$96DE29nDh2X4jdsKBgDZTz11dxBoGgPxKNNkgadKnQykCjcPOg7ZO',
        'wallet_balance' => '0',
        'fullname' => 'นัทธมนต์ สุริยะ',
        'bank_name' => 'ธนาคารกรุงเทพ',
        'bank_number' => '0898765432',
        'bank_code' => 'bank-2',
        'account_name' => 'นัทธมนต์ สุริยะ',
        'phone' => '0898765432',
        'status_code' => 2,  // BOT.อนุมัติ
        'level' => 'Bronze',
    ],
    [
        'member_id' => 'bhm516qdhtLz',
        'username' => '0623456789',
        'password' => '$2y$12$G5fyygsn04l9y9uPTd7nMzQN9K6X5x2hWiPYI1d8Aq0O0El8VR2iu',
        'wallet_balance' => '500.50',
        'fullname' => 'วินิจ สุรยุทธ',
        'bank_name' => 'ธนาคารทหารไทย',
        'bank_number' => '0623456789',
        'bank_code' => 'bank-3',
        'account_name' => 'วินิจ สุรยุทธ',
        'phone' => '0623456789',
        'status_code' => 1,  // BOT.ปฏิเสธ
        'level' => 'Bronze',
    ],

    // เพิ่มเติมสมาชิกใหม่ 2 คนที่คุณขอ
    [
        'member_id' => 'bhm516FQXpdG',
        'username' => '0959988776',
        'password' => '$2y$12$CBwFpPnLgtS0E5zvbT6Edt9csezECGBXBmfM9Jg46EFswrj4BlFCi',
        'wallet_balance' => '745.50',
        'fullname' => 'ไกรสร บัวหอม',
        'bank_name' => 'ธนาคารกรุงเทพ',
        'bank_number' => '0959988776',
        'bank_code' => 'bank-4',
        'account_name' => 'ไกรสร บัวหอม',
        'phone' => '0959988776',
        'status_code' => 2,  // BOT.อนุมัติ
        'level' => 'Bronze',
    ],
    [
        'member_id' => 'bhm516XTJhUd',
        'username' => '0847234567',
        'password' => '$2y$12$8xzHjkdlwJkpfS8ji6cQjwPKYgb9Yr6w4KXrbdbSEjDXlsR9W.jfC',
        'wallet_balance' => '1,150.00',
        'fullname' => 'พิมพ์ชนก อารีย์',
        'bank_name' => 'ธนาคารไทยพาณิชย์',
        'bank_number' => '0847234567',
        'bank_code' => 'bank-5',
        'account_name' => 'พิมพ์ชนก อารีย์',
        'phone' => '0847234567',
        'status_code' => 2,  // BOT.อนุมัติ
        'level' => 'Bronze',
    ],
    [
        'member_id' => 'bhm516ZG3r9y',
        'username' => '0859123456',
        'password' => '$2y$12$k7EKcvfgyNUNyFHXmmD7fS0JJlSo01g1HXJcODdjGh8kLC4fpTlyE',
        'wallet_balance' => '250.75',
        'fullname' => 'สุภัทรา ศรีบุญญา',
        'bank_name' => 'ธนาคารทหารไทย',
        'bank_number' => '0859123456',
        'bank_code' => 'bank-6',
        'account_name' => 'สุภัทรา ศรีบุญญา',
        'phone' => '0859123456',
        'status_code' => 1,  // BOT.ปฏิเสธ
        'level' => 'Bronze',
    ],
    [
        'member_id' => 'bhm516NC8cF6',
        'username' => '0823456789',
        'password' => '$2y$12$ovNsOSivLghsW9P57UffuORu3.9r6fn6bzNjfZPvJpycA29C7CHyS',
        'wallet_balance' => '450.25',
        'fullname' => 'สมปอง พรหมมา',
        'bank_name' => 'ธนาคารกรุงไทย',
        'bank_number' => '0823456789',
        'bank_code' => 'bank-7',
        'account_name' => 'สมปอง พรหมมา',
        'phone' => '0823456789',
        'status_code' => 2,  // BOT.อนุมัติ
        'level' => 'Bronze',
    ]
];

    // Loop เพื่อ insert ข้อมูล mockup
    foreach ($members as $member) {
        // สุ่มวันที่จากวันที่ 1-17 มิถุนายน 2025
        $randomDate = Carbon::create(2025, 6, rand(1, 17), rand(0, 23), rand(0, 59), rand(0, 59));

        // ถ้าเวลาที่สุ่มเกินเวลาปัจจุบัน ให้ปรับเวลาปัจจุบัน
        if ($randomDate->greaterThan(Carbon::now())) {
            $randomDate = Carbon::now();
        }

        DB::table('members')->insert([
            'member_id' => $member['member_id'],
            'username' => $member['username'],
            'password' => $member['password'],
            'wallet_balance' => $member['wallet_balance'],
            'wallet_specialBuyIn' => '',
            'wallet_lastUpdate' => '',
            'level' => $member['level'],
            'parent' => '',
            'type' => '',
            'playId' => '',
            'currency' => '',
            'created_at' => $randomDate,
            'updated_at' => $randomDate,
            'fullname' => $member['fullname'],
            'bank_name' => $member['bank_name'],
            'bank_number' => $member['bank_number'],
            'bank_code' => $member['bank_code'],
            'birth_date' => NULL,
            'account_name' => $member['account_name'],
            'phone' => $member['phone'],
            'enable' => 1,
            'active' => 1,
            'update_by' => 0,
            'ref_click_link' => 0,
            'ref_user' => '',
            'ref_commission' => 0.00,
            'token' => '',
            'ranking' => '',
            'source' => '',
            'role' => '',
            'nickname' => '',
            'remaining_spin' => 0.00,
        ]);
    }

    return response()->json([
        'message' => 'Generated Mockup Member Data successfully!',
    ]);
}

}
