<?php
include '../../models/comments.php';

class commentsController
{
    private $conn;
    public comments $comment;

    public function __construct($dbConn)
    {
        $this->conn = $dbConn;
        $this->comment = new comments();
    }

    function getAllComms()
    {
        $stmt = $this->conn->prepare("SELECT * FROM comments 
            JOIN users ON UserId = comm_user_id
            JOIN articles ON art_id = comm_art_id
            ORDER BY comm_art_id");

        $stmt->execute();

        return $stmt;
    }

    function getSingleComm()
    {
        $stmt = $this->conn->prepare("SELECT * FROM comments
            JOIN users ON UserId = comm_user_id
            WHERE comm_id = :comm_id");

        $stmt->bindParam(":comm_id", $this->comment->comm_id);

        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->comment->comm_id = $result['comm_id'];
            $this->comment->comm_user_id = $result['comm_user_id'];
            $this->comment->comm_user_email = $result['UserEmail'];
            $this->comment->comm_text = $result['comm_text'];
            $this->comment->comm_state = $result['comm_state'];
        } else {
            return false;
        }
    }

    function createComm()
    {
        $comm_state = 0;

        $stmt = $this->conn->prepare("INSERT INTO comments(comm_text, comm_art_id, comm_user_id, comm_state)
            VALUE(:comm_text, :comm_art_id, :comm_user_id, :comm_state)");

        $stmt->bindParam(":comm_text", $this->comment->comm_text);
        $stmt->bindParam(":comm_art_id", $this->comment->comm_art_id);
        $stmt->bindParam(":comm_user_id", $this->comment->comm_user_id);
        $stmt->bindParam(":comm_state", $comm_state);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function updtComm()
    {
        $stmt = $this->conn->prepare("UPDATE comments SET comm_state = :comm_state
            WHERE comm_id = :comm_id");

        $stmt->bindParam(":comm_id", $this->comment->comm_id);
        $stmt->bindParam(":comm_state", $this->comment->comm_state);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function deleteComm()
    {
        $stmt = $this->conn->prepare("DELETE FROM comments WHERE comm_id = :comm_id");

        $stmt->bindParam(":comm_id", $this->comment->comm_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function getCommByArt()
    {
        $stmt = $this->conn->prepare("SELECT * FROM comments JOIN users ON UserId = comm_user_id 
        WHERE comm_art_id = :comm_art_id 
        AND comm_state = 1
        ORDER BY comm_created_at");

        $stmt->bindParam(":comm_art_id", $this->comment->comm_art_id);

        $stmt->execute();

        return $stmt;
    }
}
