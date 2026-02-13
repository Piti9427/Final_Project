<?php
// เพิ่มค่า pcre.backtrack_limit เพื่อแก้ปัญหา MpdfException
ini_set('pcre.backtrack_limit', '10000000');

session_start();
include "../users/checklogin.php";
include config_loader.php";
require_once('../vendor/autoload.php');

try {
    $conn = // Use connection from config_loader.php;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $scholarship_id = filter_input(INPUT_GET, 'scholarship_id', FILTER_VALIDATE_INT);
    $applicant_id = filter_input(INPUT_GET, 'applicant_id', FILTER_VALIDATE_INT);
    
    if (!$scholarship_id || !$applicant_id) {
        echo "กรุณาระบุ ID ที่ถูกต้อง";
        exit();
    }
    
    // ดึงข้อมูลจาก scholarship_applications และแปลง user_no เป็น branch_no
    $sql = "SELECT sa.*, 
                   COALESCE(a.branch_no, 'ไม่มีสาขา') AS branch_no
            FROM scholarship_applications sa
            LEFT JOIN authorize a ON sa.user_no = a.user_no
            WHERE sa.scholarship_id = :scholarship_id 
            AND sa.id = :applicant_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':scholarship_id', $scholarship_id, PDO::PARAM_INT);
    $stmt->bindParam(':applicant_id', $applicant_id, PDO::PARAM_INT);
    $stmt->execute();
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$application) {
        echo "ไม่พบข้อมูลผู้สมัครที่ต้องการ";
        exit();
    }
    
    $mpdf = new \Mpdf\Mpdf([
        'default_font_size' => 16,
        'default_font' => 'sarabun',
        'tempDir' => sys_get_temp_dir()
    ]);
    $templatePath = __DIR__ . '/../pdf_template/pdf07.pdf';

    // นับจำนวนหน้าของ PDF
    $pageCount = $mpdf->SetSourceFile($templatePath);
    
    for ($i = 1; $i <= $pageCount; $i++) {
        $mpdf->AddPage();
        $mpdf->UseTemplate($mpdf->ImportPage($i));
        

////////// กำหนดข้อมูลหน้าแรก
        if ($i == 1) {
            $mpdf->SetFont('sarabun', '', 14);


            //รูปอัพโหลด 1 นิ้ว
            // กำหนดพาธของรูปภาพ
            $imagePath = 'uploads/' . $application['student_photo'];

            // ขนาดกรอบที่ต้องการให้รูปพอดี (เปลี่ยนค่าตามกรอบของคุณ)
            $frameWidth = 20;  // กว้าง
            $frameHeight = 25; // สูง
            $x = 163;  // ตำแหน่ง X ของกรอบ
            $y = 15;   // ตำแหน่ง Y ของกรอบ

            // ตรวจสอบว่ารูปมีอยู่จริง
            if (file_exists($imagePath)) {
                // แทรกรูปภาพให้พอดีกับกรอบ
                $mpdf->Image($imagePath, $x, $y, $frameWidth, $frameHeight, 'jpg', '', true, false);
            } else {
                // ถ้ารูปไม่มี → แสดงกรอบเปล่า
                $mpdf->SetXY($x, $y);
                $mpdf->Cell($frameWidth, $frameHeight, '', 1, 1, 'C'); // วาดกรอบสี่เหลี่ยม
                $mpdf->SetXY($x, $y + ($frameHeight / 2) - 5);
                $mpdf->Cell($frameWidth, 10, 'ไม่พบรูปภาพ', 0, 1, 'C');
            }

            // คำนำหน้าไทย
            $mpdf->SetXY(24, 69);
            $mpdf->Cell(100, 10, $application['prefix_th'], 0, 1, 'L');

            // ชื่อไทย
            $mpdf->SetXY(81, 69);
            $mpdf->Cell(100, 10, $application['first_name_th'], 0, 1, 'L');

            // นามสกุลไทย
            $mpdf->SetXY(138, 69);
            $mpdf->Cell(100, 10, $application['last_name_th'], 0, 1, 'L');

            // คำนำหน้าอังกฤษ
            $mpdf->SetXY(24, 84);
            $mpdf->Cell(100, 10, $application['prefix_en'], 0, 1, 'L');

            // ชื่ออังกฤษ
            $mpdf->SetXY(81, 84);
            $mpdf->Cell(100, 10, $application['first_name_en'], 0, 1, 'L');

            // นามสกุลอังกฤษ
            $mpdf->SetXY(138, 84);
            $mpdf->Cell(100, 10, $application['last_name_en'], 0, 1, 'L');

            // คณะ
            $mpdf->SetXY(24, 99);
            $mpdf->Cell(100, 10, $application['faculty'], 0, 1, 'L');

            // **เปลี่ยนจาก user_no เป็น branch_no**
            $mpdf->SetXY(81, 99);
            $mpdf->Cell(100, 10, $application['branch_no'], 0, 1, 'L');

            // ชั้นปี
            $mpdf->SetXY(138, 99);
            $mpdf->Cell(100, 10, $application['year_level'], 0, 1, 'L');

            //รหัสประจำตัวนึกศึกษา
            $mpdf->SetXY(24, 114);
            $mpdf->Cell(100, 10, $application['student_id'], 0, 1, 'L');

            //เกรดเฉลี่ย
            $mpdf->SetXY(110, 114);
            $mpdf->Cell(100, 10, $application['gpa'], 0, 1, 'L');

            //สถานที่เกิด
            $mpdf->SetXY(24, 128);
            $mpdf->Cell(100, 10, $application['birth_place'], 0, 1, 'L');

            //วันเดือนปีเกิด
            $mpdf->SetXY(81, 128);
            $mpdf->Cell(100, 10, $application['birth_date'], 0, 1, 'L');

            //อายุ
            $mpdf->SetXY(138, 128);
            $mpdf->Cell(100, 10, $application['age'], 0, 1, 'L');

            //ศาสนา
            $mpdf->SetXY(167, 128);
            $mpdf->Cell(100, 10, $application['religion'], 0, 1, 'L');


            //[ ที่อยู่ตามทะเบียนบ้าน ]
            //บ้านเลขที่
            $mpdf->SetXY(24, 158);
            $mpdf->Cell(100, 10, $application['permanent_house_no'], 0, 1, 'L');

            //หมู่ที่
            $mpdf->SetXY(81, 158);
            $mpdf->Cell(100, 10, $application['permanent_moo'], 0, 1, 'L');

            //ถนน
            $mpdf->SetXY(138, 158);
            $mpdf->Cell(100, 10, $application['permanent_road'], 0, 1, 'L');

            //ตำบล/แขวง
            $mpdf->SetXY(24, 173);
            $mpdf->Cell(100, 10, $application['permanent_subdistrict'], 0, 1, 'L');

            //อำเภอ/เขต
            $mpdf->SetXY(81, 173);
            $mpdf->Cell(100, 10, $application['permanent_district'], 0, 1, 'L');

            //จังหวัด
            $mpdf->SetXY(138, 173);
            $mpdf->Cell(100, 10, $application['permanent_province'], 0, 1, 'L');

            //รหัสไปรษณีย์
            $mpdf->SetXY(24, 188);
            $mpdf->Cell(100, 10, $application['permanent_postal_code'], 0, 1, 'L');

            //โทรศัพท์(บ้าน)
            $mpdf->SetXY(81, 188);
            $mpdf->Cell(100, 10, $application['permanent_phone'], 0, 1, 'L');

            //โทรศัพท์(มือถือ)
            $mpdf->SetXY(138, 188);
            $mpdf->Cell(100, 10, $application['permanent_mobile'], 0, 1, 'L');


            //[ ที่อยู่ปัจจุบัน ]
            //ประเภทที่พัก
            $mpdf->SetXY(24, 218);
            $mpdf->Cell(100, 10, $application['current_residence_type'], 0, 1, 'L');

            //อาคาร
            $mpdf->SetXY(81, 218);
            $mpdf->Cell(100, 10, $application['current_building'], 0, 1, 'L');

            //หมายเลขห้องพัก
            $mpdf->SetXY(138, 218);
            $mpdf->Cell(100, 10, $application['current_room_no'], 0, 1, 'L');

            //ที่อยู่เลขที่
            $mpdf->SetXY(24, 233);
            $mpdf->Cell(100, 10, $application['current_house_no'], 0, 1, 'L');

            //หมู่ที่
            $mpdf->SetXY(81, 233);
            $mpdf->Cell(100, 10, $application['current_moo'], 0, 1, 'L');

            //ถนน
            $mpdf->SetXY(138, 233);
            $mpdf->Cell(100, 10, $application['current_road'], 0, 1, 'L');
            
            //ตำบล/แขวง
            $mpdf->SetXY(24, 247);
            $mpdf->Cell(100, 10, $application['current_subdistrict'], 0, 1, 'L');

            //อำเภอ/เขต
            $mpdf->SetXY(81, 247);
            $mpdf->Cell(100, 10, $application['current_district'], 0, 1, 'L');

            //จังหวัด
            $mpdf->SetXY(138, 247);
            $mpdf->Cell(100, 10, $application['current_province'], 0, 1, 'L');

            //รหัสไปรษณีย์
            $mpdf->SetXY(24, 262);
            $mpdf->Cell(100, 10, $application['current_postal_code'], 0, 1, 'L');

            //โทรศัพท์
            $mpdf->SetXY(110, 262);
            $mpdf->Cell(100, 10, $application['current_phone'], 0, 1, 'L');



        }
        
////////// ข้อมูลที่อยู่ในหน้าที่ 2
        if ($i == 2) {
            $mpdf->SetFont('sarabun', '', 14);


        //[ รายได้ ]   
        // กำหนดตำแหน่งของปุ่มเลือกได้รับเงินจากบิดา/มารดา
        $x_positions = [
            'รายวัน' => 21,
            'รายสัปดาห์' => 35,
            'รายเดือน' => 54
        ];
        $y_position = 17;

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['parent_allowance_type']) ? $application['parent_allowance_type'] : '';

        // กำหนดสีฟ้า (RGB)
        $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
        $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

        foreach ($x_positions as $type => $x) {
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
            $mpdf->SetXY($x, $y_position);
            $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }


        // กำหนดตำแหน่งของปุ่มเลือกได้รับเงินจากผู้อุปการะนอกเหนือจากบิดามารดา
        $x_positions = [
            'รายวัน' => 106,
            'รายสัปดาห์' => 120,
            'รายเดือน' => 140
        ];
        $y_position = 17;

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['other_allowance_type']) ? $application['other_allowance_type'] : '';

        // กำหนดสีฟ้า (RGB)
        $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
        $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

        foreach ($x_positions as $type => $x) {
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
            $mpdf->SetXY($x, $y_position);
            $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }

        //จำนวนเงินที่ได้รับจากบิดา/มารดา
        $mpdf->SetXY(24, 21);
        $mpdf->Cell(100, 10, $application['parent_allowance_amount'], 0, 1, 'L');

        //จำนวนเงินที่ได้รับจากผู้อุปการะนอกเหนือจากบิดา/มารดา
        $mpdf->SetXY(110, 21);
        $mpdf->Cell(100, 10, $application['other_allowance_amount'], 0, 1, 'L');

        //ได้รับเงินจากกองทุนเงินให้กู้ยืมเพื่อการศึกษา
        $mpdf->SetXY(24, 36);
        $mpdf->Cell(100, 10, $application['loan_amount'], 0, 1, 'L');

        //มีรายได้พิเศษ
        $mpdf->SetXY(110, 36);
        $mpdf->Cell(100, 10, $application['extra_income_daily'], 0, 1, 'L');

        //โดยได้รับจาก
        $mpdf->SetXY(152, 36);
        $mpdf->Cell(100, 10, $application['extra_income_source'], 0, 1, 'L');


        // [ รายจ่าย ]   
        //ค่าอาหาร
        $mpdf->SetXY(24, 66);
        $mpdf->Cell(100, 10, $application['food_expense_daily'], 0, 1, 'L');

        //ค่าที่พัก
        $mpdf->SetXY(110, 66);
        $mpdf->Cell(100, 10, $application['accommodation_expense'], 0, 1, 'L');

        //การเดินทางจากที่พักถึงมหาวิทยาลัยฯ โดย
        $mpdf->SetXY(24, 81);
        $mpdf->Cell(100, 10, $application['transportation_method'], 0, 1, 'L');

        //ค่าใช้จ่ายในการเดินทางระหว่างที่พักถึงสถานที่เรียน (ถ้ามี)
        $mpdf->SetXY(110, 81);
        $mpdf->Cell(100, 10, $application['transportation_expense_daily'], 0, 1, 'L');

        //ค่าอุปกรณ์การเรียน / ตำราเรียน
        $mpdf->SetXY(24, 96);
        $mpdf->Cell(100, 10, $application['education_supplies_expense'], 0, 1, 'L');

        //ค่าใช้จ่ายอื่น ๆ
        $mpdf->SetXY(110, 96);
        $mpdf->Cell(100, 10, $application['other_expense_detail'], 0, 1, 'L');

        //บาทต่อเดือน
        $mpdf->SetXY(152, 96);
        $mpdf->Cell(100, 10, $application['other_expense_amount'], 0, 1, 'L');

        //ประมาณการค้าใช้จ่ายที่นักศึกษาคาดว่าจะเพียงพอสำหรับตนเอง
        $mpdf->SetXY(24, 111);
        $mpdf->Cell(100, 10, $application['estimated_monthly_expense'], 0, 1, 'L');

        //[ สภาพความเป็นอยู่ของผู้ขอทุน ]
        // ตัวเลือกที่เลือก
        $x_positions = [
            'อยู่กับบิดามารดา' => 21,
            'อยู่กับบิดา' => 46,
            'อยู่กับมารดา' => 64,
            'อยู่กับผู้อุปการะ' => 84,
            'อยู่หอพัก / วัด' => 108
        ];
        $y_position = 136;

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['living_conditions_grantees']) ? $application['living_conditions_grantees'] : '';

        // กำหนดสีฟ้า (RGB)
        $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
        $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

        foreach ($x_positions as $type => $x) {
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
            $mpdf->SetXY($x, $y_position);
            $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }

        //เกี่ยวข้องกับผู้อุปการะ [ถ้าเลือก]
        $mpdf->SetXY(133, 134);
        $mpdf->Cell(100, 10, $application['relationship_benefactors'], 0, 1, 'L');

        //ถ้าเลือกตัวเลือก หอพัก / วัด กรุณากรอกข้อมูลด้านล่างนี้
        //ชื่อ
        $mpdf->SetXY(24, 160);
        $mpdf->Cell(100, 10, $application['dormitorytemple'], 0, 1, 'L');

        //ห้อง
        $mpdf->SetXY(110, 160);
        $mpdf->Cell(100, 10, $application['dormitorytemple_room'], 0, 1, 'L');

        //สถานที่ติดต่อ
        $mpdf->SetXY(24, 177);
        $mpdf->Cell(100, 10, $application['dormitorytemple_contact'], 0, 1, 'L');

        //เบอร์โทรศัพท์
        $mpdf->SetXY(110, 177);
        $mpdf->Cell(100, 10, $application['dormitorytemple_phone'], 0, 1, 'L');

        // [ ค่าใช้จ่ายด้านที่พัก ]
        //ตัวเลือก
        $x_positions = [
            'ไม่เสียค่าที่พัก' => 22,
            'ค่าหอพัก / ค่าเช่าบ้าน' => 45
        ];
        $y_position = 203;

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['expense_select']) ? $application['expense_select'] : '';

        // กำหนดสีฟ้า (RGB)
        $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
        $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

        foreach ($x_positions as $type => $x) {
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
            $mpdf->SetXY($x, $y_position);
            $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }

           
        //บาท / เดือน
        $mpdf->SetXY(80, 201);
        $mpdf->Cell(100, 10, $application['dormitoryhouse_fee'], 0, 1, 'L');

        // [ ประเภทการจ่าย ]
        //ตัวเลือก
        $x_positions = [
            'จ่ายคนเดียว' =>132,
            'ร่วมกับผู้อื่น' => 154
        ];
        $y_position = 203;

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['payment_type']) ? $application['payment_type'] : '';

        // กำหนดสีฟ้า (RGB)
        $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
        $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

        foreach ($x_positions as $type => $x) {
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
            $mpdf->SetXY($x, $y_position);
            $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }

        // [ ประเภทการจ่าย ]
        //ตัวเลือก
        $x_positions = [
            'ได้รับทุนกู้ยืมรัฐบาล (กยศ.) (ปิดสูตร) ปีการศึกษา' =>22,
            'ไม่ได้กู้ยืม' => 117
        ];
        $y_position = 212;

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['scholarship_status']) ? $application['scholarship_status'] : '';

        // กำหนดสีฟ้า (RGB)
        $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
        $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

        foreach ($x_positions as $type => $x) {
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
            $mpdf->SetXY($x, $y_position);
            $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }

        //บาท / ปี
        $mpdf->SetXY(86, 210);
        $mpdf->Cell(100, 10, $application['scholarship_amount'], 0, 1, 'L');

        //[ ประวัติการรับทุนการศึกษา ]
        //ตัวเลือก   
        $x_positions = [
            'เคยได้รับทุนการศึกษา' =>23,
            'ไม่เคยได้รับทุนการศึกษา' => 53
        ];
        $y_position = 233;

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['historycholarship_status']) ? $application['historycholarship_status'] : '';

        // กำหนดสีฟ้า (RGB)
        $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
        $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

        foreach ($x_positions as $type => $x) {
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
            $mpdf->SetXY($x, $y_position);
            $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        } 


        //มัธยมปลาย
        $mpdf->SetXY(49,254);
        $mpdf->Cell(100, 10, $application['senior_high_school'], 0, 1, 'L');
        //จำนวนเงินมัธยมปลาย
        $mpdf->SetXY(93,254);
        $mpdf->Cell(100, 10, $application['senior_high_school_amount'], 0, 1, 'L');
        //ตัวเลือก   
        // กำหนดตำแหน่ง X และ Y ของแต่ละปุ่ม
        $positions = [
            'ต่อเนื่อง' => ['x' => 128, 'y' => 255],
            'เฉพาะปี' => ['x' => 144, 'y' => 255],
            'ไม่ผูกพัน' => ['x' => 159, 'y' => 255],
            'ผูกพัน' => ['x' => 128, 'y' => 259]
        ];

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['landstatus']) ? $application['landstatus'] : '';

        foreach ($positions as $type => $pos) {
            $x = $pos['x'];
            $y = $pos['y'];
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → ขอบสีฟ้า, ถ้าไม่เลือก → ขอบสีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // **วาดวงกลมปุ่มเลือก**
            $mpdf->SetXY($x, $y);
            $mpdf->Circle($x + 3.5, $y + 3.5, 1.5, 'D'); // ขอบวงกลม

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y + 3.5, 0.8, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }
    }    


////////// ข้อมูลที่อยู่ในหน้าที่ 3
        if ($i == 3) {
            $mpdf->SetFont('sarabun', '', 14);



        //อุดมศึกษาปีที่ 1
        $mpdf->SetXY(49,13);
        $mpdf->Cell(100, 10, $application['one_years'], 0, 1, 'L');
        //จำนวนเงินอุดมศึกษาปีที่ 1
        $mpdf->SetXY(93,13);
        $mpdf->Cell(100, 10, $application['one_years_amount'], 0, 1, 'L');
        //ตัวเลือก   
        // กำหนดตำแหน่ง X และ Y ของแต่ละปุ่ม
        $positions = [
            'ต่อเนื่อง' => ['x' => 128, 'y' => 13],
            'เฉพาะปี' => ['x' => 144, 'y' => 13],
            'ไม่ผูกพัน' => ['x' => 159, 'y' => 13],
            'ผูกพัน' => ['x' => 128, 'y' => 17]
        ];
        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['landstatus1']) ? $application['landstatus1'] : '';

        foreach ($positions as $type => $pos) {
            $x = $pos['x'];
            $y = $pos['y'];
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → ขอบสีฟ้า, ถ้าไม่เลือก → ขอบสีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // **วาดวงกลมปุ่มเลือก**
            $mpdf->SetXY($x, $y);
            $mpdf->Circle($x + 3.5, $y + 3.5, 1.5, 'D'); // ขอบวงกลม

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y + 3.5, 0.8, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }    

        //อุดมศึกษาปีที่ 2
        $mpdf->SetXY(49,26);
        $mpdf->Cell(100, 10, $application['two_years'], 0, 1, 'L');
        //จำนวนเงินอุดมศึกษาปีที่ 2
        $mpdf->SetXY(93,26);
        $mpdf->Cell(100, 10, $application['two_years_amount'], 0, 1, 'L');
        //ตัวเลือก   
        // กำหนดตำแหน่ง X และ Y ของแต่ละปุ่ม
        $positions = [
            'ต่อเนื่อง' => ['x' => 128, 'y' => 26],
            'เฉพาะปี' => ['x' => 144, 'y' => 26],
            'ไม่ผูกพัน' => ['x' => 159, 'y' => 26],
            'ผูกพัน' => ['x' => 128, 'y' => 30]
        ];
        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['landstatus2']) ? $application['landstatus2'] : '';

        foreach ($positions as $type => $pos) {
            $x = $pos['x'];
            $y = $pos['y'];
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → ขอบสีฟ้า, ถ้าไม่เลือก → ขอบสีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // **วาดวงกลมปุ่มเลือก**
            $mpdf->SetXY($x, $y);
            $mpdf->Circle($x + 3.5, $y + 3.5, 1.5, 'D'); // ขอบวงกลม

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y + 3.5, 0.8, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }    

        //อุดมศึกษาปีที่ 3
        $mpdf->SetXY(49,39);
        $mpdf->Cell(100, 10, $application['three_years'], 0, 1, 'L');
        //จำนวนเงินอุดมศึกษาปีที่ 3
        $mpdf->SetXY(93,39);
        $mpdf->Cell(100, 10, $application['three_years_amount'], 0, 1, 'L');
        //ตัวเลือก   
        // กำหนดตำแหน่ง X และ Y ของแต่ละปุ่ม
        $positions = [
            'ต่อเนื่อง' => ['x' => 128, 'y' => 39],
            'เฉพาะปี' => ['x' => 144, 'y' => 39],
            'ไม่ผูกพัน' => ['x' => 159, 'y' => 39],
            'ผูกพัน' => ['x' => 128, 'y' => 43]
        ];
        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['landstatus3']) ? $application['landstatus3'] : '';

        foreach ($positions as $type => $pos) {
            $x = $pos['x'];
            $y = $pos['y'];
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → ขอบสีฟ้า, ถ้าไม่เลือก → ขอบสีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // **วาดวงกลมปุ่มเลือก**
            $mpdf->SetXY($x, $y);
            $mpdf->Circle($x + 3.5, $y + 3.5, 1.5, 'D'); // ขอบวงกลม

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y + 3.5, 0.8, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }    

        //อุดมศึกษาปีที่ 4
        $mpdf->SetXY(49,52);
        $mpdf->Cell(100, 10, $application['four_years'], 0, 1, 'L');
        //จำนวนเงินอุดมศึกษาปีที่ 4
        $mpdf->SetXY(93,52);
        $mpdf->Cell(100, 10, $application['four_years_amount'], 0, 1, 'L');
        //ตัวเลือก   
        // กำหนดตำแหน่ง X และ Y ของแต่ละปุ่ม
        $positions = [
            'ต่อเนื่อง' => ['x' => 128, 'y' => 52],
            'เฉพาะปี' => ['x' => 144, 'y' => 52],
            'ไม่ผูกพัน' => ['x' => 159, 'y' => 52],
            'ผูกพัน' => ['x' => 128, 'y' => 57]
        ];
        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['landstatus4']) ? $application['landstatus4'] : '';

        foreach ($positions as $type => $pos) {
            $x = $pos['x'];
            $y = $pos['y'];
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → ขอบสีฟ้า, ถ้าไม่เลือก → ขอบสีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // **วาดวงกลมปุ่มเลือก**
            $mpdf->SetXY($x, $y);
            $mpdf->Circle($x + 3.5, $y + 3.5, 1.5, 'D'); // ขอบวงกลม

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y + 3.5, 0.8, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }    


        //อุดมศึกษาปีที่ 5
        $mpdf->SetXY(49,65);
        $mpdf->Cell(100, 10, $application['five_years'], 0, 1, 'L');
        //จำนวนเงินอุดมศึกษาปีที่ 5
        $mpdf->SetXY(93,65);
        $mpdf->Cell(100, 10, $application['five_years_amount'], 0, 1, 'L');
        //ตัวเลือก   
        // กำหนดตำแหน่ง X และ Y ของแต่ละปุ่ม
        $positions = [
            'ต่อเนื่อง' => ['x' => 128, 'y' => 65],
            'เฉพาะปี' => ['x' => 144, 'y' => 65],
            'ไม่ผูกพัน' => ['x' => 159, 'y' => 65],
            'ผูกพัน' => ['x' => 128, 'y' => 70]
        ];
        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['landstatus5']) ? $application['landstatus5'] : '';

        foreach ($positions as $type => $pos) {
            $x = $pos['x'];
            $y = $pos['y'];
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → ขอบสีฟ้า, ถ้าไม่เลือก → ขอบสีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // **วาดวงกลมปุ่มเลือก**
            $mpdf->SetXY($x, $y);
            $mpdf->Circle($x + 3.5, $y + 3.5, 1.5, 'D'); // ขอบวงกลม

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y + 3.5, 0.8, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }


        //อุดมศึกษาปีที่ 6
        $mpdf->SetXY(49,78);
        $mpdf->Cell(100, 10, $application['six_years'], 0, 1, 'L');
        //จำนวนเงินอุดมศึกษาปีที่ 6
        $mpdf->SetXY(93,78);
        $mpdf->Cell(100, 10, $application['six_years_amount'], 0, 1, 'L');
        //ตัวเลือก   
        // กำหนดตำแหน่ง X และ Y ของแต่ละปุ่ม
        $positions = [
            'ต่อเนื่อง' => ['x' => 128, 'y' => 78],
            'เฉพาะปี' => ['x' => 144, 'y' => 78],
            'ไม่ผูกพัน' => ['x' => 159, 'y' => 78],
            'ผูกพัน' => ['x' => 128, 'y' => 83]
        ];
        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['landstatus6']) ? $application['landstatus6'] : '';

        foreach ($positions as $type => $pos) {
            $x = $pos['x'];
            $y = $pos['y'];
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → ขอบสีฟ้า, ถ้าไม่เลือก → ขอบสีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // **วาดวงกลมปุ่มเลือก**
            $mpdf->SetXY($x, $y);
            $mpdf->Circle($x + 3.5, $y + 3.5, 1.5, 'D'); // ขอบวงกลม

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y + 3.5, 0.8, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }

        //ประวัติการศึกษาโดยย่อ
        //ประถมศึกษา จากโรงเรียน
        $mpdf->SetXY(28,116);
        $mpdf->Cell(100, 10, $application['primary_school'], 0, 1, 'L');
        //ประถมศึกษา จังหวัด
        $mpdf->SetXY(137,116);
        $mpdf->Cell(100, 10, $application['primary_province'], 0, 1, 'L');


        //มัธยมศึกษาตอนต้น จากโรงเรียน
        $mpdf->SetXY(28,134);
        $mpdf->Cell(100, 10, $application['middle_school'], 0, 1, 'L');
        //มัธยมศึกษาตอนต้น จังหวัด
        $mpdf->SetXY(137,134);
        $mpdf->Cell(100, 10, $application['middle_province'], 0, 1, 'L');

        //มัธยมศึกษาตอนปลาย จากโรงเรียน
        $mpdf->SetXY(28,152);
        $mpdf->Cell(100, 10, $application['high_school'], 0, 1, 'L');
        //มัธยมศึกษาตอนปลาย จังหวัด
        $mpdf->SetXY(137,152);
        $mpdf->Cell(100, 10, $application['high_province'], 0, 1, 'L');


        //[ ข้อมูลของครอบครัวและผู้อุปการะ ]

        //ข้อมูลบิดา
        //ชื่อนามสกุล
        $mpdf->SetXY(28,200);
        $mpdf->Cell(100, 10, $application['father_fullname'], 0, 1, 'L');

        //อายุ
        $mpdf->SetXY(110,200);
        $mpdf->Cell(100, 10, $application['father_age'], 0, 1, 'L');

        //สถานะ
        //ตัวเลือก   
        $x_positions = [
            'มีชีวิต' => 149,
            'ถึงแก่กรรม' => 161
        ];
        $y_position = 201;

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['father_status']) ? $application['father_status'] : '';

        // กำหนดสีฟ้า (RGB)
        $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
        $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

        foreach ($x_positions as $type => $x) {
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
            $mpdf->SetXY($x, $y_position);
            $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }

        //ที่อยู่บ้านเลขที่ 
        $mpdf->SetXY(28,217);
        $mpdf->Cell(100, 10, $application['father_house'], 0, 1, 'L');

        //ตรอก / ซอย
        $mpdf->SetXY(110,217);
        $mpdf->Cell(100, 10, $application['father_alley'], 0, 1, 'L');

        //หมู่ที่
        $mpdf->SetXY(28,233);
        $mpdf->Cell(100, 10, $application['father_moo'], 0, 1, 'L');

        //ถนน
        $mpdf->SetXY(82,233);
        $mpdf->Cell(100, 10, $application['father_road'], 0, 1, 'L');

        //ตำบล / แขวง
        $mpdf->SetXY(137,233);
        $mpdf->Cell(100, 10, $application['father_subdistrict'], 0, 1, 'L');

        //อำเภอเ / เขต
        $mpdf->SetXY(28,249);
        $mpdf->Cell(100, 10, $application['father_district'], 0, 1, 'L');

        //จังหวัด
        $mpdf->SetXY(82,249);
        $mpdf->Cell(100, 10, $application['father_province'], 0, 1, 'L');

        //รหัสไปรษณีย์
        $mpdf->SetXY(137,249);
        $mpdf->Cell(100, 10, $application['father_post_code'], 0, 1, 'L');

        //โทรศัพท์บ้าน
        $mpdf->SetXY(28,265);
        $mpdf->Cell(100, 10, $application['father_house_no'], 0, 1, 'L');

        //โทรศัพท์มือถือ
        $mpdf->SetXY(110,265);
        $mpdf->Cell(100, 10, $application['father_phone'], 0, 1, 'L');

        }

////////// ข้อมูลที่อยู่ในหน้าที่ 4
        if ($i == 4) {
            $mpdf->SetFont('sarabun', '', 14);


        //ข้อมูลของครอบครัวและผู้อุปการะ [ต่อ]    
        //อาชีพบิดา
        $mpdf->SetXY(28,3);
        $mpdf->Cell(100, 10, $application['father_occupation'], 0, 1, 'L');

        //รายได้ต่อเดือน
        $mpdf->SetXY(110,3);
        $mpdf->Cell(100, 10, $application['father_income'], 0, 1, 'L');

        //ตำแหน่งยศ
        $mpdf->SetXY(28,19);
        $mpdf->Cell(100, 10, $application['father_rank'], 0, 1, 'L');

        //ลักษณะงาน
        $mpdf->SetXY(110,19);
        $mpdf->Cell(100, 10, $application['father_job_description'], 0, 1, 'L');

        //สถานที่ทำงานของบิดา
        $mpdf->SetXY(28,36);
        $mpdf->Cell(100, 10, $application['father_workplace'], 0, 1, 'L');

        //โทรศัพท์
        $mpdf->SetXY(110,36);
        $mpdf->Cell(100, 10, $application['father_telephone'], 0, 1, 'L');


        //ช้อมูลมารดา
        //ชื่อนามสกุล
        $mpdf->SetXY(28,64);
        $mpdf->Cell(100, 10, $application['mother_fullname'], 0, 1, 'L');
        
        //อายุ
        $mpdf->SetXY(110,64);
        $mpdf->Cell(100, 10, $application['mother_age'], 0, 1, 'L');

        //สถานะ
        //ตัวเลือก   
        $x_positions = [
            'มีชีวิต' => 149,
            'ถึงแก่กรรม' => 161
        ];
        $y_position = 65;

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['mother_status']) ? $application['mother_status'] : '';

        // กำหนดสีฟ้า (RGB)
        $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
        $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

        foreach ($x_positions as $type => $x) {
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
            $mpdf->SetXY($x, $y_position);
            $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }

        //ที่อยู่บ้านเลขที่
        $mpdf->SetXY(28,81);
        $mpdf->Cell(100, 10, $application['mother_house'], 0, 1, 'L');

        //ตรอก / ซอย
        $mpdf->SetXY(110,81);
        $mpdf->Cell(100, 10, $application['mother_ally'], 0, 1, 'L');

        //หมู่ที่
        $mpdf->SetXY(28,97);
        $mpdf->Cell(100, 10, $application['mother_moo'], 0, 1, 'L');

        //ถนน
        $mpdf->SetXY(82,97);
        $mpdf->Cell(100, 10, $application['mother_road'], 0, 1, 'L');

        //ตำบล / แขวง
        $mpdf->SetXY(137,97);
        $mpdf->Cell(100, 10, $application['mother_subdistrict'], 0, 1, 'L');

        //อำเภอเ / เขต
        $mpdf->SetXY(28,113);
        $mpdf->Cell(100, 10, $application['mother_district'], 0, 1, 'L');

        //จังหวัด
        $mpdf->SetXY(82,113);
        $mpdf->Cell(100, 10, $application['mother_province'], 0, 1, 'L');

        //รหัสไปรษณีย์
        $mpdf->SetXY(137,113);
        $mpdf->Cell(100, 10, $application['mother_postcode'], 0, 1, 'L');

        //โทรศัพท์บ้าน
        $mpdf->SetXY(28,129);
        $mpdf->Cell(100, 10, $application['mother_house_no'], 0, 1, 'L');

        //โทรศัพท์มือถือ
        $mpdf->SetXY(110,129);
        $mpdf->Cell(100, 10, $application['mother_phone'], 0, 1, 'L');

        //อาชีพมารดา
        $mpdf->SetXY(28,145);
        $mpdf->Cell(100, 10, $application['mother_occupation'], 0, 1, 'L');

        //รายได้ต่อเดือน
        $mpdf->SetXY(110,145);
        $mpdf->Cell(100, 10, $application['mother_income'], 0, 1, 'L');

        //ตำแหน่งยศ
        $mpdf->SetXY(28,161);
        $mpdf->Cell(100, 10, $application['mother_rank'], 0, 1, 'L');

        //ลักษณะงาน
        $mpdf->SetXY(110,161);
        $mpdf->Cell(100, 10, $application['mother_job_description'], 0, 1, 'L');

        //สถานที่ทำงานของมารดา
        $mpdf->SetXY(28,177);
        $mpdf->Cell(100, 10, $application['mother_workplace'], 0, 1, 'L');

        //โทรศัพท์
        $mpdf->SetXY(110,177);
        $mpdf->Cell(100, 10, $application['mother_telephone'], 0, 1, 'L');


        //[ สถานภาพครอบครัว ]
        //ตัวเลือก   
        $positions = [
            'บิดามารดาอยู่ด้วยกัน' => ['x' => 21, 'y' => 203],
            'บิดาถึงแก่กรรม' => ['x' => 51, 'y' => 203],
            'มารดาถึงแก่กรรม' => ['x' => 76, 'y' => 203],
            'บิดามารดาหย่าร้างกัน' => ['x' => 21, 'y' => 211],
            'บิดามารดาแยกกันอยู่' => ['x' => 21, 'y' => 226]
        ];
        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['familystatus']) ? $application['familystatus'] : '';

        foreach ($positions as $type => $pos) {
            $x = $pos['x'];
            $y = $pos['y'];
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → ขอบสีฟ้า, ถ้าไม่เลือก → ขอบสีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // **วาดวงกลมปุ่มเลือก**
            $mpdf->SetXY($x, $y);
            $mpdf->Circle($x + 3.5, $y + 3.5, 1.5, 'D'); // ขอบวงกลม

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y + 3.5, 0.8, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }

        //กล่องข้อความบิดาหย่าร้างกัน
        $mpdf->SetXY(25,215);
        $mpdf->Cell(100, 10, $application['benefactor'], 0, 1, 'L');

        //กล่องข้อความบิดามารดาแยกกันอยู่
        $mpdf->SetXY(25,229);
        $mpdf->Cell(100, 10, $application['living_with'], 0, 1, 'L');

        //อื่นๆ
        $mpdf->SetXY(25,243);
        $mpdf->Cell(100, 10, $application['other_familystatus'], 0, 1, 'L');

        
        //[ ที่ดินและที่อยู่อาศัยของบิดามารดา ]
        //ตัวเลือก
        $x_positions = [
            'มีที่ดินสำหรับประกอบอาชีพเป็นของตนเอง' => 21
        ];
        $y_position = 267;

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['parents_landstatus']) ? $application['parents_landstatus'] : '';

        // กำหนดสีฟ้า (RGB)
        $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
        $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

        foreach ($x_positions as $type => $x) {
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
            $mpdf->SetXY($x, $y_position);
            $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }

        //ไร่ 
        $mpdf->SetXY(77,265);
        $mpdf->Cell(100, 10, $application['ownfarm'], 0, 1, 'L');

        }



/////////// ข้อมูลที่อยู่ในหน้าที่ 5
        if ($i == 5) {
            $mpdf->SetFont('sarabun', '', 14);


        //[ ที่ดินและที่อยู่อาศัยของบิดามารดา ต่อ]    
        //ตัวเลือก
        $x_positions = [
            'เช่าที่ดินผู้อื่น' => 21
        ];
        $y_position = 0;

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['parents_landstatus']) ? $application['parents_landstatus'] : '';

        // กำหนดสีฟ้า (RGB)
        $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
        $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

        foreach ($x_positions as $type => $x) {
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
            $mpdf->SetXY($x, $y_position);
            $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }

        //เช่าที่ดินผู้อื่น ไร่
        $mpdf->SetXY(45,0);
        $mpdf->Cell(100, 10, $application['otherfarm'], 0, 1, 'L');

        //ค่าเช่าเดือนละ บาท
        $mpdf->SetXY(90,0);
        $mpdf->Cell(100, 10, $application['monthly_rent_land'], 0, 1, 'L');

        //หรือปีละ บาท
        $mpdf->SetXY(130,0);
        $mpdf->Cell(100, 10, $application['peryear_rent_land'], 0, 1, 'L');

        //อาศัยผู้อื่น
        //ตัวเลือก
        $x_positions = [
            'อาศัยผู้อื่น' => 21
        ];
        $y_position = 9;

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['parents_landstatus']) ? $application['parents_landstatus'] : '';

        // กำหนดสีฟ้า (RGB)
        $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
        $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

        foreach ($x_positions as $type => $x) {
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
            $mpdf->SetXY($x, $y_position);
            $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }
        //
        $mpdf->SetXY(43,8);
        $mpdf->Cell(100, 10, $application['liveothers_land'], 0, 1, 'L');

        //เช่าบ้านอยู่ 
        //ตัวเลือก
        $x_positions = [
            'อาศัยผู้อื่น' => 21
        ];
        $y_position = 18;

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['parents_landstatus']) ? $application['parents_landstatus'] : '';

        // กำหนดสีฟ้า (RGB)
        $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
        $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

        foreach ($x_positions as $type => $x) {
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
            $mpdf->SetXY($x, $y_position);
            $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }

        //ค่าเช่าเดือนละ บาท
        $mpdf->SetXY(59,17);
        $mpdf->Cell(100, 10, $application['renthouse_monthly_land'], 0, 1, 'L');

        //หรือปีละ บาท
        //
        $mpdf->SetXY(100,17);
        $mpdf->Cell(100, 10, $application['renthouse_peryear_land'], 0, 1, 'L');

    //[ ผู้อุปการะอื่นนอกจากบิดา/มารดา ]
        //ตัวเลือก
        $x_positions = [
            'มี' => 21,
            'ไม่มี' => 30
        ];
        $y_position = 39;

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['hasGuardian']) ? $application['hasGuardian'] : '';

        // กำหนดสีฟ้า (RGB)
        $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
        $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

        foreach ($x_positions as $type => $x) {
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
            $mpdf->SetXY($x, $y_position);
            $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }

        //ชื่อนามสกุล ผู้อุปการะ
        $mpdf->SetXY(24,51);
        $mpdf->Cell(100, 10, $application['guardian_fullname'], 0, 1, 'L');

        //อายะ
        $mpdf->SetXY(110,51);
        $mpdf->Cell(100, 10, $application['guardian_age'], 0, 1, 'L');

        //มีความเกี่ยวข้องเป็น
        $mpdf->SetXY(140,51);
        $mpdf->Cell(100, 10, $application['guardian_relevant'], 0, 1, 'L');

        //บ้านเลขที่
        $mpdf->SetXY(24,66);
        $mpdf->Cell(100, 10, $application['guardian_house'], 0, 1, 'L');

        //ตรอก / ซอย
        $mpdf->SetXY(67,66);
        $mpdf->Cell(100, 10, $application['guardian_ally'], 0, 1, 'L');

        //หมู่ที่
        $mpdf->SetXY(110,66);
        $mpdf->Cell(100, 10, $application['guardian_moo'], 0, 1, 'L');

        //ถนน
        $mpdf->SetXY(153,66);
        $mpdf->Cell(100, 10, $application['guardian_road'], 0, 1, 'L');
        
        //ตำบล / แขวง
        $mpdf->SetXY(24,80);
        $mpdf->Cell(100, 10, $application['guardian_subdistrict'], 0, 1, 'L');

        //อำเภอเ / เขต
        $mpdf->SetXY(67,80);
        $mpdf->Cell(100, 10, $application['guardian_district'], 0, 1, 'L');

        //จังหวัด
        $mpdf->SetXY(110,80);
        $mpdf->Cell(100, 10, $application['guardian_province'], 0, 1, 'L');

        //รหัสไปรษณีย์
        $mpdf->SetXY(153,80);
        $mpdf->Cell(100, 10, $application['guardian_postcode'], 0, 1, 'L');

        //โทรศัพท์บ้าน
        $mpdf->SetXY(24,95);
        $mpdf->Cell(100, 10, $application['guardian_house_no'], 0, 1, 'L');

        //โทรศัพท์มือถือ
        $mpdf->SetXY(110,95);
        $mpdf->Cell(100, 10, $application['guardian_phone'], 0, 1, 'L');

        //สถานภาพ
        //ตัวเลือก
        $x_positions = [
            'โสด' => 21,
            'สมรส' => 33
        ];
        $y_position = 111;

        // ดึงค่าที่เลือกจากฐานข้อมูล
        $selectedAllowanceType = !empty($application['guardian_status']) ? $application['guardian_status'] : '';

        // กำหนดสีฟ้า (RGB)
        $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
        $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

        foreach ($x_positions as $type => $x) {
            $isSelected = ($selectedAllowanceType === $type);

            // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
            $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
            $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

            // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
            $mpdf->SetXY($x, $y_position);
            $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

            // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
            if ($isSelected) {
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
            }
        }
        
        //มีบุตร (คน)
        $mpdf->SetXY(24,122);
        $mpdf->Cell(100, 10, $application['guardian_children'], 0, 1, 'L');

        //กำลังศึกษา (คน)
        $mpdf->SetXY(83,122);
        $mpdf->Cell(100, 10, $application['guardian_children_studying'], 0, 1, 'L');

        //ประกอบอาชีพ (คน)
        $mpdf->SetXY(140,122);
        $mpdf->Cell(140, 10, $application['guardian_children_occupation'], 0, 1, 'L');

        //อาชีพผู้อุปการะ
        $mpdf->SetXY(24,137);
        $mpdf->Cell(140, 10, $application['guardian_occupation'], 0, 1, 'L');

        //รายได้เดือนละ (บาท)
        $mpdf->SetXY(110,137);
        $mpdf->Cell(140, 10, $application['guardian_monthly_income'], 0, 1, 'L');

        //ตำแหน่งยศ
        $mpdf->SetXY(24,152);
        $mpdf->Cell(140, 10, $application['guardian_rank'], 0, 1, 'L');

        //ลักษณะงาน
        $mpdf->SetXY(110,152);
        $mpdf->Cell(140, 10, $application['guardian_job_description'], 0, 1, 'L');

        //สถานที่ทำงาน
        $mpdf->SetXY(24,167);
        $mpdf->Cell(140, 10, $application['guardian_workplace'], 0, 1, 'L');

        //โทรศัพท์
        $mpdf->SetXY(110,167);
        $mpdf->Cell(140, 10, $application['guardian_telephone'], 0, 1, 'L');


    //[ ข้อมูลการศึกษาและอาชีพพี่น้องของผู้ขอทุน ]
        //ผู้ขอทุน มีพี่ – น้อง (รวมผู้ขอทุน) จำนวน   คน
        $mpdf->SetXY(72,188);
        $mpdf->Cell(140, 10, $application['sibling_amount'], 0, 1, 'L');

        //และผู้ขอทุนเป็นบุตรคนที่   ของครอบครัว
        $mpdf->SetXY(139,188);
        $mpdf->Cell(140, 10, $application['sibling_child_amount'], 0, 1, 'L');

    //กรอกรายละเอียดพี่น้อง (เรียงตามลำดับมากไปน้อย) รวมทั้งผู้ขอทุนด้วย
    //คนที่   1
        //ชื่อสกุล
        $mpdf->SetXY(37,225);
        $mpdf->Cell(140, 10, $application['sibling_fullname_one'], 0, 1, 'L');

        //อายุ
        $mpdf->SetXY(57,225);
        $mpdf->Cell(140, 10, $application['sibling_age_one'], 0, 1, 'L');

        //สถานศึกษา
        $mpdf->SetXY(69,225);
        $mpdf->Cell(140, 10, $application['sibling_education_one'], 0, 1, 'L');

        //ระดับชั้น
        $mpdf->SetXY(90,225);
        $mpdf->Cell(140, 10, $application['sibling_grade_level_one'], 0, 1, 'L');

        //อาชีพ
        $mpdf->SetXY(110,225);
        $mpdf->Cell(140, 10, $application['sibling_occupation_one'], 0, 1, 'L');

        //รายได้ต่อเดือน
        $mpdf->SetXY(130,225);
        $mpdf->Cell(140, 10, $application['sibling_monthly_income_one'], 0, 1, 'L');

        //สถานภาพ สมรส
        $mpdf->SetXY(150,225);
        $mpdf->Cell(140, 10, $application['sibling_status_one'], 0, 1, 'L');

        //จำนวนบุตร
        $mpdf->SetXY(173,225);
        $mpdf->Cell(140, 10, $application['sibling_children_amount_one'], 0, 1, 'L');


    //คนที่   2
        //ชื่อสกุล
        $mpdf->SetXY(37,235);
        $mpdf->Cell(140, 10, $application['sibling_fullname_two'], 0, 1, 'L');

        //อายุ
        $mpdf->SetXY(57,235);
        $mpdf->Cell(140, 10, $application['sibling_age_two'], 0, 1, 'L');

        //สถานศึกษา
        $mpdf->SetXY(69,235);
        $mpdf->Cell(140, 10, $application['sibling_education_two'], 0, 1, 'L');

        //ระดับชั้น
        $mpdf->SetXY(90,235);
        $mpdf->Cell(140, 10, $application['sibling_grade_level_two'], 0, 1, 'L');

        //อาชีพ
        $mpdf->SetXY(110,235);
        $mpdf->Cell(140, 10, $application['sibling_occupation_two'], 0, 1, 'L');
        
        //รายได้ต่อเดือน
        $mpdf->SetXY(130,235);
        $mpdf->Cell(140, 10, $application['sibling_monthly_income_two'], 0, 1, 'L');

        //สถานภาพ สมรส
        $mpdf->SetXY(150,235);
        $mpdf->Cell(140, 10, $application['sibling_status_two'], 0, 1, 'L');
        
        //จำนวนบุตร
        $mpdf->SetXY(173,235);
        $mpdf->Cell(140, 10, $application['sibling_children_amount_two'], 0, 1, 'L');


    //คนที่   3
        //ชื่อสกุล
        $mpdf->SetXY(37,244);
        $mpdf->Cell(140, 10, $application['sibling_fullname_three'], 0, 1, 'L');

        //อายุ
        $mpdf->SetXY(57,244);
        $mpdf->Cell(140, 10, $application['sibling_age_three'], 0, 1, 'L');

        //สถานศึกษา
        $mpdf->SetXY(69,244);
        $mpdf->Cell(140, 10, $application['sibling_education_three'], 0, 1, 'L');

        //ระดับชั้น
        $mpdf->SetXY(90,244);
        $mpdf->Cell(140, 10, $application['sibling_grade_level_three'], 0, 1, 'L');

        //อาชีพ
        $mpdf->SetXY(110,244);
        $mpdf->Cell(140, 10, $application['sibling_occupation_three'], 0, 1, 'L');
        
        //รายได้ต่อเดือน
        $mpdf->SetXY(130,244);
        $mpdf->Cell(140, 10, $application['sibling_monthly_income_three'], 0, 1, 'L');

        //สถานภาพ สมรส
        $mpdf->SetXY(150,244);
        $mpdf->Cell(140, 10, $application['sibling_status_three'], 0, 1, 'L');
        
        //จำนวนบุตร
        $mpdf->SetXY(173,244);
        $mpdf->Cell(140, 10, $application['sibling_children_amount_three'], 0, 1, 'L');
        
    //คนที่   4
        //ชื่อสกุล
        $mpdf->SetXY(37,254);
        $mpdf->Cell(140, 10, $application['sibling_fullname_four'], 0, 1, 'L');

        //อายุ
        $mpdf->SetXY(57,254);
        $mpdf->Cell(140, 10, $application['sibling_age_four'], 0, 1, 'L');

        //สถานศึกษา
        $mpdf->SetXY(69,254);
        $mpdf->Cell(140, 10, $application['sibling_education_four'], 0, 1, 'L');

        //ระดับชั้น
        $mpdf->SetXY(90,254);
        $mpdf->Cell(140, 10, $application['sibling_grade_level_four'], 0, 1, 'L');

        //อาชีพ
        $mpdf->SetXY(110,254);
        $mpdf->Cell(140, 10, $application['sibling_occupation_four'], 0, 1, 'L');
        
        //รายได้ต่อเดือน
        $mpdf->SetXY(130,254);
        $mpdf->Cell(140, 10, $application['sibling_monthly_income_four'], 0, 1, 'L');

        //สถานภาพ สมรส
        $mpdf->SetXY(150,254);
        $mpdf->Cell(140, 10, $application['sibling_status_four'], 0, 1, 'L');
        
        //จำนวนบุตร
        $mpdf->SetXY(173,254);
        $mpdf->Cell(140, 10, $application['sibling_children_amount_four'], 0, 1, 'L');

        //ขณะนี้มีบุตรที่อยู่ในความอุปการะของบิดา และ/หรือ มารดา จำนวน
        $mpdf->SetXY(100,268);
        $mpdf->Cell(140, 10, $application['sibling_currently_children'], 0, 1, 'L');

        }
        
        
////////// ข้อมูลที่อยู่ในหน้าที่ 6
        if ($i == 6) {
            $mpdf->SetFont('sarabun', '', 14);

            //ครอบครัวประสบปัญหาขาดแคลนเงินอย่างไร
            $mpdf->SetXY(24,3);
            $mpdf->Cell(140, 10, $application['sibling_financial_problems'], 0, 1, 'L');

            //และแก้ไขปัญหาโดยวิธีการใดเมื่อขาดเงิน
            $mpdf->SetXY(24,28);
            $mpdf->Cell(140, 10, $application['sibling_financial_problems'], 0, 1, 'L');


            //ความจำเป็นที่ต้องขอรับทุนการศึกษา
            $mpdf->SetXY(24,51);
            $mpdf->Cell(140, 10, $application['sibling_financial_problems'], 0, 1, 'L');

            //ประสบปัญหาอื่นๆ ปัญหาด้านสุขภาพ – โรคประจำตัว
            //ตัวเลือก
            $x_positions = [
                'ไม่มี' => 21,
                'มี' => 33
            ];
            $y_position = 73;

            // ดึงค่าที่เลือกจากฐานข้อมูล
            $selectedAllowanceType = !empty($application['healthIssue']) ? $application['healthIssue'] : '';

            // กำหนดสีฟ้า (RGB)
            $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
            $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

            foreach ($x_positions as $type => $x) {
                $isSelected = ($selectedAllowanceType === $type);

                // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
                $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
                $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

                // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
                $mpdf->SetXY($x, $y_position);
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

                // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
                if ($isSelected) {
                    $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
                }
            }
            //
            $mpdf->SetXY(24,78);
            $mpdf->Cell(140, 10, $application['healthIssueDescription'], 0, 1, 'L');

            //ปัญหาด้านอื่นๆ ที่เป็นอุปสรรคต่อการเรียน
            $mpdf->SetXY(24,93);
            $mpdf->Cell(140, 10, $application['studyProblems'], 0, 1, 'L');

            //ปัญหาครอบครัว
            $mpdf->SetXY(24,116);
            $mpdf->Cell(140, 10, $application['familyProblems'], 0, 1, 'L');

            //งานพิเศษที่ทำอยู่
            $mpdf->SetXY(24,139);
            $mpdf->Cell(140, 10, $application['parttime_job'], 0, 1, 'L');

            //รายได้  บาท/
            $mpdf->SetXY(110,139);
            $mpdf->Cell(140, 10, $application['parttime_income'], 0, 1, 'L');

            //วัน / สัปดาป์ / เดือน / ปี
            $mpdf->SetXY(154,139);
            $mpdf->Cell(140, 10, $application['parttime_income_period'], 0, 1, 'L');

            //มีความสามารถพิเศษอะไรบ้าง ระบุ
            $mpdf->SetXY(24,156);
            $mpdf->Cell(140, 10, $application['special_abilities'], 0, 1, 'L');

            //กิจกรรมที่เคยทำในสถานศึกษา
            //   1
            $mpdf->SetXY(30,183);
            $mpdf->Cell(140, 10, $application['special_activities'], 0, 1, 'L');

            //   2
            $mpdf->SetXY(30,193);
            $mpdf->Cell(140, 10, $application['special_activities1'], 0, 1, 'L');

            //   3
            $mpdf->SetXY(30,202);
            $mpdf->Cell(140, 10, $application['special_activities2'], 0, 1, 'L');

            //รางวัลทางด้านการศึกษาที่เคยได้รับ
            //   1
            $mpdf->SetXY(30,222);
            $mpdf->Cell(140, 10, $application['awards'], 0, 1, 'L');

            // ปี พศ
            $mpdf->SetXY(165,222);
            $mpdf->Cell(140, 10, $application['awards_year'], 0, 1, 'L');

            //   2
            $mpdf->SetXY(30,231);
            $mpdf->Cell(140, 10, $application['awards1'], 0, 1, 'L');

            //ปี พศ
            $mpdf->SetXY(165,231);
            $mpdf->Cell(140, 10, $application['awards_year1'], 0, 1, 'L');

            //จุดมุ่งหมายในอนาคตเมื่อจบการศึกษา
            $mpdf->SetXY(24,251);
            $mpdf->Cell(140, 10, $application['future_goals'], 0, 1, 'L');

        }    


////////// ข้อมูลที่อยู่ในหน้าที่ 7
        if ($i == 7) {
            $mpdf->SetFont('sarabun', '', 14);


            //บุคคลใกล้ชิดที่สามารถติดต่อได้กรณีเร่งด่วน    
            //ชื่อ - สกุล
            $mpdf->SetXY(24,3);
            $mpdf->Cell(140, 10, $application['emergency_contact_name'], 0, 1, 'L');

            //มีความเกี่ยวข้องเป็น
            $mpdf->SetXY(140,3);
            $mpdf->Cell(140, 10, $application['emergency_contact_relevant'], 0, 1, 'L');

            //บ้านเลขที่
            $mpdf->SetXY(24,21);
            $mpdf->Cell(140, 10, $application['emergency_contact_house'], 0, 1, 'L');

            //ตรอก / ซอย
            $mpdf->SetXY(67,21);
            $mpdf->Cell(140, 10, $application['emergency_contact_ally'], 0, 1, 'L');

            //หมู่ที่
            $mpdf->SetXY(110,21);
            $mpdf->Cell(140, 10, $application['emergency_contact_moo'], 0, 1, 'L');

            //ถนน
            $mpdf->SetXY(153,21);
            $mpdf->Cell(140, 10, $application['emergency_contact_road'], 0, 1, 'L');

            //ตำบล / แขวง
            $mpdf->SetXY(24,39);
            $mpdf->Cell(140, 10, $application['emergency_contact_subdistrict'], 0, 1, 'L');

            //อำเภอ / เขต
            $mpdf->SetXY(81,39);
            $mpdf->Cell(140, 10, $application['emergency_contact_district'], 0, 1, 'L');

            //จังหวัด
            $mpdf->SetXY(138,39);
            $mpdf->Cell(140, 10, $application['emergency_contact_province'], 0, 1, 'L');

            //รหัสไปรษณีย์
            $mpdf->SetXY(24,56);
            $mpdf->Cell(140, 10, $application['emergency_contact_postcode'], 0, 1, 'L');

            //โทรศัพท์บ้าน
            $mpdf->SetXY(81,56);
            $mpdf->Cell(140, 10, $application['emergency_contact_house_no'], 0, 1, 'L');

            //โทรศัพท์มือถือ
            $mpdf->SetXY(138,56);
            $mpdf->Cell(140, 10, $application['emergency_contact_phone'], 0, 1, 'L');

            // [ จำนวนเงินทุนที่ต้องการ ]
            //หากมหาวิทยาลัยพิจารณาให้ทุนการศึกษานักศึกษาเห็นว่าจำนวนเงินที่เหมาะสม คือ
            //ตัวเลือก
            $x_positions = [
                '3,000 บาท' => 21,
                '4,000 บาท' => 41,
                '5,000 บาท' => 61
            ];
            $y_position = 86;

            // ดึงค่าที่เลือกจากฐานข้อมูล
            $selectedAllowanceType = !empty($application['scholarship_required']) ? $application['scholarship_required'] : '';

            // กำหนดสีฟ้า (RGB)
            $mpdf->SetDrawColor(0, 102, 255);  // ขอบสีน้ำเงิน
            $mpdf->SetFillColor(4, 200, 306);  // เติมสีน้ำเงินเมื่อถูกเลือก

            foreach ($x_positions as $type => $x) {
                $isSelected = ($selectedAllowanceType === $type);

                // **ถ้าเลือก → สีน้ำเงิน, ถ้าไม่เลือก → สีดำ**
                $mpdf->SetDrawColor($isSelected ? 0 : 0, $isSelected ? 102 : 0, $isSelected ? 255 : 0);  
                $mpdf->SetFillColor(0, 102, 255); // สีฟ้าเมื่อเลือก

                // วาดวงกลมปุ่มเลือก (ขนาดเล็กลง)
                $mpdf->SetXY($x, $y_position);
                $mpdf->Circle($x + 3.5, $y_position + 3.5, 1.5, 'D'); // วงกลมขอบสีน้ำเงิน

                // ถ้าเลือกอันนี้ → เติมเต็มสีฟ้าในวงกลม
                if ($isSelected) {
                    $mpdf->Circle($x + 3.5, $y_position + 3.5, 0.5, 'DF'); // เติมเต็มวงกลมเล็ก
                }
            }

            //นักศึกษาจะนำเงินที่ได้รับไปใช้จ่ายเป็นค่าอะไรบ้าง (ระบุรายละเอียด)
            $mpdf->SetXY(24,100);
            $mpdf->Cell(140, 10, $application['scholarship_amount_description'], 0, 1, 'L');

            //ลงชื่อผู้สมัครขอรับทุน
            $mpdf->SetXY(24,167);
            $mpdf->Cell(140, 10, $application['signature_scholarship'], 0, 1, 'L');

            //ชื่อ - สกุล (ตัวบรจง)
            $mpdf->SetXY(110,167);
            $mpdf->Cell(140, 10, $application['signature_name'], 0, 1, 'L');

            //วันที่
            $mpdf->SetXY(24,185);
            $mpdf->Cell(140, 10, $application['signature_date'], 0, 1, 'L');

            //เดือน
            $mpdf->SetXY(81,185);
            $mpdf->Cell(140, 10, $application['signature_month'], 0, 1, 'L');

            //พ.ศ.
            $mpdf->SetXY(138,185);
            $mpdf->Cell(140, 10, $application['signature_year'], 0, 1, 'L');

        }
        
////////// ข้อมูลที่อยู่ในหน้าที่ 8
        if ($i == 8) {
            $mpdf->SetFont('sarabun', '', 14);


            //บรรยายประวัติ สภาพครอบครัว และเหตุผลความจำเป็นในการรับทุน
            $mpdf->SetXY(24, 0);
            $mpdf->MultiCell(140, 10, $application['describe_scholarship'], 0, 'L');

        // [ แผนที่แสดงที่อยู่ของผู้ปกครอง และแสดงสถานที่ / จุดที่ตั้งสำคัญๆ เพื่อให้สามารถเดินทางได้โดยสะดวก ]
            //อัพโหลดแผนที่หรือเอกสาร
            // กำหนดพาธของรูปภาพ
            $imagePath = 'uploads/' . $application['fileUpload1'];

            // ขนาดกรอบของรูปภาพ
            $frameWidth = 30;  // กว้าง
            $frameHeight = 30; // สูง
            $x = 150;  // ตำแหน่ง X
            $y = 130;  // ตำแหน่ง Y

            // ตรวจสอบว่ารูปมีอยู่จริง
            if (file_exists($imagePath)) {
                // แสดงรูปภาพ
                $mpdf->Image($imagePath, $x, $y, $frameWidth, $frameHeight, 'jpg', '', true, false);
            } else {
                // ถ้ารูปไม่มี → แสดงกรอบเปล่า + ข้อความแจ้งเตือน
                $mpdf->SetXY($x, $y);
                $mpdf->Cell($frameWidth, $frameHeight, '', 1, 1, 'C'); // วาดกรอบรูป
                $mpdf->SetXY($x, $y + ($frameHeight / 2) - 5);
                $mpdf->Cell($frameWidth, 10, 'ไม่พบรูปภาพ', 0, 1, 'C');
            }
            //ชื่อไฟล์ filupload 1
            $mpdf->SetXY(30,140);
            $mpdf->Cell(140, 10,"ชื่อไฟล์: ". $application['fileUpload1'], 0, 1, 'L');

            
            //จุดสังเกตุที่สำคัญ
            $mpdf->SetXY(29,173);
            $mpdf->Cell(140, 10, $application['landmarks'], 0, 1, 'L');

            //คำอธิบายเส้นทาง
            $mpdf->SetXY(110,173);
            $mpdf->Cell(140, 10, $application['directions'], 0, 1, 'L');


            //สำเนาบัตรประจำตัวนักศึกษา / V-Card 1 ฉบับ
            // กำหนดพาธของรูปภาพ
            $imagePath = 'uploads/' . $application['fileUpload2'];

            // ขนาดกรอบของรูปภาพ
            $frameWidth = 30;  // กว้าง
            $frameHeight = 30; // สูง
            $x = 150;  // ตำแหน่ง X
            $y = 210;  // ตำแหน่ง Y

            // ตรวจสอบว่ารูปมีอยู่จริง
            if (file_exists($imagePath)) {
                // แสดงรูปภาพ
                $mpdf->Image($imagePath, $x, $y, $frameWidth, $frameHeight, 'jpg', '', true, false);
            } else {
                // ถ้ารูปไม่มี → แสดงกรอบเปล่า + ข้อความแจ้งเตือน
                $mpdf->SetXY($x, $y);
                $mpdf->Cell($frameWidth, $frameHeight, '', 1, 1, 'C'); // วาดกรอบรูป
                $mpdf->SetXY($x, $y + ($frameHeight / 2) - 5);
                $mpdf->Cell($frameWidth, 10, 'ไม่พบรูปภาพ', 0, 1, 'C');
            }
            //ชื่อไฟล์ filupload 2
            $mpdf->SetXY(30,220);
            $mpdf->Cell(140, 10,"ชื่อไฟล์: ". $application['fileUpload2'], 0, 1, 'L');

        }
        
        
////////// ข้อมูลที่อยู่ในหน้าที่ 9
        if ($i == 9) {
            $mpdf->SetFont('sarabun', '', 14);


            //ใบแสดงผลการศึกษาเฉลี่ยสะสม (ให้นักศึกษาพิมพ์จากเว็บไซต์ของมหาวิทยาลัย)
             // กำหนดพาธของรูปภาพ
             $imagePath = 'uploads/' . $application['fileUpload3'];
             // ขนาดกรอบของรูปภาพ
             $frameWidth = 30;  // กว้าง
             $frameHeight = 30; // สูง
             $x = 150;  // ตำแหน่ง X
             $y = 2;  // ตำแหน่ง Y
 
             // ตรวจสอบว่ารูปมีอยู่จริง
             if (file_exists($imagePath)) {
                 // แสดงรูปภาพ
                 $mpdf->Image($imagePath, $x, $y, $frameWidth, $frameHeight, 'jpg', '', true, false);
             } else {
                 // ถ้ารูปไม่มี → แสดงกรอบเปล่า + ข้อความแจ้งเตือน
                 $mpdf->SetXY($x, $y);
                 $mpdf->Cell($frameWidth, $frameHeight, '', 1, 1, 'C'); // วาดกรอบรูป
                 $mpdf->SetXY($x, $y + ($frameHeight / 2) - 5);
                 $mpdf->Cell($frameWidth, 10, 'ไม่พบรูปภาพ', 0, 1, 'C');
             }
             //ชื่อไฟล์ filupload 3
             $mpdf->SetXY(30,10);
             $mpdf->Cell(140, 10,"ชื่อไฟล์: ". $application['fileUpload3'], 0, 1, 'L');


             //สำเนาหน้าสมุดบัญชีเงินธนาคารของผู้ขอรับทุนการศึกษา
              // กำหนดพาธของรูปภาพ
              $imagePath = 'uploads/' . $application['fileUpload4'];
              // ขนาดกรอบของรูปภาพ
              $frameWidth = 30;  // กว้าง
              $frameHeight = 30; // สูง
              $x = 150;  // ตำแหน่ง X
              $y = 60;  // ตำแหน่ง Y
  
              // ตรวจสอบว่ารูปมีอยู่จริง
              if (file_exists($imagePath)) {
                  // แสดงรูปภาพ
                  $mpdf->Image($imagePath, $x, $y, $frameWidth, $frameHeight, 'jpg', '', true, false);
              } else {
                  // ถ้ารูปไม่มี → แสดงกรอบเปล่า + ข้อความแจ้งเตือน
                  $mpdf->SetXY($x, $y);
                  $mpdf->Cell($frameWidth, $frameHeight, '', 1, 1, 'C'); // วาดกรอบรูป
                  $mpdf->SetXY($x, $y + ($frameHeight / 2) - 5);
                  $mpdf->Cell($frameWidth, 10, 'ไม่พบรูปภาพ', 0, 1, 'C');
              }
              //ชื่อไฟล์ filupload 4
              $mpdf->SetXY(30,70);
              $mpdf->Cell(140, 10,"ชื่อไฟล์: ". $application['fileUpload4'], 0, 1, 'L');










        }    
    }
    
    $mpdf->Output();
} catch (PDOException $e) {
    error_log($e->getMessage(), 3, 'C:/xampp/htdocs/newcompany/logs/error.log');
    echo "เกิดข้อผิดพลาด กรุณาลองใหม่ภายหลัง";
}
?>
