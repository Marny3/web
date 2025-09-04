<?php
$conn = mysqli_connect("localhost","root","","dating_app");

if(!$conn){
    echo "Connection Failed " . mysqli_connect_error() or die();
}