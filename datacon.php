<?php
 $dbServername= "localhost";
 $dbUsername= "root";
 $dbPassword= "";
 $dbName= "project_finder";

 $conn= mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
 if (!$conn) {
     die("Database connection failed: " . mysqli_connect_error());
 }
