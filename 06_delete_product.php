<?php
require 'config.php';
require 'helpers/image_upload.php';
require 'helpers/validation.php';
require 'includes/app.php';
// التحقق من وجود id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('رقم المنتج غير صحيح.');
}
$id = (int) $_GET['id'];
$stmt = $pdo->prepare('SELECT image FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();
// تنفيذ الحذف
$sql = "DELETE FROM products
        WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
// التحقق هل تم حذف سجل فعليًا
if ($stmt->rowCount() > 0) {
     // بعد حذف السجل من قاعدة البيانات، نحذف الصورة من المجلد إن وجدت.
    delete_product_image($product['image'] ?? null);
    header('Location: 02_show_products.php?success=deleted');
} else {
    header('Location: 02_show_products.php?error=not_found');
}
exit;