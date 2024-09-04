<?php

class Feedback
{
    private $conn;
    private $table_name = "feedback";

    private $user_id;
    private $username;
    private $feedback_id;
    private $rating;
    private $comment;
    private $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    //getters

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getUsername()
    {
        return $this->username;
    }


    public function getFeedbackId()
    {
        return $this->feedback_id;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }



    //setters

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setFeedbackId($feedback_id)
    {
        $this->feedback_id = $feedback_id;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }




    public function createFeedback()
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, username, feedback_id, rating, comment, created_at) VALUES (:user_id, :username, :feedback_id, :rating, :comment, :created_at)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':feedback_id', $this->feedback_id);
        $stmt->bindParam(':rating', $this->rating);
        $stmt->bindParam(':comment', $this->comment);
        $stmt->bindParam(':created_at', $this->created_at);

        return $stmt->execute();
    }

    public function readFeedback()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    public function deleteFeedback($feedback_id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE feedback_id = :feedback_id";
        $stmt = $this->conn->prepare($query);

        // Bind the batch_id parameter
        $stmt->bindParam(':feedback_id', $feedback_id);


        // Execute the query
        if ($stmt->execute()) {
            header("Location: admin_reviews.php");
            exit();
        } else {
            // Debugging output
            print_r($stmt->errorInfo());
            return false;
        }
    }
}
