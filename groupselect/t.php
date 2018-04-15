<?php
session_start();
include_once 'C:\xampp\htdocs\moodle\mod\groupselect\classes\event\check.php';
if (isset($_POST['addStudent'])) {
    $id = $_POST['email'];
    $group_id = $_SESSION['groupid'];
    $check = new mod_groupselect\event\check();
    $check->check_student($id,$group_id);
}
?>
<!DOCTYPE html>
<html>
<body>
    <form action="#" method="post"enctype="multipart/form-data">
        add student:
        <input type="number" name="email">
        <input  type="submit" name="addStudent">
    </form>;

</body>
</html>