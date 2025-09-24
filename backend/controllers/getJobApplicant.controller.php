<?php

     function getJobApplicants($pdo) {
    
        $query = "SELECT * FROM recruitment_candidates";
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

    function getJobApplicant($id, $pdo) {
        $query = "SELECT * FROM recruitment_candidates WHERE candidate_id = :candidate_id";

        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ":candidate_id" => $id
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

?>