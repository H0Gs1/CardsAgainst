<?php
  $servername = "sql15.cpt2.host-h.net";
  $username = "elitewmzsu_7";
  $password = "yEsYN4LEs7M4Q8hBGJi8";
  $database = "elitewmzsu_db7";

  // Create a new mysqli connection
  $conn = new mysqli($servername, $username, $password, $database);

  // Check connection
  if ($conn->connect_error) {
      die("Database connection failed: " . $conn->connect_error);
  }

  // Return the mysqli connection
  return $conn;
?>