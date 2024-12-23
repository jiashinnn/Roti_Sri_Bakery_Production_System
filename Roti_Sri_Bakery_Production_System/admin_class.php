<?php
session_start();
ini_set('display_errors', 1);
class Action
{
    private $db;

    public function __construct()
    {
        ob_start();
        include 'db_connect.php';

        $this->db = $conn;
    }
    function __destruct()
    {
        $this->db->close();
        ob_end_flush();
    }

    function login()
    {
        extract($_POST);
        $qry = $this->db->query("SELECT *, user_fullName as name FROM tbl_users where user_email = '" . $email . "' and user_password = '" . md5($password) . "' ");

        // Add debug information
        echo "Query executed: " . $qry->num_rows . " rows found\n";

        if ($qry->num_rows > 0) {
            $row = $qry->fetch_array();

            // // Debug: Print what we got from database
            // echo "Database row: ";
            // print_r($row);

            foreach ($row as $key => $value) {
                if ($key != 'user_password' && !is_numeric($key)) {
                    $_SESSION['login_' . $key] = $value;
                    // Debug: Print each session variable as it's set
                    echo "Setting session: login_" . $key . " = " . $value . "\n";
                }
            }

            // Debug: Print final session state
            // echo "Final session state: ";
            // print_r($_SESSION);

            return 1;
        } else {
            return 3;
        }
    }

    function logout()
    {
        session_destroy();
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }
        header("location:login.php");
    }

    function save_user()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('user_id', 'cpass')) && !is_numeric($k)) {
                if ($k == 'user_password') {
                    $v = md5($v);
                }
                if (empty($data)) {
                    $data .= " $k='$v' ";
                } else {
                    $data .= ", $k='$v' ";
                }
            }
        }

        if (empty($user_id)) {
            $save = $this->db->query("INSERT INTO tbl_users SET $data");
        } else {
            $save = $this->db->query("UPDATE tbl_users SET $data WHERE user_id = $user_id");
        }

        if ($save) {
            return 1;
        }
    }

    function update_user()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('user_id', 'cpass', 'table')) && !is_numeric($k)) {
                if ($k == 'user_password')
                    $v = md5($v);
                if (empty($data)) {
                    $data .= " $k='$v' ";
                } else {
                    $data .= ", $k='$v' ";
                }
            }
        }
        $check = $this->db->query("SELECT * FROM tbl_users where user_mail ='$email' " . (!empty($id) ? " and user_id != {$user_id} " : ''))->num_rows;
        if ($check > 0) {
            return 2;
            exit;
        }
        if (empty($id)) {
            $save = $this->db->query("INSERT INTO tbl_users set $data");
        } else {
            $save = $this->db->query("UPDATE tbl_users set $data where user_id = $user_id");
        }

        if ($save) {
            foreach ($_POST as $key => $value) {
                if ($key != 'user_password' && !is_numeric($key))
                    $_SESSION['login_' . $key] = $value;
            }
            return 1;
        }
    }
    function delete_user()
    {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM tbl_users where user_id = " . $user_id);
        if ($delete)
            return 1;
    }
}
