<?php
session_start();
include './con.php';
$name=$_REQUEST['name'];
$email=$_REQUEST['email'];
$phone=$_REQUEST['phone'];
$password=$_REQUEST['password'];
$q="insert into login(name,email,phone,password)values('$name','$email','$phone','$password');";
if($con->query($q))
{
 $_SESSION['save']="<p>success</p>";  //echo"data save";  
 header("location:./login.php");
}
else
{
 $_SESSION['save']="<p>Data not save</p>"; //echo"data not save";  
}
?>
