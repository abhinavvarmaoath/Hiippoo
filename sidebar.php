<?php
session_start();
echo "My name is</br>";
echo htmlspecialchars($_SESSION['username']);
?>