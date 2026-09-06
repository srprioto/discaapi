<?php

class Query
{
    private PDO $pdo;
    public array $columns = [];

    public function __construct()
    {
        $this->pdo = require __DIR__ . '/conect.php';
    }

    public function run(string $sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $this->columns = [];
        for ($i = 0; $i < $stmt->columnCount(); $i++) {
            $meta = $stmt->getColumnMeta($i);
            $this->columns[] = $meta['name'];
        }

        if ($stmt->columnCount() > 0) {
            return $stmt->fetchAll();
        }

        return $stmt->rowCount();
    }
}




// // SELECT simple
// (new Query())->run("SELECT * FROM FACT_DISC_SNAPSHOT_CERT_EESS");

// // SELECT con parámetros
// (new Query())->run("SELECT * FROM tabla WHERE id = ?", [5]);

// // INSERT
// (new Query())->run("INSERT INTO tabla (nombre) VALUES (?)", ['Juan']);

// // UPDATE
// (new Query())->run("UPDATE tabla SET nombre = ? WHERE id = ?", ['Pedro', 5]);

// // CREATE
// (new Query())->run("CREATE TABLE test (id INT)");

// // Stored Procedure con parámetros
// (new Query())->run("{CALL sp_mi_procedimiento(?, ?)}", [1, 'valor']);

// // Función escalar o de tabla
// (new Query())->run("SELECT dbo.mi_funcion(?) AS resultado", [10]);