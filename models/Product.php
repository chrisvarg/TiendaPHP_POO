<?php

require_once 'config/DataBase.php';

class Product 
{
    private $id;
    private $idCategory;
    private $name;
    private $description;
    private $price;
    private $stock;
    private $ofert;
    private $date;
    private $image;
    private $db;

    public function __construct()
    {
        $this->db = DataBase::connect();
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the value of idCategory
     */ 
    public function getIdCategory()
    {
        return $this->idCategory;
    }

    /**
     * Set the value of idCategory
     * @return  self
     */ 
    public function setIdCategory($idCategory)
    {
        $this->idCategory = $idCategory;
        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $this->db->escape_string($name);
        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $this->db->escape_string($description);
        return $this;
    }

    /**
     * Get the value of price
     */ 
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of price
     * @return  self
     */ 
    public function setPrice($price)
    {
        $this->price = $this->db->escape_string($price);
        return $this;
    }

    /**
     * Get the value of stock
     */ 
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set the value of stock
     * @return  self
     */ 
    public function setStock($stock)
    {
        $this->stock = $this->db->escape_string($stock);
        return $this;
    }

    /**
     * Get the value of ofert
     */ 
    public function getOfert()
    {
        return $this->ofert;
    }

    /**
     * Set the value of ofert
     * @return  self
     */ 
    public function setOfert($ofert)
    {
        $this->ofert = $this->db->escape_string($ofert);
        return $this;
    }

    /**
     * Get the value of date
     */ 
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date
     * @return  self
     */ 
    public function setDate($date)
    {
        $this->date = $this->db->escape_string($date);
        return $this;
    }

    /**
     * Get the value of image
     */ 
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the value of image
     * @return  self
     */ 
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM productos ORDER BY id DESC;";
        $products = $this->db->query($sql);

        return $products;
    }

    public function getOne()
    {
        $sql = "SELECT * FROM productos WHERE id = {$this->getId()}";
        $product = $this->db->query($sql);

        return $product->fetch_object();
    }

    public function getAllCategory()
    {
        $sql = "SELECT p.*, c.name AS 'catNombre' FROM productos p
                INNER JOIN categorias c ON c.id = p.id_categoria
                WHERE p.id_categoria = {$this->getIdCategory()}
                ORDER BY id  DESC";
        $products = $this->db->query($sql);

        // var_dump($products);
        // exit();
        return $products;
    }

    public function save()
    {
        $sql = "INSERT INTO productos 
               VALUES(NULL, '{$this->getIdCategory()}', '{$this->getName()}', '{$this->getDescription()}', 
               {$this->getPrice()}, {$this->getStock()}, NULL, CURDATE(), '{$this->getImage()}');";

        $save = $this->db->query($sql);

        $result = false;
        if($save) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

    public function delete()
    {
        $sql = "DELETE FROM productos WHERE id = {$this->getId()}";
        $delete = $this->db->query($sql);

        $result = false;
        if($delete) {
            $result = true;
        } else {
            $result = false;
        }

        return $result;        
    }

    public function edit()
    {
        $sql = "UPDATE productos SET name ='{$this->getName()}', description ='{$this->getDescription()}', 
                price ={$this->getPrice()}, stock ={$this->getStock()}, id_categoria ={$this->getIdCategory()} ";
        if ($this->getImage() != null) {
            $sql .= ", image ='{$this->getImage()}'";
        }
        $sql .= "WHERE id ={$this->getId()};";

        $save = $this->db->query($sql);
        
        $result = false;
        if($save) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

    public function getRamdon($limit)
    {
        $sql = "SELECT * FROM productos ORDER BY RAND() LIMIT $limit ;";
        $products = $this->db->query($sql);

        return $products;
    }

    public function updateStockProduct()
    {
        $current = $this->getStock();

        $sql = "UPDATE productos set stock = {$this->stock}
                WHERE id = {$this->getId()}";
        // echo $sql;
        // exit();
        $save = $this->db->query($sql);

        $result = false;
        if($save) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }
 

}