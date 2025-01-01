<?php

session_start();

session_destroy();

header("Location: LogInPage.html");
exit();


?>