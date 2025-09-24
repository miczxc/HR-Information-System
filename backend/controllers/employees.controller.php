<?php
    // include_once __DIR__ . "/../config/database.php";
    require_once __DIR__ . "/../utils/checkDuplicateEmailForEmployee.php";


    function getEmployees($pdo) {
    
        $query = "SELECT * FROM employees";
        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute();

            $datas = $stmt->fetchAll();
            $response = [
                "success" => true,
                "data" => $datas 
            ];
        } catch (PDOException $e) {
            $response = [
                "success" => false,
                "error" => $e->getMessage()
            ];
            }
            return $response;
    }

    function getEmployee($id, $pdo) {
        $query = "SELECT * FROM employees WHERE employee_id = :employee_id";

        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ":employee_id" => $id
            ]);

            $datas = $stmt->fetch();
            $response = [
                "success" => true,
                "data" => $datas 
            ];
        } catch (PDOException $e) {
            $response = [
                "success" => false,
                "error" => $e->getMessage()
            ];
            }
            return $response;
    }



    function insertEmployee($employee, $pdo){

        $isDuplicateEmail = checkDuplicateEmailForEmployee($employee["email"], $pdo);

        if($isDuplicateEmail["isExist"]){
            return $response = [
                "success" => false,
                "message" => $isDuplicateEmail["message"]
            ];
        }

        $query = "INSERT INTO employees (first_name, last_name, date_of_birth, gender, contact_email, phone_number, address, hire_date, employment_status, job_title, password_hash, is_admin) VALUES (:first_name, :last_name, :date_of_birth, :gender, :contact_email, :phone_number, :address, :hire_date, :employment_status, :job_title, :password_hash, :is_admin)";

        try {
            // i dont think if this is necessary
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($query);
            $hashedPassword = password_hash($employee["password"], PASSWORD_BCRYPT);

            $isInserted = $stmt->execute([
            ":first_name"        => $employee["firstName"],
            ":last_name"         => $employee["lastName"],
            ":date_of_birth"     => $employee["birthDate"],
            ":gender"            => $employee["gender"],
            ":contact_email"     => $employee["email"],
            ":phone_number"      => $employee["phoneNumber"],
            ":address"           => $employee["address"],
            ":hire_date"         => $employee["hireDate"],
            ":employment_status" => $employee["employmentStatus"],
            ":job_title"         => $employee["jobTitle"],
            ":password_hash"     => $hashedPassword,
            ":is_admin"           => $employee["isAdmin"]
             ]);

             if(!$isInserted){
                $pdo->rollBack();
                $response = [
                "success" => false,
                "message" => "Failed to insert new Employee"
             ];
             }

             
            $response = [
                 "success" => true,
                 "message" => "Successfully inserted the employee ID: {$pdo->lastInsertId()}"
                ];

            $pdo->commit(); 
        } catch (PDOException $e) {
            $response = [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }

        return $response;

    }

?>