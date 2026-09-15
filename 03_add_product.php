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
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name ASC')->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '0');
    $category_id = (int)($_POST['category_id'] ?? 0);
    if ($name === '') {
        $errors[] = 'اسم المنتج مطلوب.';
    } elseif (mb_strlen($name) < 3) {
        $errors[] = 'اسم المنتج يجب ألا يقل عن 3 أحرف.';
    }
    if ($category_id <= 0) {
        $errors[] = 'يجب اختيار تصنيف المنتج.';
    }
    if ($price === '' || !is_numeric($price) || (float)$price <= 0) {
        $errors[] = 'السعر يجب أن يكون رقمًا أكبر من صفر.';
    }
    if ($quantity === '' || !ctype_digit((string)$quantity)) {
        $errors[] = 'الكمية يجب أن تكون عددًا صحيحًا غير سالب.';
    }
    $imagePath = upload_product_image('image', $errors);
    if (empty($errors)) {
        $sql = "INSERT INTO products (category_id, name, description, price, quantity, image)
                VALUES (:category_id, :name, :description, :price, :quantity, :image)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':category_id' => $category_id,
            ':name' => $name,
            ':description' => $description,
            ':price' => (float)$price,
            ':quantity' => (int)$quantity,
             ':image' => $imagePath,
        ]);
        $success = 'تمت إضافة المنتج بنجاح.';
        $name = '';
        $description = '';
        $price = '';
        $quantity = '0';
        $category_id = 0;
    }
    else
    {
        render_errors($errors);
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة منتج</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
<header class="page-title">
    <h1>إضافة منتج جديد</h1>
    <p>مثال على POST + Validation + Prepared Statement + INSERT.</p>
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
<section class="success"><strong><?= e($success) ?></strong>
 <a href="02_show_products.php">عرض المنتجات</a></section>
<?php endif; ?>
<section class="card">
    <form method="POST" action="" class="form" enctype="multipart/form-data">
        <label>اسم المنتج</label>
        <input type="text" name="name" value="<?= e($name) ?>" placeholder="مثال: سماعات بلوتوث">
        <label>التصنيف</label>
        <select name="category_id">
            <option value="0">اختر التصنيف</option>
            <?php foreach ($categories as $category): ?>
<option value="<?= e($category['id']) ?>" <?= ((int)$category['id'] === (int)$category_id) ? 'selected' : '' ?>>
    <?= e($category['name']) ?>
    </option>
            <?php endforeach; ?>
        </select>
        <label>السعر</label>
        <input type="number" step="0.01" name="price" value="<?= e($price) ?>" placeholder="0.00">
        <label>الكمية</label>
        <input type="number" name="quantity" value="<?= e($quantity) ?>" min="0">
        <label>الوصف</label>
        <textarea name="description" rows="4" placeholder="وصف مختصر للمنتج"><?= e($description) ?></textarea>
        <label>صورة المنتج</label>
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
    <p class="hint">الحد الأقصى 2MB، والأنواع المسموحة: JPG, PNG, WEBP, GIF.</p>

        <button type="submit">حفظ المنتج</button>
    </form>
</section>
</div>
</body>
</html>
