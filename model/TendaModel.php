<?php


class TendaModel
{
    public $db = null;
    
    public function __construct()
    {
        $this->db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USERNAME, DB_PASSWORD);
    }

    public function getAllTendas()
    {
        return $this->db->query('SELECT * FROM Tendas');
    }

    public function getAllAvaliableTendas()
    {
        return $this->db->query('SELECT * FROM Tendas WHERE status=\'disponivel\'');
    }
}

?>

