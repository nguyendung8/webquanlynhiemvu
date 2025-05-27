<?php

   include 'config.php';

   session_start();

   $admin_id = $_SESSION['admin_id']; //tạo session admin

   if(!isset($admin_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }

   if(isset($_GET['delete'])){//xóa người dùng từ onclick href='delete'
      $delete_id = $_GET['delete'];
      mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'") or die('query failed');
      header('location:admin_users.php');
   }

   if(isset($_POST['update_user'])){
      $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
      $name = mysqli_real_escape_string($conn, $_POST['name']);
      $email = mysqli_real_escape_string($conn, $_POST['email']);

      $update_user = mysqli_query($conn, "UPDATE `users` SET 
         name = '$name', 
         email = '$email' 
         WHERE user_id = '$user_id'") or die('query failed');

      if($update_user){
         $message[] = 'Cập nhật thông tin thành công!';
      }else{
         $message[] = 'Cập nhật thông tin thất bại!';
      }
   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Người dùng</title>

   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
   <style>
       .action-btn {
         padding: 6px 12px;
         border-radius: 4px;
         font-size: 15px;
         margin-right: 5px;
         border: none;
         cursor: pointer;
         text-decoration: none;
      }
      label{
         font-size: 18px;
      }
      input{
         font-size: 18px;
      }
   </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="users">
   <h1 class="title"> Tài khoản người dùng </h1>

   <div class="box-container">
      <?php
         $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type != 'admin'") or die('query failed');
         while($fetch_users = mysqli_fetch_assoc($select_users)){
      ?>
      <div class="box">
         <p> Id người dùng : <span><?php echo $fetch_users['user_id']; ?></span> </p>
         <p> Tên người dùng : <span><?php echo $fetch_users['name']; ?></span> </p>
         <p> Email : <span><?php echo $fetch_users['email']; ?></span> </p>
         <div class="flex-btn">
            <button type="button" class="action-btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $fetch_users['user_id']; ?>">
               <i class="fas fa-edit"></i> Cập nhật
            </button>
            <?php if($fetch_users['user_type'] != 'admin'){ ?>
               <a href="admin_users.php?delete=<?php echo $fetch_users['user_id']; ?>" 
                  onclick="return confirm('Xóa người dùng này?');" 
                  class="action-btn  btn-danger">
                  <i class="fas fa-trash"></i> Xóa
               </a>
            <?php } ?>
         </div>
      </div>

      <!-- Modal Cập nhật -->
      <div class="modal fade" id="updateModal<?php echo $fetch_users['user_id']; ?>" tabindex="-1" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" style="font-size: 22px;">Cập nhật thông tin người dùng</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <form action="" method="post">
                  <div class="modal-body">
                     <input type="hidden" name="user_id" value="<?php echo $fetch_users['user_id']; ?>">
                     
                     <div class="mb-3">
                        <label for="name" class="form-label">Tên người dùng</label>
                        <input style="font-size: 15px;" type="text" class="form-control" id="name" name="name" 
                               value="<?php echo $fetch_users['name']; ?>" required>
                     </div>

                     <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input style="font-size: 15px;" type="email" class="form-control" id="email" name="email" 
                               value="<?php echo $fetch_users['email']; ?>" required>
                     </div>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="action-btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                     <button type="submit" name="update_user" class="action-btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật
                     </button>
                  </div>
               </form>
            </div>
         </div>
      </div>
      <?php
         }
      ?>
   </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/admin_script.js"></script>

</body>
</html>