<?php
session_start();
session_unset();
session_destroy();
header("Location: /hospital_php/login.php");
exit;
