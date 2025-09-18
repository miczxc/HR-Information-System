<?php

 $isDuplicateEmail = checkDuplicateEmail($employee["email"], $pdo);

        if($isDuplicateEmail["isExist"]){
            return $response = [
                "success" => false,
                "message" => $isDuplicateEmail["message"]
            ];
        }


?>