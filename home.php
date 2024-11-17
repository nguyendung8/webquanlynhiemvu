<?php

   include 'config.php';

   session_start();

   $user_id = @$_SESSION['patient_id']; //tạo session người dùng thường  


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Trang chủ</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
 
</head>
<body>
   
<?php include 'student_header.php'; ?>

Day la trang chu

<script src="js/script.js"></script>
<script src="js/slide_show.js"></script>

</body>
</html>