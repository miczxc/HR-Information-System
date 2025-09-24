<?php
require_once __DIR__ . '/../config/database.php';

function checkDuplicateEmailForApplicant($email, $pdo){
        $query = "SELECT COUNT(*) AS total FROM recruitment_candidates WHERE email = :email";
        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute([":email" => $email]);
            $isExist = $stmt->fetch();

            if($isExist && $isExist["total"] > 0) {
                $response = [
                    "isExist" => true,
                    "message" => "$email: Email already existing"

                ];
            }else {
                $response = [
                    "isExist" => false,
                ];

            }

        } catch (PDOException $e) {
            $response = [
                    "isExist" => true,
                    "message" => "Error: {$e->getMessage()}"
                ];
        }
        return $response;
    }

function insertJobApplicant($pdo, $postData, $files) {
    // Absolute paths for saving
    $targetDirPhoto  = __DIR__ . "/../uploads/photos/";
    $targetDirResume = __DIR__ . "/../uploads/resumes/";

    // Ensure folders exist
    if (!is_dir($targetDirPhoto))  mkdir($targetDirPhoto, 0777, true);
    if (!is_dir($targetDirResume)) mkdir($targetDirResume, 0777, true);

    $requiredFields = ["position", "firstName", "midleName", "lastName", "address", "email", "phoneNumber"];

    // Validate fields
    foreach ($requiredFields as $field) {
        if (!isset($postData[$field]) || trim($postData[$field]) === "") {
            return [
                "success" => false,
                "message" => "The $field is missing"
            ];
        }
    }

    // Check duplicate email
    $isDuplicateEmail = checkDuplicateEmailForApplicant($postData["email"], $pdo);
    if ($isDuplicateEmail["isExist"]) {
        http_response_code(409);
        return [
            "success" => false,
            "message" => $isDuplicateEmail["message"]
        ];
    }

    // Unique filenames
    $photoName  = uniqid("photo_")  . "_" . basename($files["photo"]["name"]);
    $resumeName = uniqid("resume_") . "_" . basename($files["resume"]["name"]);

    // Absolute paths for moving
    $photoPathAbs  = $targetDirPhoto  . $photoName;
    $resumePathAbs = $targetDirResume . $resumeName;

    // Relative paths for DB
    $photoPathRel  = "uploads/photos/" . $photoName;
    $resumePathRel = "uploads/resumes/" . $resumeName;

    // Upload files
    if (!move_uploaded_file($files["photo"]["tmp_name"], $photoPathAbs)) {
        return ["success" => false, "message" => "Photo upload failed."];
    }

    if (!move_uploaded_file($files["resume"]["tmp_name"], $resumePathAbs)) {
        return ["success" => false, "message" => "Resume upload failed."];
    }

    // Insert into DB
    try {
        $stmt = $pdo->prepare("
            INSERT INTO recruitment_candidates 
                (job_applied_for, first_name, middle_name, last_name, address, email, phone_number, picture_path, resume_path, status) 
            VALUES 
                (:job_applied_for, :firstName, :midleName, :lastName, :address, :email, :phoneNumber, :photo, :resume, :status)
        ");

        $stmt->execute([
            ':job_applied_for' => $postData['position'],
            ':firstName'       => $postData['firstName'],
            ':midleName'       => $postData['midleName'],
            ':lastName'        => $postData['lastName'],
            ':address'         => $postData['address'],
            ':email'           => $postData['email'],
            ':phoneNumber'     => $postData['phoneNumber'],
            ':photo'           => $photoPathRel,   // ✅ relative path
            ':resume'          => $resumePathRel,  // ✅ relative path
            ':status'          => "New"
        ]);

        return [
            "success" => true,
            "message" => "Applicant data uploaded successfully!"
        ];

    } catch (PDOException $e) {
        return [
            "success" => false,
            "message" => "Database error: " . $e->getMessage()
        ];
    }
}

