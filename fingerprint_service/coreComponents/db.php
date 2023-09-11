<?php

class Database
{
    private const host = "host.docker.internal";
    private const user = "root";
    private const password = "12345678";
    private const database = "fingerprint";
    private $connection;

    function __construct()
    {
        $this->connection = new mysqli(self::host, self::user, self::password, self::database, 3300);
        if (mysqli_connect_errno()) {
            printf("Connection Failed: %s\n",  mysqli_connect_errno());
            exit();
        }
    }

    function getUserInfo($username): array
    {
        $sql_query = "SELECT * FROM users WHERE username=?";
        $param_type = "s";
        $param_array = [$username];
        $result = $this->select($sql_query, $param_type, $param_array);
        return $result;
    }

    function select($sql_query, $sql_param_type = "", $param_array = array())
    {
        if ($statement = $this->connection->prepare($sql_query)) {
            if (!empty($sql_param_type) && !empty($param_array)) {
                $this->bindQueryParams($statement, $sql_param_type, $param_array);
            }

            $statement->execute();
            // $statement->free_result();
            $sql_query_result = $statement->get_result();
            error_log(json_encode(["sql_query_result" => $sql_query_result, "error" => mysqli_error($this->connection)]));
            if ($sql_query_result->num_rows > 0) {
                while ($row = $sql_query_result->fetch_assoc()) {
                    $result_set[] = $row;
                }
            }

            if (!empty($result_set)) {
                return $result_set;
            }
        }
    }

    function count($sql_query, $sql_param_type = "", $param_array = array())
    {
        if ($statement = $this->connection->prepare($sql_query)) {
            if (!empty($sql_param_type) && !empty($param_array)) {
                $this->bindQueryParams($statement, $sql_param_type, $param_array);
            }

            $statement->execute();
            $sql_query_result = $statement->get_result();

            return $sql_query_result->num_rows;
        }
    }

    function insert($sql_query, $sql_param_type = "", $param_array = array())
    {
        if ($statement = $this->connection->prepare($sql_query)) {
            $this->bindQueryParams($statement, $sql_param_type, $param_array);
            $statement->execute();
            $insert_id = $statement->insert_id;
            return $insert_id;
        }
    }

    function execute_count($sql_query)
    {
        if ($result = $this->connection->query($sql_query)) {
            $sql_query_result = $result->fetch_row();
            return $sql_query_result[0];
        }
    }
    function select2($sql_query, $sql_param_type = "", $param_array = array())
    {
        if ($result = $this->connection->query($sql_query)) {
            $sql_query_result = $result->fetch_all(MYSQLI_ASSOC);
            return $sql_query_result;
        }
    }
    function execute($sql_query)
    {
        if ($statement = $this->connection->prepare($sql_query)) {
            //pass
            if (!empty($sql_param_type) && !empty($param_array)) {
                $this->bindQueryParams($statement, $sql_param_type, $param_array);
            }

            $statement->execute();
            $sql_query_result = $statement->get_result();
            return $sql_query_result;
        }
    }

    function update($sql_query, $sql_param_type = "", $param_array = array())
    {
        if ($statement = $this->connection->prepare($sql_query)) {
            if (!empty($sql_param_type) and !(empty($param_array))) {
                $this->bindQueryParams($statement, $sql_param_type, $param_array);
                $statement->execute();
                $statement->store_result();
                return $statement->affected_rows;
            }
        }
        return 0;
    }

    function bindQueryParams($statement, $sql_param_type, $param_array = array())
    {
        $param_value_reference[] = &$sql_param_type;
        for ($i = 0; $i < count($param_array); $i++) {
            $param_value_reference[] = &$param_array[$i];
        }

        call_user_func_array(array($statement, 'bind_param'), $param_value_reference);
    }

    function __destruct()
    {
        $this->connection->close();
    }
}
