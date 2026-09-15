<?php
require 'config.php';
require 'helpers/image_upload.php';
require 'helpers/validation.php';
require 'includes/app.php';
$errors = [];
$success = '';
$name = '';
$description = '';
$price = '';
$quantity = '0';
$category_id = 0;
// التحقق من وجود رقم المنتج في الرابط
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die('رقم المنتج غير صحيح.');
}
// جلب التصنيفات
$categories = $pdo->query(
    'SELECT id, name FROM categories ORDER BY name ASC'
)->fetchAll();
// جلب بيانات المنتج
$sql = "SELECT id, category_id, name, description, price, quantity,image
        FROM products
        WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id' => $id
]);
$product = $stmt->fetch();
// التحقق من وجود المنتج
if (!$product) {
    die('المنتج غير موجود.');
}
// تعبئة الحقول ببيانات المنتج
$name = $product['name'];
$description = $product['description'];
$price = $product['price'];
$quantity = $product['quantity'];
$category_id = (int)$product['category_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '0');
    $category_id = (int)($_POST['category_id'] ?? 0);
    // التحقق من اسم المنتج
    if ($name === '') {
        $errors[] = 'اسم المنتج مطلوب.';
    } elseif (mb_strlen($name) < 3) {
        $errors[] = 'اسم المنتج يجب ألا يقل عن 3 أحرف.';
    }
    // التحقق من التصنيف
    if ($category_id <= 0) {
        $errors[] = 'يجب اختيار تصنيف المنتج.';
    }
    // التحقق من السعر
    if ($price === '' || !is_numeric($price) || (float)$price <= 0) {
        $errors[] = 'السعر يجب أن يكون رقمًا أكبر من صفر.';
    }
    // التحقق من الكمية
    if ($quantity === '' || !ctype_digit((string)$quantity)) {
        $errors[] = 'الكمية يجب أن تكون عددًا صحيحًا غير سالب.';
    }
    $imagePath = upload_product_image('image', $errors, $product['image']);
    // إذا لم توجد أخطاء
    if (empty($errors)) {
        $sql = "UPDATE products
                SET category_id = :category_id,
                    name = :name,
                    description = :description,
                    price = :price,
                    quantity = :quantity,
                     image = :image
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':category_id' => $category_id,
            ':name' => $name,
            ':description' => $description,
            ':price' => (float)$price,
            ':quantity' => (int)$quantity,
            ':image' => $imagePath,
            ':id' => $id,
        ]);
        $success = 'تم تعديل المنتج بنجاح.';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل منتج</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
<header class="page-title">
    <h1>تعديل المنتج</h1>
    <p> Prepared Statement + UPDATE.</p>
</header>
<nav class="nav">
    <a href="index.php">الرئيسية</a>
    <a href="01_test_connection.php">اختبار الاتصال</a>
    <a href="02_show_products.php">عرض المنتجات</a>
    <a href="03_add_product.php">إضافة منتج</a>
    <a href="04_search_products.php">بحث المنتجات</a>
</nav>
<?php if (!empty($errors)): ?>
<section class="alert">
    <h2>يرجى تصحيح الأخطاء التالية:</h2>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= e($error) ?></li>
        <?php endforeach; ?>
    </ul>
</section>
<?php endif; ?>
<?php if ($success): ?>
<section class="success">
    <strong><?= e($success) ?></strong>
    <a href="02_show_products.php">عرض المنتجات </a>
</section>
<?php endif; ?>
<section class="card">
    <form method="POST" action="" class="form" enctype="multipart/form-data">
        <label> اسم المنتج</label>
        <input type="text" name="name" value="<?= e($name) ?>">
        <label> التصنيف</label>
        <select name="category_id">
            <option value="0"> اختر التصنيف </option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= e($category['id']) ?>"
            <?= ((int)$category['id'] === (int)$category_id) ? 'selected' : '' ?> >
                <?= e($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label>السعر</label>
        <input type="number" step="0.01" name="price"
            value="<?= e($price) ?>" placeholder="0.00" >
        <label>الكمية</label>
        <input type="number" name="quantity"
            value="<?= e($quantity) ?>" min="0" >
        <label> الوصف</label>
        <textarea  name="description"  rows="4"><?= e($description) ?></textarea>
         <label>الصورة الحالية</label>
    <?php if (!empty($product['image'])): ?>
        <img src="<?= e(base_url($product['image'])) ?>" class="preview-img" alt="صورة المنتج الحالية">
    <?php else: ?>
        <img src="<?= e(base_url('assets/no-image.svg')) ?>" class="preview-img" alt="لا توجد صورة">
    <?php endif; ?>

    <label>رفع صورة جديدة، اختياري</label>
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
    <p class="hint">إذا لم تختر صورة جديدة، سيحتفظ النظام بالصورة الحالية.</p>

        <button type="submit"> حفظ التعديلات  </button>
        <a href="02_show_products.php">إلغاء</a>
    </form>
</section>
</div>
</body>
</html>