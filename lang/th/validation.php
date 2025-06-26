<?php
return [

    /*
    |----------------------------------------------------------------------
    | Validation Language Lines
    |----------------------------------------------------------------------
    |
    | ข้อความข้อผิดพลาดที่ใช้โดยคลาสตัวตรวจสอบบางตัว ในบางกฎจะมีหลายเวอร์ชัน
    | เช่นกฎขนาด ข้อความเหล่านี้สามารถปรับแต่งได้ตามต้องการ
    |
    */

    'accepted' => 'ฟิลด์ :attribute ต้องได้รับการยอมรับ.',
    'accepted_if' => 'ฟิลด์ :attribute ต้องได้รับการยอมรับเมื่อ :other มีค่าเป็น :value.',
    'active_url' => 'ฟิลด์ :attribute ต้องเป็น URL ที่ถูกต้อง.',
    'after' => 'ฟิลด์ :attribute ต้องเป็นวันที่หลังจาก :date.',
    'after_or_equal' => 'ฟิลด์ :attribute ต้องเป็นวันที่หลังหรือเท่ากับ :date.',
    'alpha' => 'ฟิลด์ :attribute ต้องประกอบด้วยตัวอักษรเท่านั้น.',
    'alpha_dash' => 'ฟิลด์ :attribute ต้องประกอบด้วยตัวอักษร ตัวเลข ขีดกลาง และขีดล่างเท่านั้น.',
    'alpha_num' => 'ฟิลด์ :attribute ต้องประกอบด้วยตัวอักษรและตัวเลขเท่านั้น.',
    'array' => 'ฟิลด์ :attribute ต้องเป็นอาเรย์.',
    'ascii' => 'ฟิลด์ :attribute ต้องประกอบด้วยตัวอักษรอัลฟา-นัมเบอร์ และสัญลักษณ์แบบบิตเดี่ยวเท่านั้น.',
    'before' => 'ฟิลด์ :attribute ต้องเป็นวันที่ก่อน :date.',
    'before_or_equal' => 'ฟิลด์ :attribute ต้องเป็นวันที่ก่อนหรือเท่ากับ :date.',
    'between' => [
        'array' => 'ฟิลด์ :attribute ต้องมีระหว่าง :min และ :max รายการ.',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาดระหว่าง :min และ :max กิโลไบต์.',
        'numeric' => 'ฟิลด์ :attribute ต้องมีค่าอยู่ระหว่าง :min และ :max.',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาวระหว่าง :min และ :max อักขระ.',
    ],
    'boolean' => 'ฟิลด์ :attribute ต้องเป็นจริงหรือเท็จ.',
    'can' => 'ฟิลด์ :attribute มีค่าไม่อนุญาต.',
    'confirmed' => 'ฟิลด์ :attribute ยืนยันไม่ตรงกัน.',
    'current_password' => 'รหัสผ่านไม่ถูกต้อง.',
    'date' => 'ฟิลด์ :attribute ต้องเป็นวันที่ที่ถูกต้อง.',
    'date_equals' => 'ฟิลด์ :attribute ต้องเป็นวันที่ที่เท่ากับ :date.',
    'date_format' => 'ฟิลด์ :attribute ต้องตรงกับรูปแบบ :format.',
    'decimal' => 'ฟิลด์ :attribute ต้องมี :decimal ตำแหน่งทศนิยม.',
    'declined' => 'ฟิลด์ :attribute ต้องปฏิเสธ.',
    'declined_if' => 'ฟิลด์ :attribute ต้องปฏิเสธเมื่อ :other มีค่าเป็น :value.',
    'different' => 'ฟิลด์ :attribute และ :other ต้องแตกต่างกัน.',
    'digits' => 'ฟิลด์ :attribute ต้องประกอบด้วย :digits หลัก.',
    'digits_between' => 'ฟิลด์ :attribute ต้องมีระหว่าง :min และ :max หลัก.',
    'dimensions' => 'ฟิลด์ :attribute มีขนาดภาพที่ไม่ถูกต้อง.',
    'distinct' => 'ฟิลด์ :attribute มีค่าที่ซ้ำกัน.',
    'doesnt_end_with' => 'ฟิลด์ :attribute ต้องไม่ลงท้ายด้วยค่าหนึ่งจากค่าต่อไปนี้: :values.',
    'doesnt_start_with' => 'ฟิลด์ :attribute ต้องไม่เริ่มต้นด้วยค่าหนึ่งจากค่าต่อไปนี้: :values.',
    'email' => 'ฟิลด์ :attribute ต้องเป็นที่อยู่อีเมลที่ถูกต้อง.',
    'ends_with' => 'ฟิลด์ :attribute ต้องลงท้ายด้วยค่าหนึ่งจากค่าต่อไปนี้: :values.',
    'enum' => 'ตัวเลือก :attribute ที่เลือกไม่ถูกต้อง.',
    'exists' => 'ตัวเลือก :attribute ที่เลือกไม่ถูกต้อง.',
    'extensions' => 'ฟิลด์ :attribute ต้องมีนามสกุลไฟล์หนึ่งจากค่าต่อไปนี้: :values.',
    'file' => 'ฟิลด์ :attribute ต้องเป็นไฟล์.',
    'filled' => 'ฟิลด์ :attribute ต้องมีค่า.',
    'gt' => [
        'array' => 'ฟิลด์ :attribute ต้องมีมากกว่า :value รายการ.',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาดมากกว่า :value กิโลไบต์.',
        'numeric' => 'ฟิลด์ :attribute ต้องมีค่ามากกว่า :value.',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาวมากกว่า :value อักขระ.',
    ],
    'gte' => [
        'array' => 'ฟิลด์ :attribute ต้องมี :value รายการขึ้นไป.',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาดมากกว่าหรือเท่ากับ :value กิโลไบต์.',
        'numeric' => 'ฟิลด์ :attribute ต้องมีค่ามากกว่าหรือเท่ากับ :value.',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาวมากกว่าหรือเท่ากับ :value อักขระ.',
    ],
    'hex_color' => 'ฟิลด์ :attribute ต้องเป็นสีแบบ hexadecimal ที่ถูกต้อง.',
    'image' => 'ฟิลด์ :attribute ต้องเป็นภาพ.',
    'in' => 'ตัวเลือก :attribute ที่เลือกไม่ถูกต้อง.',
    'in_array' => 'ฟิลด์ :attribute ต้องมีอยู่ใน :other.',
    'integer' => 'ฟิลด์ :attribute ต้องเป็นจำนวนเต็ม.',
    'ip' => 'ฟิลด์ :attribute ต้องเป็นที่อยู่ IP ที่ถูกต้อง.',
    'ipv4' => 'ฟิลด์ :attribute ต้องเป็นที่อยู่ IPv4 ที่ถูกต้อง.',
    'ipv6' => 'ฟิลด์ :attribute ต้องเป็นที่อยู่ IPv6 ที่ถูกต้อง.',
    'json' => 'ฟิลด์ :attribute ต้องเป็นสตริง JSON ที่ถูกต้อง.',
    'lowercase' => 'ฟิลด์ :attribute ต้องเป็นตัวพิมพ์เล็ก.',
    'lt' => [
        'array' => 'ฟิลด์ :attribute ต้องมีน้อยกว่า :value รายการ.',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาดน้อยกว่า :value กิโลไบต์.',
        'numeric' => 'ฟิลด์ :attribute ต้องมีค่าน้อยกว่า :value.',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาวน้อยกว่า :value อักขระ.',
    ],
    'lte' => [
        'array' => 'ฟิลด์ :attribute ต้องไม่มากกว่า :value รายการ.',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาดน้อยกว่าหรือเท่ากับ :value กิโลไบต์.',
        'numeric' => 'ฟิลด์ :attribute ต้องมีค่าน้อยกว่าหรือเท่ากับ :value.',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาวน้อยกว่าหรือเท่ากับ :value อักขระ.',
    ],
    'mac_address' => 'ฟิลด์ :attribute ต้องเป็นที่อยู่ MAC ที่ถูกต้อง.',
    'max' => [
        'array' => 'ฟิลด์ :attribute ต้องมีไม่เกิน :max รายการ.',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาดไม่เกิน :max กิโลไบต์.',
        'numeric' => 'ฟิลด์ :attribute ต้องมีค่าน้อยกว่าหรือเท่ากับ :max.',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาวไม่เกิน :max อักขระ.',
    ],
    'max_digits' => 'ฟิลด์ :attribute ต้องมีไม่เกิน :max หลัก.',
    'mimes' => 'ฟิลด์ :attribute ต้องเป็นไฟล์ประเภท: :values.',
    'mimetypes' => 'ฟิลด์ :attribute ต้องเป็นไฟล์ประเภท: :values.',
    'min' => [
        'array' => 'ฟิลด์ :attribute ต้องมีอย่างน้อย :min รายการ.',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาดอย่างน้อย :min กิโลไบต์.',
        'numeric' => 'ฟิลด์ :attribute ต้องมีค่ามากกว่าหรือเท่ากับ :min.',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาวอย่างน้อย :min อักขระ.',
    ],
    'min_digits' => 'ฟิลด์ :attribute ต้องมีอย่างน้อย :min หลัก.',
    'missing' => 'ฟิลด์ :attribute ต้องหายไป.',
    'missing_if' => 'ฟิลด์ :attribute ต้องหายไปเมื่อ :other มีค่าเป็น :value.',
    'missing_unless' => 'ฟิลด์ :attribute ต้องหายไปเว้นแต่ :other จะมีค่าเป็น :value.',
    'missing_with' => 'ฟิลด์ :attribute ต้องหายไปเมื่อ :values ปรากฏ.',
    'missing_with_all' => 'ฟิลด์ :attribute ต้องหายไปเมื่อ :values ปรากฏทั้งหมด.',
    'multiple_of' => 'ฟิลด์ :attribute ต้องเป็นจำนวนเท่าของ :value.',
    'not_in' => 'ตัวเลือก :attribute ที่เลือกไม่ถูกต้อง.',
    'not_regex' => 'ฟิลด์ :attribute มีรูปแบบไม่ถูกต้อง.',
    'numeric' => 'ฟิลด์ :attribute ต้องเป็นตัวเลข.',
    'password' => [
        'letters' => 'ฟิลด์ :attribute ต้องมีตัวอักษรอย่างน้อยหนึ่งตัว.',
        'mixed' => 'ฟิลด์ :attribute ต้องมีตัวอักษรพิมพ์ใหญ่และตัวพิมพ์เล็กอย่างน้อยหนึ่งตัว.',
        'numbers' => 'ฟิลด์ :attribute ต้องมีตัวเลขอย่างน้อยหนึ่งตัว.',
        'symbols' => 'ฟิลด์ :attribute ต้องมีสัญลักษณ์อย่างน้อยหนึ่งตัว.',
        'uncompromised' => 'ฟิลด์ :attribute ที่ให้มามีข้อมูลรั่วไหลจากแหล่งข้อมูล ขอให้เลือกฟิลด์ :attribute อื่น.',
    ],
    'present' => 'ฟิลด์ :attribute ต้องปรากฏ.',
    'present_if' => 'ฟิลด์ :attribute ต้องปรากฏเมื่อ :other มีค่าเป็น :value.',
    'present_unless' => 'ฟิลด์ :attribute ต้องปรากฏเว้นแต่ :other จะมีค่าเป็น :value.',
    'present_with' => 'ฟิลด์ :attribute ต้องปรากฏเมื่อ :values ปรากฏ.',
    'present_with_all' => 'ฟิลด์ :attribute ต้องปรากฏเมื่อ :values ปรากฏทั้งหมด.',
    'prohibited' => 'ฟิลด์ :attribute ห้ามใช้งาน.',
    'prohibited_if' => 'ฟิลด์ :attribute ห้ามใช้งานเมื่อ :other มีค่าเป็น :value.',
    'prohibited_unless' => 'ฟิลด์ :attribute ห้ามใช้งานเว้นแต่ :other จะอยู่ใน :values.',
    'prohibits' => 'ฟิลด์ :attribute ห้ามไม่ให้ :other ปรากฏ.',
    'regex' => 'ฟิลด์ :attribute มีรูปแบบไม่ถูกต้อง.',
    'required' => 'ฟิลด์ :attribute จำเป็นต้องใช้.',
    'required_array_keys' => 'ฟิลด์ :attribute ต้องประกอบด้วยค่าของ: :values.',
    'required_if' => 'ฟิลด์ :attribute จำเป็นต้องใช้เมื่อ :other มีค่าเป็น :value.',
    'required_if_accepted' => 'ฟิลด์ :attribute จำเป็นต้องใช้เมื่อ :other ถูกยอมรับ.',
    'required_unless' => 'ฟิลด์ :attribute จำเป็นต้องใช้เว้นแต่ :other จะอยู่ใน :values.',
    'required_with' => 'ฟิลด์ :attribute จำเป็นต้องใช้เมื่อ :values ปรากฏ.',
    'required_with_all' => 'ฟิลด์ :attribute จำเป็นต้องใช้เมื่อ :values ปรากฏทั้งหมด.',
    'required_without' => 'ฟิลด์ :attribute จำเป็นต้องใช้เมื่อ :values ไม่ปรากฏ.',
    'required_without_all' => 'ฟิลด์ :attribute จำเป็นต้องใช้เมื่อ :values ทั้งหมดไม่ปรากฏ.',
    'same' => 'ฟิลด์ :attribute ต้องตรงกับ :other.',
    'size' => [
        'array' => 'ฟิลด์ :attribute ต้องมี :size รายการ.',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาด :size กิโลไบต์.',
        'numeric' => 'ฟิลด์ :attribute ต้องมีค่า :size.',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาว :size อักขระ.',
    ],
    'starts_with' => 'ฟิลด์ :attribute ต้องเริ่มต้นด้วยค่าหนึ่งจากค่าต่อไปนี้: :values.',
    'string' => 'ฟิลด์ :attribute ต้องเป็นสตริง.',
    'timezone' => 'ฟิลด์ :attribute ต้องเป็นเขตเวลาที่ถูกต้อง.',
    'unique' => 'ฟิลด์ :attribute นี้มีอยู่แล้ว.',
    'uploaded' => 'ฟิลด์ :attribute อัปโหลดไม่สำเร็จ.',
    'uppercase' => 'ฟิลด์ :attribute ต้องเป็นตัวพิมพ์ใหญ่.',
    'url' => 'ฟิลด์ :attribute ต้องเป็น URL ที่ถูกต้อง.',
    'ulid' => 'ฟิลด์ :attribute ต้องเป็น ULID ที่ถูกต้อง.',
    'uuid' => 'ฟิลด์ :attribute ต้องเป็น UUID ที่ถูกต้อง.',

    /*
    |----------------------------------------------------------------------
    | Custom Validation Language Lines
    |----------------------------------------------------------------------
    |
    | ที่นี่คุณสามารถกำหนดข้อความข้อผิดพลาดที่กำหนดเองสำหรับแอตทริบิวต์ที่ใช้
    | กฎ "attribute.rule" เพื่อระบุข้อความเฉพาะสำหรับกฎที่กำหนดของแอตทริบิวต์
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |----------------------------------------------------------------------
    | Custom Validation Attributes
    |----------------------------------------------------------------------
    |
    | ข้อความเหล่านี้จะใช้เพื่อแทนที่ค่าตัวแปรของแอตทริบิวต์ด้วยข้อความที่เป็นมิตรกับผู้อ่าน
    | เช่น "ที่อยู่อีเมล" แทน "email"
    |
    */

    'attributes' => [],
];
