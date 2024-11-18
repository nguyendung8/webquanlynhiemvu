<?php

include 'config.php';

session_start();

$user_id = @$_SESSION['patient_id']; //tạo session người dùng thường

// Lấy danh sách danh mục
$categories = mysqli_query($conn, "SELECT * FROM `categories`") or die('Query failed');

// Xử lý tìm kiếm và lọc
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;
$price_filter = isset($_GET['price']) ? $_GET['price'] : '';
$difficulty_filter = isset($_GET['difficulty']) ? mysqli_real_escape_string($conn, $_GET['difficulty']) : '';

$query = "SELECT * FROM `courses` WHERE 1";

// Tìm kiếm theo tên
if ($search) {
    $query .= " AND `title` LIKE '%$search%'";
}

// Lọc theo danh mục
if ($category_filter) {
    $query .= " AND `category_id` = $category_filter";
}

// Lọc theo mức giá
if ($price_filter == 'free') {
    $query .= " AND `price` = 0";
} elseif ($price_filter == '0-1000000') {
    $query .= " AND `price` > 0 AND `price` < 1000000";
} elseif ($price_filter == '1000000-5000000') {
    $query .= " AND `price` >= 1000000 AND `price` <= 5000000";
} elseif ($price_filter == '>5000000') {
    $query .= " AND `price` > 5000000";
}

// Lọc theo độ khó
if ($difficulty_filter) {
    $query .= " AND `difficulty` = '$difficulty_filter'";
}

$courses = mysqli_query($conn, $query) or die('Query failed');

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Danh sách khóa học</title>

   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .card {
         border-radius: 12px !important;
      }
      .card-img-top {
         height: 203px;
         width: 305px;
         border-top-left-radius: 12px !important;
         border-top-right-radius: 12px !important;
      }
      .form-select {
         height: 84%;
      }
      .card-body {
         font-size: 16px;
      }
      .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Thêm hiệu ứng chuyển động và đổ bóng */
         }

         /* Khi hover, thẻ card sẽ nổi lên */
      .card:hover {
         cursor: pointer;
         transform: translateY(-10px); /* Nổi lên một chút */
         box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Thêm đổ bóng nhẹ để tạo hiệu ứng nổi */
      }
   </style>
</head>
<body>

<?php include 'student_header.php'; ?>

<section class="container my-5">
    <h1 class="text-center mb-4">Danh sách khóa học</h1>
    <!-- Tìm kiếm và Lọc -->
    <form action="" method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm khóa học..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-2">
            <select name="category" class="form-select">
                <option value="0">Tất cả danh mục</option>
                <?php while ($category = mysqli_fetch_assoc($categories)) { ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo ($category_filter == $category['id']) ? 'selected' : ''; ?>>
                        <?php echo $category['name']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="price" class="form-select">
                <option value="">Tất cả mức giá</option>
                <option value="free" <?php echo ($price_filter == 'free') ? 'selected' : ''; ?>>Miễn phí</option>
                <option value="0-1000000" <?php echo ($price_filter == '0-1000000') ? 'selected' : ''; ?>>Dưới 1,000,000 VNĐ</option>
                  <option value="1000000-5000000" <?php echo ($price_filter == '1000000-5000000') ? 'selected' : ''; ?>>1,000,000 - 5,000,000 VNĐ</option>
                  <option value=">5000000" <?php echo ($price_filter == '>5000000') ? 'selected' : ''; ?>>Trên 5,000,000</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="difficulty" class="form-select">
                <option value="">Tất cả độ khó</option>
                <option value="beginner" <?php echo ($difficulty_filter == 'beginner') ? 'selected' : ''; ?>>Dễ</option>
                <option value="intermediate" <?php echo ($difficulty_filter == 'intermediate') ? 'selected' : ''; ?>>Trung bình</option>
                <option value="advanced" <?php echo ($difficulty_filter == 'advanced') ? 'selected' : ''; ?>>Khó</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="new-btn btn-primary w-100">Tìm kiếm</button>
        </div>
    </form>

    <!-- Hiển thị danh sách khóa học -->
    <div class="row">
        <?php if (mysqli_num_rows($courses) > 0) { ?>
            <?php while ($course = mysqli_fetch_assoc($courses)) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?php echo $course['thumbnail']; ?>" class="card-img-top" alt="Course Thumbnail">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $course['title']; ?></h5>
                            <p class="card-text"><?php echo substr($course['description'], 0, 100); ?>...</p>
                            <p class="card-text"><strong>Giá:</strong> 
                                <?php echo $course['price'] == 0 ? 'Miễn phí' : number_format($course['price'], 0, ',', '.') . ' VNĐ'; ?>
                            </p>
                            <p class="card-text"><strong>Độ khó:</strong> 
                                <?php
                                    if ($course['difficulty'] == 'beginner') echo 'Dễ';
                                    elseif ($course['difficulty'] == 'intermediate') echo 'Trung bình';
                                    else echo 'Khó';
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="col-12">
                <p class="text-center fs-2">Không tìm thấy khóa học nào phù hợp!</p>
            </div>
        <?php } ?>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="js/slide_show.js"></script>

</body>
</html>
