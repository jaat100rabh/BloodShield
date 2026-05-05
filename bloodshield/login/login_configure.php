<?php
include './con.php';
session_start();
$phone=$_POST['phone'];
$passowrd=$_POST['passowrd'];
$result= mysqli_query($con, "select*from login where phone='$phone' and password='$password'")or die("failed to login". mysqli_connect_error());
$row= mysqli_fetch_array($result);
if($row['phone']==$phone && $row['password']==$password){
    $_SESSION['user_token'] = $login;
    header("location:./login/INDEX_1.php"); //echo"login success";
}
 else {
     $_SESSION['fail']="<p style='color:#ffffff;'>Sorry Login Has Failed! Please Enter Correct ID And PASSOWRD</p>";
     header("location:./login.php");
}
?>