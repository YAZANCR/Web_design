<?php
require 'config.php';
require 'helpers/image_upload.php';
require 'helpers/validation.php';
require 'includes/app.php';
$keyword = trim($_GET['keyword'] ?? '');
$category_id = (int)($_GET['category_id'] ?? 0);
$categories = $pdo->query('SELECT id, name 
                        FROM categories ORDER BY name ASC')->fetchAll();
$sql = "SELECT products.id, products.name, products.price, products.quantity,
               categories.name AS category_name
        FROM products
        LEFT JOIN categories ON products.category_id = categories.id
        WHERE 1 = 1";
$params = [];
if ($keyword !== '') {
    $sql .= " AND products.name LIKE :keyword";
    $params[':keyword'] = '%' . $keyword . '%';
}
if ($category_id > 0) {
    $sql .= " AND products.category_id = :category_id";
    $params[':category_id'] = $category_id;
}
$sql .= " ORDER BY products.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بحث المنتجات</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
<header class="page-title">
    <h1>بحث المنتجات</h1>
    <p>مثال على GET مع Prepared Statement.</p>
</header>
<nav class="nav">
    <a href="index.php">الرئيسية</a>
    <a href="01_test_connection.php">اختبار الاتصال</a>
    <a href="02_show_products.php">عرض المنتجات</a>
    <a href="03_add_product.php">إضافة منتج</a>
    <a href="04_search_products.php">بحث المنتجات</a>
</nav>
<section class="card">
    <form method="GET" action="" class="search-form">
        <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="اكتب اسم المنتج">
        <select name="category_id">
            <option value="0">كل التصنيفات</option>
            <?php foreach ($categories as $category): ?>
<option value="<?= e($category['id']) ?>" <?= ((int)$category['id'] === $category_id) ? 'selected' : '' ?>>
                    <?= e($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">بحث</button>
    </form>
</section>
<section class="card">
    <h2>نتائج البحث</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>الرقم</th>
                    <th>المنتج</th>
                    <th>التصنيف</th>
                    <th>السعر</th>
                    <th>الكمية</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($products)): ?>
                <tr><td colspan="5">لا توجد نتائج مطابقة.</td></tr>
            <?php endif; ?>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= e($product['id']) ?></td>
                    <td><?= e($product['name']) ?></td>
                    <td><?= e($product['category_name']) ?></td>
                    <td><?= e($product['price']) ?> $</td>
                    <td><?= e($product['quantity']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
</div>
</body>
</html>
