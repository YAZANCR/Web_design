<?php
// تحميل ملف الاتصال بقاعدة البيانات
require 'config.php';
require 'helpers/validation.php';
require 'includes/app.php';
// query() تستخدم لتنفيذ استعلام 
// SELECT DATABASE() → يعيد اسم قاعدة البيانات المستخدمة حاليًا.
// NOW() → يعيد التاريخ والوقت الحاليين من خادم MySQL.
// AS → يعطي اسمًا مستعارًا (Alias) للنتيجة.
$stmt = $pdo->query(
    'SELECT DATABASE() AS db_name, NOW() AS server_time'
);
// fetch() تستخدم لجلب صف واحد من النتائج.
$info = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار الاتصال بقاعدة البيانات</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
<header class="page-title">
    <h1>اختبار الاتصال بقاعدة البيانات</h1>
    <p>هذا المثال يتأكد أن PHP يستطيع الاتصال بقاعدة mini_store.</p>
</header>
<nav class="nav">
    <a href="index.php">الرئيسية</a>
    <a href="01_test_connection.php">اختبار الاتصال</a>
    <a href="02_show_products.php">عرض المنتجات</a>
    <a href="03_add_product.php">إضافة منتج</a>
    <a href="04_search_products.php">بحث المنتجات</a>
</nav>
<section class="success">
    <h2>تم الاتصال بنجاح</h2>
    <p>اسم قاعدة البيانات: <strong><?= e($info['db_name']) ?></strong></p>
    <p>وقت الخادم: <strong><?= e($info['server_time']) ?></strong></p>
</section>
</div>
</body>
</html>
