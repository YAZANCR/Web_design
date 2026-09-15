<?php
require 'config.php';
require 'helpers/image_upload.php';
require 'helpers/validation.php';
require 'includes/app.php';
$sql = "SELECT 
            products.id, 
            products.name, 
            products.description, 
            products.price, 
            products.quantity,
            products.image,  
            categories.name AS category_name, 
            products.created_at 
        FROM products 
        LEFT JOIN categories ON products.category_id = categories.id 
        ORDER BY products.id DESC";
$stmt = $pdo->query($sql);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض المنتجات</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
<header class="page-title">
    <h1>عرض منتجات المتجر</h1>
    <p>مثال على قراءة البيانات باستخدام SELECT و JOIN.</p>
</header>
<nav class="nav">
    <a href="index.php">الرئيسية</a>
    <a href="01_test_connection.php">اختبار الاتصال</a>
    <a href="02_show_products.php">عرض المنتجات</a>
    <a href="03_add_product.php">إضافة منتج</a>
    <a href="04_search_products.php">بحث المنتجات</a>
</nav>
<?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
<section class="success">
    <strong>تم حذف المنتج بنجاح.</strong>
</section>
<?php endif; ?>
<?php if (isset($_GET['error']) && $_GET['error'] === 'not_found'): ?>
<section class="alert">
    <strong>المنتج غير موجود أو تم حذفه مسبقًا.</strong>
</section>
<?php endif; ?>
<section class="card">
    <h2>قائمة المنتجات</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                <th>الصورة</th>
                    <th>الرقم</th>
                    <th>اسم المنتج</th>
                    <th>التصنيف</th>
                    <th>السعر</th>
                    <th>الكمية</th>
                    <th>تاريخ الإضافة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="7">لا توجد منتجات حاليًا.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($products as $product): ?>
                <tr>
                <td>
                <?php if (!empty($product['image'])): ?>
                        <img src="<?= e(base_url($product['image'])) ?>" class="product-img" alt="صورة المنتج">
                    <?php else: ?>
                        <img src="<?= e(base_url('assets/no-image.svg')) ?>" class="product-img" alt="لا توجد صورة">
                    <?php endif; ?>
                </td>
                 
                    <td><?= e($product['id']) ?> </td>
                    <td><?= e($product['name']) ?> </td>
                    <td><?= e($product['category_name']) ?></td>
                    <td><?= e($product['price']) ?> $</td>
                    <td><?= e($product['quantity']) ?></td>
                    <td><?= e($product['created_at']) ?></td>
                    <td>
                        <a href="05_edit_product.php?id=<?= e($product['id']) ?>">
                            تعديل
                        </a>
                         |
                        <a href="06_delete_product.php?id=<?= e($product['id']) ?>"
                        onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">
                            حذف
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
</div>
</body>
</html>