<?php

ini_set('default_charset', 'utf8');

/**
 * Class sql_parser
 * #author Felipe Lima <felipe@felipelima.eti.br>
 */
class sql_parser
{
    /**
     * @return \PDO
     */
    public function sql_parser_conn()
    {
        try {
            if ($_SERVER["SERVER_ADDR"] == "127.0.0.1") {
                $hostname = "127.0.0.01";
                $database = "database";
                $username = "root";
                $password = "root";
                $conn     = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
                $conn->exec("set names utf8");
            }
        } catch (PDOException $e) {
            $_SESSION["maintenance"] = true;
        }
        return $conn;
    }

    /**
     * @param $conn
     * @return null
     */
    public function sql_parser_conn_end($conn)
    {
        return $conn = null;
    }

    // EXECUTE COMMAND SQL -> no RETURN

    /**
     * @param $sql
     * @param $isConn
     * @return mixed
     */
    public function sql_parser_execute($sql, $isConn)
    {
        $stmt = $isConn->prepare($sql);
        $stmt->execute();
        $id = $isConn->lastInsertId();

        return $id;
    }

    /**
     * @param $sql
     * @param $isConn
     * @return mixed
     */
    public function sql_parser_fetch($sql, $isConn)
    {
        $stmt = $isConn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>