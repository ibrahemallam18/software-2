<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DB_Class
 *
 * @author world
 */
class DB_Class {
    //put your code here
    private $host = "localhost";
    private $username = "moodleuser";
    private $password = "yara123";
    private static $instance; //for singletone DP
    private $db_name = "moodle"; //your database name 
    private $database_connection; // 

    public function __construct() {
        $this->database_connection = $this->database_connect($this->host, $this->username, $this->password);
        $this->database_select($this->db_name);
    }

    /*
     * de l single tone design pattern
     * ene 3yza kol l classes tst5dm nfs l object l fl class 3shan mn3mlsh objects 
     * kteer mno 
     * w 3shan byst3mlo nfs el 7aga
     * el function ba2a m3naha  en lw el object bta3 el DB_class lsa mt3mlsh
     * h3mlo ba2a
     */

    public static function getInstance() {// create only one object for databse 
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Connect to database
     *
     * @param string $database_host
     * @param string $database_username
     * @param string $database_password
     * @return mysqli_connection link
     */
    private function database_connect($database_host, $database_username, $database_password) {
        $c = mysqli_connect($database_host, $database_username, $database_password);
        if ($c) {
            return $c;
        } else {
            die("Database connection error");
        }
    }

    /**
     * select a db
     *
     * @param string $database_name
     * @return mysqli link
     */
    private function database_select($database_name) {
        return mysqli_select_db($this->database_connection, $database_name)
                or die("no db is selecteted");
    }

    /**
     * Close db connection
     *
     */
    public function database_close() {
        if (!mysqli_close($this->database_connection)) {
            die("Connection close failed.");
        }
    }

    public function clean($str) {
        $str1 = trim($str); // remove 
        /* Magic Quotes, generally speaking, is the process of escaping special characters with a '\' to allow a string to be entered into a database. This is considered 'magic' because PHP can do this automatically for you if you have magic_quotes_gpc turned on.
          More specifically if magic_quotes_gpc is turned on for the copy of PHP you are using all Get, Post & Cookie variables (gpc, get it?) in PHP will already have special characters like ", ' and \ escaped so it is safe to put them directly into an SQL query. */

        if (get_magic_quotes_gpc()) {
            $str1 = stripslashes($str1);
        }
        return mysqli_real_escape_string($this->database_connection, $str1); //Clean any SQL words.
    }

    /**
     * Make query to the database
     *
     * @param string $database_query
     * @return sqlresult
     */
    public function database_query($database_query) {
        $query_result = mysqli_query($this->database_connection, $database_query);
        return $query_result;
    }

    /**
     * Executes query and returns query result (row, array)
     *
     * @param string $query   SQL query text
     *         
     * @access public
     * @return associated array
     */
    public function get_row($query) {
        if (!strstr(strtoupper($query), "LIMIT")) {
            $query .= " LIMIT 0,1";
        }
        if (!($res = $this->database_query($query))) {
            die("Database error: " . mysqli_error($this->database_connection) . "<br/>In query: " . $query);
        }
        return mysqli_fetch_assoc($res);
    }

    /**
     * Executes query result (table, array of array)
     *
     * @param string database_result

     * @access public
     * @return array of rows 
     */
    public function database_all_array($database_result) {
        $array_return = array();
        while ($row = mysqli_fetch_array($database_result)) {
            $array_return[] = $row;
        }
//        if(count($array_return)>0)
        return $array_return;
    }

    /**
     * Executes query result (table, array of array)
     *
     * @param string database_result

     * @access public
     * @return associated array of rows 
     */
    public function database_all_assoc($database_result) {

        while ($row = mysqli_fetch_assoc($database_result)) {
            $array_return[] = $row;
        }
        if (isset($array_return)) {
            return $array_return;
        }
    }

    /**
     * Returns number of rows in the result
     *
     * @param mixed $database_result
     * @return integer
     */
    public function database_affected_rows($database_result) {

        return mysqli_affected_rows($database_result);
    }

    /**
     * Returns number of rows in the result
     *
     * @param mixed $database_result
     * @return integer
     */
    public function database_num_rows($database_result) {

        return mysqli_num_rows($database_result);
    }

#-#############################################
# desc: does an update query with an array
# param: table, assoc array with data (not escaped), where condition (optional. if none given, all records updated)
# returns: (query_id) for fetching results etc

    public function update($table, $data, $where = '1') {
        $q = "UPDATE `$table` SET ";

        foreach ($data as $key => $val) {
            if (strtolower($val) == 'null') {
                $q .= "`$key` = NULL, ";
            } elseif (strtolower($val) == 'now()') {
                $q .= "`$key` = NOW(), ";
            } else {
                $q .= "`$key`='" . $this->clean($val) . "', ";
            }
        }

        $q = rtrim($q, ', ') . ' WHERE ' . $where . ';';

        return $this->database_query($q);
    }

#-#update()
#-#############################################
# desc: does an insert query with an array
# param: table, assoc array with data (not escaped)
# returns: id of inserted record, false if error

    public function insert($table, $data) {
        $q = "INSERT INTO `$table` ";
        $v = '';
        $n = '';

        foreach ($data as $key => $val) {
            $n .= "`$key`, ";
            if (strtolower($val) == 'null') {
                $v .= "NULL, ";
            } elseif (strtolower($val) == 'now()') {
                $v .= "NOW(), ";
            } else {
                $v .= "'" . $val . "', ";
            }
        }

        $q .= "(" . rtrim($n, ', ') . ") VALUES (" . rtrim($v, ', ') . ");";

        if ($this->database_query($q)) {
            return mysqli_insert_id($this->database_connection);
        } else {
            return false;
        }
    }
}
