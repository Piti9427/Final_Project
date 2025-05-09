<?php
session_start();
// Database connection
include "../config.php";   

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    function generateUniqueFilename($directory, $filename) {
        $info = pathinfo($filename);
        $base_name = $info['filename'];
        $extension = isset($info['extension']) ? $info['extension'] : ''; // ตรวจสอบว่ามี extension หรือไม่
        // สร้างชื่อไฟล์ใหม่โดยใช้ uniqid()
        $new_filename = $base_name . '_' . uniqid() . ($extension ? '.' . $extension : '');
        return $new_filename;
    }


    // กำหนดโฟลเดอร์ปลายทางสำหรับไฟล์อัปโหลด
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    } 

    // จัดการอัปโหลด student_photo
    $student_photo = "";    
    if (isset($_FILES["student_photo"]) && $_FILES["student_photo"]["error"] == 0) {
        $file_extension = strtolower(pathinfo($_FILES["student_photo"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];

        if (!in_array($file_extension, $allowed_extensions)) {
            die("ไฟล์ประเภทนี้ไม่ได้รับอนุญาต");
        }

        if ($_FILES["student_photo"]["size"] > 2 * 1024 * 1024) {
            die("ขนาดไฟล์ใหญ่เกินไป");
        }

        $student_photo = generateUniqueFilename($target_dir, $_FILES["student_photo"]["name"]);
        $target_file = $target_dir . $student_photo;

        if (move_uploaded_file($_FILES["student_photo"]["tmp_name"], $target_file)) {
            $student_photo = $student_photo;
        } else {
            die("อัปโหลดไฟล์ไม่สำเร็จ");
        }
    }

    $logo_photo = isset($_POST['logo_photo']) ? $_POST['logo_photo'] : null;


    // จัดการอัปโหลด student_image
    $student_image = "";
    if (isset($_FILES["student_image"]) && $_FILES["student_image"]["error"] == 0) {
        $file_extension = strtolower(pathinfo($_FILES["student_image"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];

        if (!in_array($file_extension, $allowed_extensions)) {
            die("ไฟล์ประเภทนี้ไม่ได้รับอนุญาต");
        }

        if ($_FILES["student_image"]["size"] > 2 * 1024 * 1024) {
            die("ขนาดไฟล์ใหญ่เกินไป");
        }

        $student_image = generateUniqueFilename($target_dir, $_FILES["student_image"]["name"]);
        $target_file = $target_dir . $student_image;

        if (move_uploaded_file($_FILES["student_image"]["tmp_name"], $target_file)) {
            $student_image = $student_image;
        } else {
            die("อัปโหลดไฟล์ไม่สำเร็จ");
        }
    }

    // อัปโหลดบัตรประจำตัวนักศึกษา
    $id_card_image = "";
    if (isset($_FILES["id_card_image"]) && $_FILES["id_card_image"]["error"] == 0) {
        $file_extension = strtolower(pathinfo($_FILES["id_card_image"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];

        if (!in_array($file_extension, $allowed_extensions)) {
            die("ไฟล์ประเภทนี้ไม่ได้รับอนุญาต");
        }

        if ($_FILES["id_card_image"]["size"] > 2 * 1024 * 1024) {
            die("ขนาดไฟล์ใหญ่เกินไป");
        }

        $id_card_image = generateUniqueFilename($target_dir, $_FILES["id_card_image"]["name"]);
        $target_file = $target_dir . $id_card_image;

        if (move_uploaded_file($_FILES["id_card_image"]["tmp_name"], $target_file)) {
            $id_card_image = $id_card_image;
        } else {
            die("อัปโหลดไฟล์ไม่สำเร็จ");
        }
    }

    // อัปโหลดใบแสดงผลการศึกษา
    $average_grade_image = "";
    if (isset($_FILES["average_grade_image"]) && $_FILES["average_grade_image"]["error"] == 0) {
        $file_extension = strtolower(pathinfo($_FILES["average_grade_image"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];

        if (!in_array($file_extension, $allowed_extensions)) {
            die("ไฟล์ประเภทนี้ไม่ได้รับอนุญาต");
        }

        if ($_FILES["average_grade_image"]["size"] > 2 * 1024 * 1024) {
            die("ขนาดไฟล์ใหญ่เกินไป");
        }

        $average_grade_image = generateUniqueFilename($target_dir, $_FILES["average_grade_image"]["name"]);
        $target_file = $target_dir . $average_grade_image;

        if (move_uploaded_file($_FILES["average_grade_image"]["tmp_name"], $target_file)) {
            $average_grade_image = $average_grade_image;
        } else {
            die("อัปโหลดไฟล์ไม่สำเร็จ");
        }
    }

    // อัปโหลดสำเนาหน้าสมุดบัญชี
    $bank_account_image = "";
    if (isset($_FILES["bank_account_image"]) && $_FILES["bank_account_image"]["error"] == 0) {
        $file_extension = strtolower(pathinfo($_FILES["bank_account_image"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];

        if (!in_array($file_extension, $allowed_extensions)) {
            die("ไฟล์ประเภทนี้ไม่ได้รับอนุญาต");
        }

        if ($_FILES["bank_account_image"]["size"] > 2 * 1024 * 1024) {
            die("ขนาดไฟล์ใหญ่เกินไป");
        }

        $bank_account_image = generateUniqueFilename($target_dir, $_FILES["bank_account_image"]["name"]);
        $target_file = $target_dir . $bank_account_image;

        if (move_uploaded_file($_FILES["bank_account_image"]["tmp_name"], $target_file)) {
            $bank_account_image = $bank_account_image;
        } else {
            die("อัปโหลดไฟล์ไม่สำเร็จ");
        }
    }

    // อัปโหลดแผนที่
    $fileUpload1 = "";
    if (!empty($_FILES['fileUpload1']['name'][0])) {
        $uploaded_files = [];
        $allowed_extensions = ["jpg", "jpeg", "png", "gif", "pdf", "doc", "docx"];

        foreach ($_FILES['fileUpload1']['name'] as $key => $filename) {
            $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = generateUniqueFilename($target_dir, $filename);
                $target_file = $target_dir . $new_filename;

                if (move_uploaded_file($_FILES['fileUpload1']['tmp_name'][$key], $target_file)) {
                    $uploaded_files[] = $new_filename;
                } else {
                    error_log("Failed to upload file: $filename");
                }
            } else {
                error_log("Unsupported file type: $filename");
            }
        }

        $fileUpload1 = implode(",", $uploaded_files); // รวมชื่อไฟล์ทั้งหมด
    }

    // สำเนาบัตรประจำตัวนักศึกษา
    $fileUpload2 = "";
    if (!empty($_FILES['fileUpload2']['name'][0])) {
        $uploaded_files = [];
        $allowed_extensions = ["jpg", "jpeg", "png", "gif", "pdf", "doc", "docx"];

        foreach ($_FILES['fileUpload2']['name'] as $key => $filename) {
            $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = generateUniqueFilename($target_dir, $filename);
                $target_file = $target_dir . $new_filename;

                if (move_uploaded_file($_FILES['fileUpload2']['tmp_name'][$key], $target_file)) {
                    $uploaded_files[] = $new_filename;
                } else {
                    error_log("Failed to upload file: $filename");
                }
            } else {
                error_log("Unsupported file type: $filename");
            }
        }

        $fileUpload2 = implode(",", $uploaded_files); // รวมชื่อไฟล์ทั้งหมด
    }

    // ใบแสดงผลการศึกษาเฉลี่ยสะสม
    $fileUpload3 = "";
    if (!empty($_FILES['fileUpload3']['name'][0])) {
        $uploaded_files = [];
        $allowed_extensions = ["jpg", "jpeg", "png", "gif", "pdf", "doc", "docx"];

        foreach ($_FILES['fileUpload3']['name'] as $key => $filename) {
            $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = generateUniqueFilename($target_dir, $filename);
                $target_file = $target_dir . $new_filename;

                if (move_uploaded_file($_FILES['fileUpload3']['tmp_name'][$key], $target_file)) {
                    $uploaded_files[] = $new_filename;
                } else {
                    error_log("Failed to upload file: $filename");
                }
            } else {
                error_log("Unsupported file type: $filename");
            }
        }

        $fileUpload3 = implode(",", $uploaded_files); // รวมชื่อไฟล์ทั้งหมด
    }

    // สำเนาหน้าสมุดบัญชีเงินธนาคาร
    $fileUpload4 = "";
    if (!empty($_FILES['fileUpload4']['name'][0])) {
        $uploaded_files = [];
        $allowed_extensions = ["jpg", "jpeg", "png", "gif", "pdf", "doc", "docx"];

        foreach ($_FILES['fileUpload4']['name'] as $key => $filename) {
            $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = generateUniqueFilename($target_dir, $filename);
                $target_file = $target_dir . $new_filename;

                if (move_uploaded_file($_FILES['fileUpload4']['tmp_name'][$key], $target_file)) {
                    $uploaded_files[] = $new_filename;
                } else {
                    error_log("Failed to upload file: $filename");
                }
            } else {
                error_log("Unsupported file type: $filename");
            }
        }

        $fileUpload4 = implode(",", $uploaded_files); // รวมชื่อไฟล์ทั้งหมด
    }


    // เตรียมคำสั่ง SQL
    $sql = "INSERT INTO scholarship_applications (
        status, scholarship_id, user_login, academic_year, student_photo, prefix_th, first_name_th, last_name_th,
        prefix_en, first_name_en, last_name_en, faculty, user_no, branch_no, major, year_level,
        student_id, gpa, birth_place, birth_date, age, religion,
        permanent_house_no, permanent_moo, permanent_road, permanent_subdistrict,
        permanent_district, permanent_province, permanent_postal_code,
        permanent_phone, permanent_mobile, current_residence_type,
        current_building, current_room_no, current_house_no, current_moo,
        current_road, current_subdistrict, current_district, current_province,
        current_postal_code, current_phone, parent_allowance_type,
        parent_allowance_amount, other_allowance_type, other_allowance_amount,
        loan_amount, extra_income_daily, extra_income_source, food_expense_daily,
        accommodation_expense, transportation_method, transportation_expense_daily,
        education_supplies_expense, other_expense_detail, other_expense_amount,
        estimated_monthly_expense, living_with, Benefactor_relevance, guardian_relation,
        dormitory_name, dormitory_room, contact_address, contact_phone,
        dormitorytemple, dormitorytemple_room, dormitorytemple_contact, dormitorytemple_phone, expense_select, payment_type, scholarship_status, scholarship_amount, scholarship_term_amount, scholarship_cost_living,
        historycholarship_status, dormitoryhouse_fee, senior_high_school, one_years, two_years, three_years, four_years, five_years, six_years,
        senior_high_school_amount, one_years_amount, two_years_amount, three_years_amount, four_years_amount,
        five_years_amount, six_years_amount,
        primary_school, primary_province, middle_school, middle_province, high_school, high_province,
        father_fullname, father_age, father_status, father_house, father_alley, father_moo, father_road,
        father_subdistrict, father_district, father_province, father_post_code, father_house_no,
        father_phone, father_occupation, father_income, father_rank, father_job_description,
        father_workplace, father_telephone, mother_fullname, mother_age, mother_status, mother_house, mother_ally,
        mother_moo, mother_road, mother_subdistrict, mother_district, mother_province,
        mother_postcode, mother_house_no, mother_phone, mother_occupation, mother_income,
        mother_rank, mother_job_description, mother_workplace, mother_telephone, living_conditions_grantees, relationship_benefactors, parents_landstatus,
        familystatus, benefactor, other_familystatus, landstatus, landstatus1, landstatus2, landstatus3, landstatus4, landstatus5, landstatus6,
        ownfarm, otherfarm, monthly_rent_land, peryear_rent_land, hasGuardian, guardian_fullname, guardian_age, guardian_relevant, guardian_house, guardian_ally,
        guardian_moo, guardian_road, guardian_subdistrict, guardian_district, guardian_province, guardian_postcode, guardian_house_no, guardian_phone, guardian_status,
        guardian_children, guardian_children_studying, guardian_children_occupation, guardian_occupation, guardian_monthly_income, guardian_rank, guardian_job_description,
        guardian_workplace, guardian_telephone, liveothers_land, renthouse_monthly_land, renthouse_peryear_land, sibling_amount, sibling_child_amount, sibling_fullname_one,
        sibling_age_one, sibling_education_one, sibling_grade_level_one, sibling_occupation_one, sibling_monthly_income_one, sibling_status_one, sibling_children_amount_one,
        sibling_fullname_two, sibling_age_two, sibling_education_two, sibling_grade_level_two, sibling_occupation_two, sibling_monthly_income_two, sibling_status_two, sibling_children_amount_two,
        sibling_fullname_three, sibling_age_three, sibling_education_three, sibling_grade_level_three, sibling_occupation_three, sibling_monthly_income_three, sibling_status_three, sibling_children_amount_three,
        sibling_fullname_four, sibling_age_four, sibling_education_four, sibling_grade_level_four, sibling_occupation_four, sibling_monthly_income_four, sibling_status_four, sibling_children_amount_four,
        sibling_currently_children, sibling_financial_problems, sibling_solutions, sibling_scholarship_necessity, healthIssue, healthIssueDescription, studyProblems, familyProblems,
        parttime_job, parttime_income, parttime_income_period, special_abilities, special_activities, special_activities1, special_activities2,
        awards, awards_year, awards1, awards_year1, future_goals, emergency_contact_name, emergency_contact_relevant, emergency_contact_house, emergency_contact_ally,
        emergency_contact_moo, emergency_contact_road, emergency_contact_subdistrict, emergency_contact_district, emergency_contact_province, emergency_contact_postcode,
        emergency_contact_house_no, emergency_contact_phone, scholarship_required, scholarship_amount_description, signature_scholarship, signature_name, signature_date, signature_month, signature_year,
        student_image, id_card_image, average_grade_image, bank_account_image, describe_scholarship, fileUpload1, landmarks, directions, fileUpload2, fileUpload3, fileUpload4, logo_photo
    ) VALUES (
        :status, :scholarship_id, :user_login, :academic_year, :student_photo, :prefix_th, :first_name_th, :last_name_th,
        :prefix_en, :first_name_en, :last_name_en, :faculty, :user_no, :branch_no, :major, :year_level,
        :student_id, :gpa, :birth_place, :birth_date, :age, :religion,
        :permanent_house_no, :permanent_moo, :permanent_road, :permanent_subdistrict,
        :permanent_district, :permanent_province, :permanent_postal_code,
        :permanent_phone, :permanent_mobile, :current_residence_type,
        :current_building, :current_room_no, :current_house_no, :current_moo,
        :current_road, :current_subdistrict, :current_district, :current_province,
        :current_postal_code, :current_phone, :parent_allowance_type,
        :parent_allowance_amount, :other_allowance_type, :other_allowance_amount,
        :loan_amount, :extra_income_daily, :extra_income_source, :food_expense_daily,
        :accommodation_expense, :transportation_method, :transportation_expense_daily,
        :education_supplies_expense, :other_expense_detail, :other_expense_amount,
        :estimated_monthly_expense, :living_with, :Benefactor_relevance, :guardian_relation,
        :dormitory_name, :dormitory_room, :contact_address, :contact_phone,
        :dormitorytemple, :dormitorytemple_room, :dormitorytemple_contact, :dormitorytemple_phone, :expense_select, :payment_type, :scholarship_status, :scholarship_amount, :scholarship_term_amount, :scholarship_cost_living,
        :historycholarship_status, :dormitoryhouse_fee, :senior_high_school, :one_years, :two_years, :three_years, :four_years, :five_years, :six_years,
        :senior_high_school_amount, :one_years_amount, :two_years_amount, :three_years_amount, :four_years_amount,
        :five_years_amount, :six_years_amount,
        :primary_school, :primary_province, :middle_school, :middle_province, :high_school, :high_province,
        :father_fullname, :father_age, :father_status, :father_house, :father_alley, :father_moo, :father_road,
        :father_subdistrict, :father_district, :father_province, :father_post_code, :father_house_no,
        :father_phone, :father_occupation, :father_income, :father_rank, :father_job_description,
        :father_workplace, :father_telephone, :mother_fullname, :mother_age, :mother_status, :mother_house, :mother_ally,
        :mother_moo, :mother_road, :mother_subdistrict, :mother_district, :mother_province,
        :mother_postcode, :mother_house_no, :mother_phone, :mother_occupation, :mother_income,
        :mother_rank, :mother_job_description, :mother_workplace, :mother_telephone, :living_conditions_grantees, :relationship_benefactors, :parents_landstatus,
        :familystatus, :benefactor, :other_familystatus, :landstatus, :landstatus1, :landstatus2, :landstatus3, :landstatus4, :landstatus5, :landstatus6,
        :ownfarm, :otherfarm, :monthly_rent_land, :peryear_rent_land, :hasGuardian, :guardian_fullname, :guardian_age, :guardian_relevant, :guardian_house, :guardian_ally,
        :guardian_moo, :guardian_road, :guardian_subdistrict, :guardian_district, :guardian_province, :guardian_postcode, :guardian_house_no, :guardian_phone, :guardian_status,
        :guardian_children, :guardian_children_studying, :guardian_children_occupation, :guardian_occupation, :guardian_monthly_income, :guardian_rank, :guardian_job_description,
        :guardian_workplace, :guardian_telephone, :liveothers_land, :renthouse_monthly_land, :renthouse_peryear_land, :sibling_amount, :sibling_child_amount, :sibling_fullname_one,
        :sibling_age_one, :sibling_education_one, :sibling_grade_level_one, :sibling_occupation_one, :sibling_monthly_income_one, :sibling_status_one, :sibling_children_amount_one,
        :sibling_fullname_two, :sibling_age_two, :sibling_education_two, :sibling_grade_level_two, :sibling_occupation_two, :sibling_monthly_income_two, :sibling_status_two, :sibling_children_amount_two,
        :sibling_fullname_three, :sibling_age_three, :sibling_education_three, :sibling_grade_level_three, :sibling_occupation_three, :sibling_monthly_income_three, :sibling_status_three, :sibling_children_amount_three,
        :sibling_fullname_four, :sibling_age_four, :sibling_education_four, :sibling_grade_level_four, :sibling_occupation_four, :sibling_monthly_income_four, :sibling_status_four, :sibling_children_amount_four,
        :sibling_currently_children, :sibling_financial_problems, :sibling_solutions, :sibling_scholarship_necessity, :healthIssue, :healthIssueDescription, :studyProblems, :familyProblems,
        :parttime_job, :parttime_income, :parttime_income_period, :special_abilities, :special_activities, :special_activities1, :special_activities2,
        :awards, :awards_year, :awards1, :awards_year1, :future_goals, :emergency_contact_name, :emergency_contact_relevant, :emergency_contact_house, :emergency_contact_ally,
        :emergency_contact_moo, :emergency_contact_road, :emergency_contact_subdistrict, :emergency_contact_district, :emergency_contact_province, :emergency_contact_postcode,
        :emergency_contact_house_no, :emergency_contact_phone, :scholarship_required, :scholarship_amount_description, :signature_scholarship, :signature_name, :signature_date, :signature_month, :signature_year,
        :student_image, :id_card_image, :average_grade_image, :bank_account_image, :describe_scholarship, :fileUpload1, :landmarks, :directions, :fileUpload2, :fileUpload3, :fileUpload4, :logo_photo
    )";

    $stmt = $conn->prepare($sql);
    // Bind parameters with null coalescing
    $params = [
        ':status' => 'pending',
        ':scholarship_id' => $_POST['scholarship_id'] ?? null, // เพิ่ม scholarship_id
        ':user_login' => $_POST['user_login'] ?? null,
        ':academic_year' => $_POST['academic_year'] ?? null,
        ':student_photo' => $student_photo,
        ':prefix_th' => $_POST['prefix_th'] ?? null,
        ':first_name_th' => $_POST['first_name_th'] ?? null,
        ':last_name_th' => $_POST['last_name_th'] ?? null,
        ':prefix_en' => $_POST['prefix_en'] ?? null,
        ':first_name_en' => $_POST['first_name_en'] ?? null,
        ':last_name_en' => $_POST['last_name_en'] ?? null,
        ':faculty' => $_POST['faculty'] ?? null,
        ':user_no' => $_POST['user_no'] ?? null,
        ':branch_no' => $_POST['branch_no'] ?? null,
        ':major' => $_POST['major'] ?? null,
        ':year_level' => $_POST['year_level'] ?? null,
        ':student_id' => $_POST['student_id'] ?? null,
        ':gpa' => $_POST['gpa'] ?? null,
        ':birth_place' => $_POST['birth_place'] ?? null,
        ':birth_date' => $_POST['birth_date'] ?? null,
        ':age' => $_POST['age'] ?? null,
        ':religion' => $_POST['religion'] ?? null,
        ':permanent_house_no' => $_POST['permanent_house_no'] ?? null,
        ':permanent_moo' => $_POST['permanent_moo'] ?? null,
        ':permanent_road' => $_POST['permanent_road'] ?? null,
        ':permanent_subdistrict' => $_POST['permanent_subdistrict'] ?? null,
        ':permanent_district' => $_POST['permanent_district'] ?? null,
        ':permanent_province' => $_POST['permanent_province'] ?? null,
        ':permanent_postal_code' => $_POST['permanent_postal_code'] ?? null,
        ':permanent_phone' => $_POST['permanent_phone'] ?? null,
        ':permanent_mobile' => $_POST['permanent_mobile'] ?? null,
        ':current_residence_type' => $_POST['current_residence_type'] ?? null,
        ':current_building' => $_POST['current_building'] ?? null,
        ':current_room_no' => $_POST['current_room_no'] ?? null,
        ':current_house_no' => $_POST['current_house_no'] ?? null,
        ':current_moo' => $_POST['current_moo'] ?? null,
        ':current_road' => $_POST['current_road'] ?? null,
        ':current_subdistrict' => $_POST['current_subdistrict'] ?? null,
        ':current_district' => $_POST['current_district'] ?? null,
        ':current_province' => $_POST['current_province'] ?? null,
        ':current_postal_code' => $_POST['current_postal_code'] ?? null,
        ':current_phone' => $_POST['current_phone'] ?? null,
        ':parent_allowance_type' => $_POST['parent_allowance_type'] ?? null,
        ':parent_allowance_amount' => $_POST['parent_allowance_amount'] ?? null,
        ':other_allowance_type' => $_POST['other_allowance_type'] ?? null,
        ':other_allowance_amount' => $_POST['other_allowance_amount'] ?? null,
        ':loan_amount' => $_POST['loan_amount'] ?? null,
        ':extra_income_daily' => $_POST['extra_income_daily'] ?? null,
        ':extra_income_source' => $_POST['extra_income_source'] ?? null,
        ':food_expense_daily' => $_POST['food_expense_daily'] ?? null,
        ':accommodation_expense' => $_POST['accommodation_expense'] ?? null,
        ':transportation_method' => $_POST['transportation_method'] ?? null,
        ':transportation_expense_daily' => $_POST['transportation_expense_daily'] ?? null,
        ':education_supplies_expense' => $_POST['education_supplies_expense'] ?? null,
        ':other_expense_detail' => $_POST['other_expense_detail'] ?? null,
        ':other_expense_amount' => $_POST['other_expense_amount'] ?? null,
        ':estimated_monthly_expense' => $_POST['estimated_monthly_expense'] ?? null,
        ':living_with' => $_POST['living_with'] ?? null,
        ':Benefactor_relevance' => $_POST['Benefactor_relevance'] ?? null,
        ':guardian_relation' => $_POST['guardian_relation'] ?? null,
        ':dormitory_name' => $_POST['dormitory_name'] ?? null,
        ':dormitory_room' => $_POST['dormitory_room'] ?? null,
        ':contact_address' => $_POST['contact_address'] ?? null,
        ':contact_phone' => $_POST['contact_phone'] ?? null,
        ':dormitorytemple' => $_POST['dormitorytemple'] ?? null,
        ':dormitorytemple_room' => $_POST['dormitorytemple_room'] ?? null,
        ':dormitorytemple_contact' => $_POST['dormitorytemple_contact'] ?? null,
        ':dormitorytemple_phone' => $_POST['dormitorytemple_phone'] ?? null,
        ':expense_select' => $_POST['expense_select'] ?? null,
        ':payment_type' => $_POST['payment_type'] ?? null,
        ':scholarship_status' => $_POST['scholarship_status'] ?? null,
        ':scholarship_amount' => $_POST['scholarship_amount'] ?? null,
        ':scholarship_term_amount' => $_POST['scholarship_term_amount'] ?? null,
        ':scholarship_cost_living' => $_POST['scholarship_cost_living'] ?? null,
        ':historycholarship_status' => $_POST['historycholarship_status'] ?? null,
        ':dormitoryhouse_fee' => $_POST['dormitoryhouse_fee'] ?? null,
        ':senior_high_school' => $_POST['senior_high_school'] ?? null,
        ':one_years' => $_POST['one_years'] ?? null,
        ':two_years' => $_POST['two_years'] ?? null,
        ':three_years' => $_POST['three_years'] ?? null,
        ':four_years' => $_POST['four_years'] ?? null,
        ':five_years' => $_POST['five_years'] ?? null,
        ':six_years' => $_POST['six_years'] ?? null,
        ':senior_high_school_amount' => $_POST['senior_high_school_amount'] ?? null,
        ':one_years_amount' => $_POST['one_years_amount'] ?? null,
        ':two_years_amount' => $_POST['two_years_amount'] ?? null,
        ':three_years_amount' => $_POST['three_years_amount'] ?? null,
        ':four_years_amount' => $_POST['four_years_amount'] ?? null,
        ':five_years_amount' => $_POST['five_years_amount'] ?? null,
        ':six_years_amount' => $_POST['six_years_amount'] ?? null,
        ':primary_school' => $_POST['primary_school'] ?? null,
        ':primary_province' => $_POST['primary_province'] ?? null,
        ':middle_school' => $_POST['middle_school'] ?? null,
        ':middle_province' => $_POST['middle_province'] ?? null,
        ':high_school' => $_POST['high_school'] ?? null,
        ':high_province' => $_POST['high_province'] ?? null,
        ':father_fullname' => $_POST['father_fullname'] ?? null,
        ':father_age' => $_POST['father_age'] ?? null,
        ':father_status' => $_POST['father_status'] ?? null,
        ':father_house' => $_POST['father_house'] ?? null,
        ':father_alley' => $_POST['father_alley'] ?? null,
        ':father_moo' => $_POST['father_moo'] ?? null,
        ':father_road' => $_POST['father_road'] ?? null,
        ':father_subdistrict' => $_POST['father_subdistrict'] ?? null,
        ':father_district' => $_POST['father_district'] ?? null,
        ':father_province' => $_POST['father_province'] ?? null,
        ':father_post_code' => $_POST['father_post_code'] ?? null,
        ':father_house_no' => $_POST['father_house_no'] ?? null,
        ':father_phone' => $_POST['father_phone'] ?? null,
        ':father_occupation' => $_POST['father_occupation'] ?? null,
        ':father_income' => $_POST['father_income'] ?? null,
        ':father_rank' => $_POST['father_rank'] ?? null,
        ':father_job_description' => $_POST['father_job_description'] ?? null,
        ':father_workplace' => $_POST['father_workplace'] ?? null,
        ':father_telephone' => $_POST['father_telephone'] ?? null,
        ':mother_fullname' => $_POST['mother_fullname'] ?? null,
        ':mother_age' => $_POST['mother_age'] ?? null,
        ':mother_status' => $_POST['mother_status'] ?? null,
        ':mother_house' => $_POST['mother_house'] ?? null,
        ':mother_ally' => $_POST['mother_ally'] ?? null,
        ':mother_moo' => $_POST['mother_moo'] ?? null,
        ':mother_road' => $_POST['mother_road'] ?? null,
        ':mother_subdistrict' => $_POST['mother_subdistrict'] ?? null,
        ':mother_district' => $_POST['mother_district'] ?? null,
        ':mother_province' => $_POST['mother_province'] ?? null,
        ':mother_postcode' => $_POST['mother_postcode'] ?? null,
        ':mother_house_no' => $_POST['mother_house_no'] ?? null,
        ':mother_phone' => $_POST['mother_phone'] ?? null,
        ':mother_occupation' => $_POST['mother_occupation'] ?? null,
        ':mother_income' => $_POST['mother_income'] ?? null,
        ':mother_rank' => $_POST['mother_rank'] ?? null,
        ':mother_job_description' => $_POST['mother_job_description'] ?? null,
        ':mother_workplace' => $_POST['mother_workplace'] ?? null,
        ':mother_telephone' => $_POST['mother_telephone'] ?? null,
        ':living_conditions_grantees' => $_POST['living_conditions_grantees'] ?? null,
        ':relationship_benefactors' => $_POST['relationship_benefactors'] ?? null,
        ':parents_landstatus' => $_POST['parents_landstatus'] ?? null,
        ':familystatus' => $_POST['familystatus'] ?? null,
        ':benefactor' => $_POST['benefactor'] ?? null,
        ':other_familystatus' => $_POST['other_familystatus'] ?? null,
        ':landstatus' => $_POST['landstatus'] ?? null,
        ':landstatus1' => $_POST['landstatus1'] ?? null,
        ':landstatus2' => $_POST['landstatus2'] ?? null,
        ':landstatus3' => $_POST['landstatus3'] ?? null,
        ':landstatus4' => $_POST['landstatus4'] ?? null,
        ':landstatus5' => $_POST['landstatus5'] ?? null,
        ':landstatus6' => $_POST['landstatus6'] ?? null,
        ':ownfarm' => $_POST['ownfarm'] ?? null,
        ':otherfarm' => $_POST['otherfarm'] ?? null,
        ':monthly_rent_land' => $_POST['monthly_rent_land'] ?? null,
        ':peryear_rent_land' => $_POST['peryear_rent_land'] ?? null,
        ':hasGuardian' => $_POST['hasGuardian'] ?? null,
        ':guardian_fullname' => $_POST['guardian_fullname'] ?? null,
        ':guardian_age' => $_POST['guardian_age'] ?? null,
        ':guardian_relevant' => $_POST['guardian_relevant'] ?? null,
        ':guardian_house' => $_POST['guardian_house'] ?? null,
        ':guardian_ally' => $_POST['guardian_ally'] ?? null,
        ':guardian_moo' => $_POST['guardian_moo'] ?? null,
        ':guardian_road' => $_POST['guardian_road'] ?? null,
        ':guardian_subdistrict' => $_POST['guardian_subdistrict'] ?? null,
        ':guardian_district' => $_POST['guardian_district'] ?? null,
        ':guardian_province' => $_POST['guardian_province'] ?? null,
        ':guardian_postcode' => $_POST['guardian_postcode'] ?? null,
        ':guardian_house_no' => $_POST['guardian_house_no'] ?? null,
        ':guardian_phone' => $_POST['guardian_phone'] ?? null,
        ':guardian_status' => $_POST['guardian_status'] ?? null,
        ':guardian_children' => $_POST['guardian_children'] ?? null,
        ':guardian_children_studying' => $_POST['guardian_children_studying'] ?? null,
        ':guardian_children_occupation' => $_POST['guardian_children_occupation'] ?? null,
        ':guardian_occupation' => $_POST['guardian_occupation'] ?? null,
        ':guardian_monthly_income' => $_POST['guardian_monthly_income'] ?? null,
        ':guardian_rank' => $_POST['guardian_rank'] ?? null,
        ':guardian_job_description' => $_POST['guardian_job_description'] ?? null,
        ':guardian_workplace' => $_POST['guardian_workplace'] ?? null,
        ':guardian_telephone' => $_POST['guardian_telephone'] ?? null,
        ':liveothers_land' => $_POST['liveothers_land'] ?? null,
        ':renthouse_monthly_land' => $_POST['renthouse_monthly_land'] ?? null,
        ':renthouse_peryear_land' => $_POST['renthouse_peryear_land'] ?? null,
        ':sibling_amount' => $_POST['sibling_amount'] ?? null,
        ':sibling_child_amount' => $_POST['sibling_child_amount'] ?? null,

        ':sibling_fullname_one' => $_POST['sibling_fullname_one'] ?? null,
        ':sibling_age_one' => $_POST['sibling_age_one'] ?? null,
        ':sibling_education_one' => $_POST['sibling_education_one'] ?? null,
        ':sibling_grade_level_one' => $_POST['sibling_grade_level_one'] ?? null,
        ':sibling_occupation_one' => $_POST['sibling_occupation_one'] ?? null,
        ':sibling_monthly_income_one' => $_POST['sibling_monthly_income_one'] ?? null,
        ':sibling_status_one' => $_POST['sibling_status_one'] ?? null,
        ':sibling_children_amount_one' => $_POST['sibling_children_amount_one'] ?? null,

        ':sibling_fullname_two' => $_POST['sibling_fullname_two'] ?? null,
        ':sibling_age_two' => $_POST['sibling_age_two'] ?? null,
        ':sibling_education_two' => $_POST['sibling_education_two'] ?? null,
        ':sibling_grade_level_two' => $_POST['sibling_grade_level_two'] ?? null,
        ':sibling_occupation_two' => $_POST['sibling_occupation_two'] ?? null,
        ':sibling_monthly_income_two' => $_POST['sibling_monthly_income_two'] ?? null,
        ':sibling_status_two' => $_POST['sibling_status_two'] ?? null,
        ':sibling_children_amount_two' => $_POST['sibling_children_amount_two'] ?? null,

        ':sibling_fullname_three' => $_POST['sibling_fullname_three'] ?? null,
        ':sibling_age_three' => $_POST['sibling_age_three'] ?? null,
        ':sibling_education_three' => $_POST['sibling_education_three'] ?? null,
        ':sibling_grade_level_three' => $_POST['sibling_grade_level_three'] ?? null,
        ':sibling_occupation_three' => $_POST['sibling_occupation_three'] ?? null,
        ':sibling_monthly_income_three' => $_POST['sibling_monthly_income_three'] ?? null,
        ':sibling_status_three' => $_POST['sibling_status_three'] ?? null,
        ':sibling_children_amount_three' => $_POST['sibling_children_amount_three'] ?? null,

        ':sibling_fullname_four' => $_POST['sibling_fullname_four'] ?? null,
        ':sibling_age_four' => $_POST['sibling_age_four'] ?? null,
        ':sibling_education_four' => $_POST['sibling_education_four'] ?? null,
        ':sibling_grade_level_four' => $_POST['sibling_grade_level_four'] ?? null,
        ':sibling_occupation_four' => $_POST['sibling_occupation_four'] ?? null,
        ':sibling_monthly_income_four' => $_POST['sibling_monthly_income_four'] ?? null,
        ':sibling_status_four' => $_POST['sibling_status_four'] ?? null,
        ':sibling_children_amount_four' => $_POST['sibling_children_amount_four'] ?? null,
        
        ':sibling_currently_children' => $_POST['sibling_currently_children'] ?? null,
        ':sibling_financial_problems' => $_POST['sibling_financial_problems'] ?? null,
        ':sibling_solutions' => $_POST['sibling_solutions'] ?? null,
        ':sibling_scholarship_necessity' => $_POST['sibling_scholarship_necessity'] ?? null,
        ':healthIssue' => $_POST['healthIssue'] ?? null,
        ':healthIssueDescription' => $_POST['healthIssueDescription'] ?? null,
        ':studyProblems' => $_POST['studyProblems'] ?? null,
        ':familyProblems' => $_POST['familyProblems'] ?? null,
        ':parttime_job' => $_POST['parttime_job'] ?? null,
        ':parttime_income' => $_POST['parttime_income'] ?? null,
        ':parttime_income_period' => $_POST['parttime_income_period'] ?? null,
        ':special_abilities' => $_POST['special_abilities'] ?? null,
        ':special_activities' => $_POST['special_activities'] ?? null,
        ':special_activities1' => $_POST['special_activities1'] ?? null,
        ':special_activities2' => $_POST['special_activities2'] ?? null,
        ':awards' => $_POST['awards'] ?? null,
        ':awards_year' => $_POST['awards_year'] ?? null,
        ':awards1' => $_POST['awards1'] ?? null,
        ':awards_year1' => $_POST['awards_year1'] ?? null,
        ':future_goals' => $_POST['future_goals'] ?? null,
        ':emergency_contact_name' => $_POST['emergency_contact_name'] ?? null,
        ':emergency_contact_relevant' => $_POST['emergency_contact_relevant'] ?? null,
        ':emergency_contact_house' => $_POST['emergency_contact_house'] ?? null,
        ':emergency_contact_ally' => $_POST['emergency_contact_ally'] ?? null,
        ':emergency_contact_moo' => $_POST['emergency_contact_moo'] ?? null,
        ':emergency_contact_road' => $_POST['emergency_contact_road'] ?? null,
        ':emergency_contact_subdistrict' => $_POST['emergency_contact_subdistrict'] ?? null,
        ':emergency_contact_district' => $_POST['emergency_contact_district'] ?? null,
        ':emergency_contact_province' => $_POST['emergency_contact_province'] ?? null,
        ':emergency_contact_postcode' => $_POST['emergency_contact_postcode'] ?? null,
        ':emergency_contact_house_no' => $_POST['emergency_contact_house_no'] ?? null,
        ':emergency_contact_phone' => $_POST['emergency_contact_phone'] ?? null,
        ':scholarship_required' => $_POST['scholarship_required'] ?? null,
        ':scholarship_amount_description' => $_POST['scholarship_amount_description'] ?? null,
        ':signature_scholarship' => $_POST['signature_scholarship'] ?? null,
        ':signature_name' => $_POST['signature_name'] ?? null,
        ':signature_date' => $_POST['signature_date'] ?? null,
        ':signature_month' => $_POST['signature_month'] ?? null,
        ':signature_year' => $_POST['signature_year'] ?? null,
        ':student_image' => $student_image ? $student_image : "", // กำหนดค่า "" หากไม่มีการอัปโหลด
        ':id_card_image' => $id_card_image ? $id_card_image : "",
        ':average_grade_image' => $average_grade_image ? $average_grade_image : "",
        ':bank_account_image' => $bank_account_image ? $bank_account_image : "",
        ':describe_scholarship' => $_POST['describe_scholarship'] ?? null,
        ':fileUpload1' => !empty($fileUpload1) ? $fileUpload1 : null,
        ':landmarks' => $_POST['landmarks'] ?? null,
        ':directions' => $_POST['directions'] ?? null,
        ':fileUpload2' => !empty($fileUpload2) ? $fileUpload2 : null,
        ':fileUpload3' => !empty($fileUpload3) ? $fileUpload3 : null,
        ':fileUpload4' => !empty($fileUpload4) ? $fileUpload4 : null,
        ':logo_photo' => $logo_photo
        
        
    ];
    // ตรวจสอบและสร้างโฟลเดอร์ log หากไม่มี
    $logDir = 'C:/xampp/htdocs/newcompany/log';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true); // สร้างโฟลเดอร์พร้อมกำหนดสิทธิ์
    }
    // ตรวจสอบและสร้างไฟล์ log หากไม่มี
    $logFilePath = $logDir . '/error.log';
    if (!file_exists($logFilePath)) {
        file_put_contents($logFilePath, ""); // สร้างไฟล์ log ว่าง
    }

    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    
    // Execute SQL
    if ($stmt->execute()) {
        $_SESSION['success'] = "บันทึกข้อมูลสำเร็จ";

        // Redirect หลังจากบันทึกสำเร็จ
        header("Location: scholarship_list.php?status=success");
        exit();
    } else {
        // บันทึก error ลง log แต่ไม่แสดง SQL error บนเว็บ
        $errorInfo = $stmt->errorInfo();
        error_log("SQL Error: " . implode(" ", $errorInfo), 3, "logs/sql_errors.log");

        $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง";
        header("Location: scholarship_list.php?status=error");
        exit();
    }
} catch (Exception $e) {
    // บันทึก error ลง log
    error_log("Exception caught: " . $e->getMessage(), 3, "logs/general_errors.log");

    $_SESSION['error'] = "เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่";
    header("Location: scholarship_list.php?status=error");
    exit();
}
?>