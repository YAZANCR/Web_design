<?php

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>  Mini Store</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
<header class="hero">
    <h1>  ربط PHP مع MySQL</h1>
    <p>تطبيق تدريبي على مشروع متجر إلكتروني مصغّر.</p>
</header>
<nav class="nav">
    <a href="index.php">الرئيسية</a>
    <a href="01_test_connection.php">اختبار الاتصال</a>
    <a href="02_show_products.php">عرض المنتجات</a>
    <a href="03_add_product.php">إضافة منتج</a>
    <a href="04_search_products.php">بحث المنتجات</a>
    <a href="logout.php"> تسجيل الخروج</a>
    <a href="change_password.php">تغيير كلمة المرور</a>
</nav>
<section class="card">
<?php if(isset($_SESSION['admin_name'])) {
    ?>
<h1 style="color:red;">مرحباً بك، <?= htmlspecialchars($adminName) ?></h1>
<p>دورك الحالي: <?= htmlspecialchars($adminRole) ?></p>
<?php
}
?>
</section>
</div>
</body>
</html>
