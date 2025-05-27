<?php
   include 'config.php';
   session_start();
   $admin_id = $_SESSION['admin_id']; 
   if(!isset($admin_id)){
      header('location:login.php');
   }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Trang quản trị</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="dashboard">
   <h1 class="title">Bảng thông tin</h1>
   <div class="box-container">

      <div style="height: -webkit-fill-available;" class="box">
         <?php
            $select_tasks = mysqli_query($conn, "SELECT * FROM `tasks` WHERE status = 'Đang thực hiện'") or die('query failed');
            $number_of_ongoing_tasks = mysqli_num_rows($select_tasks);
         ?>
         <h3><?php echo $number_of_ongoing_tasks; ?></h3>
         <p>Nhiệm vụ đang thực hiện</p>
      </div>

      <div style="height: -webkit-fill-available;" class="box">
         <?php 
            $select_completed = mysqli_query($conn, "SELECT * FROM `tasks` WHERE status = 'Hoàn thành'") or die('query failed');
            $number_of_completed = mysqli_num_rows($select_completed);
         ?>
         <h3><?php echo $number_of_completed; ?></h3>
         <p>Nhiệm vụ đã hoàn thành</p>
      </div>

      <div style="height: -webkit-fill-available;" class="box">
         <?php 
            $select_high_priority = mysqli_query($conn, "SELECT * FROM `tasks` WHERE priority = 'Cao'") or die('query failed');
            $number_of_high_priority = mysqli_num_rows($select_high_priority);
         ?>
         <h3><?php echo $number_of_high_priority; ?></h3>
         <p>Nhiệm vụ ưu tiên cao</p>
      </div>

      <div style="height: -webkit-fill-available;" class="box">
         <?php 
            $select_upcoming_events = mysqli_query($conn, "SELECT * FROM `events` WHERE start_time > NOW()") or die('query failed');
            $number_of_upcoming_events = mysqli_num_rows($select_upcoming_events);
         ?>
         <h3><?php echo $number_of_upcoming_events; ?></h3>
         <p>Sự kiện sắp diễn ra</p>
      </div>

      <div style="height: -webkit-fill-available;" class="box">
         <?php 
            $select_collaborations = mysqli_query($conn, "SELECT * FROM `collaborations`") or die('query failed');
            $number_of_collaborations = mysqli_num_rows($select_collaborations);
         ?>
         <h3><?php echo $number_of_collaborations; ?></h3>
         <p>Nhiệm vụ được chia sẻ</p>
      </div>

   </div>
</section>

<script src="js/admin_script.js"></script>

</body>
</html>