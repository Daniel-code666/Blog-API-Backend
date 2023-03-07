<?php
include '../../models/articles.php';

class articlesController
{
    private $conn;
    public articles $article;

    public function __construct($dbConn)
    {
        $this->conn = $dbConn;
        $this->article = new articles();
    }

    public function getAllArticles()
    {
        $stmt = $this->conn->prepare("SELECT * FROM articles");

        $stmt->execute();

        return $stmt;
    }

    public function getSingleArticle()
    {
        $stmt = $this->conn->prepare("SELECT * FROM articles WHERE art_id = :art_id");

        $stmt->bindParam(":art_id", $this->article->art_id);

        if ($stmt->execute()) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->article->art_id = $data['art_id'];
            $this->article->art_name = $data['art_name'];
            $this->article->art_desc = $data['art_desc'];
            $this->article->art_img = $data['art_img'];
            $this->article->created_at = $data['created_at'];
            $this->article->art_img_ext = $data['art_img_ext'];
        } else {
            return false;
        }
    }

    public function createArticle()
    {
        $stmt = $this->conn->prepare("insert into articles(art_name, art_desc, art_img, art_img_ext) values(:art_name, :art_desc,
            :art_img, :art_img_ext)");

        $stmt->bindparam(":art_name", $this->article->art_name);
        $stmt->bindparam(":art_desc", $this->article->art_desc);
        $stmt->bindparam(":art_img", $this->article->art_img);
        $stmt->bindparam(":art_img_ext", $this->article->art_img_ext);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updtArticle()
    {
        // $imgFolder = 'C:\xampp\htdocs\Cosas\blog_api\front-end\img\articulos\\';

        $stmt = $this->conn->prepare("SELECT * FROM articles WHERE art_id = :art_id");

        $stmt->bindParam(":art_id", $this->article->art_id);

        $stmt->execute();

        $currData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (
            $currData['art_name'] != $this->article->art_name || $currData['art_desc'] != $this->article->art_desc ||
            $currData['art_img'] != $this->article->art_img
        ) {
            // if ($this->art_img != "") {
            //     $imgFolder = $imgFolder . $this->art_img;

            //     unlink($imgFolder);
            // }

            $stmt = $this->conn->prepare("UPDATE articles SET art_name = :art_name,  art_desc = :art_desc, 
            art_img = :art_img, art_img_ext = :art_img_ext WHERE art_id = :art_id");

            $stmt->bindParam(":art_name", $this->article->art_name);
            $stmt->bindParam(":art_desc", $this->article->art_desc);
            $stmt->bindParam(":art_img", $this->article->art_img);
            $stmt->bindParam(":art_img_ext", $this->article->art_img_ext);
            $stmt->bindParam(":art_id", $this->article->art_id);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function deleteArticle()
    {
        // $imgFolder = 'C:\xampp\htdocs\Cosas\blog_api\front-end\img\articulos\\';

        $stmt = $this->conn->prepare("SELECT art_img FROM articles WHERE art_id = :art_id");
        $stmt->bindParam(":art_id", $this->article->art_id);

        $stmt->execute();

        $img = $stmt->fetch(PDO::FETCH_ASSOC);

        // $imgFolder = $imgFolder . $img["art_img"];

        // unlink($imgFolder);

        $stmt = $this->conn->prepare("DELETE FROM articles WHERE art_id = :art_id");

        $stmt->bindParam(":art_id", $this->article->art_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
