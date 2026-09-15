<?php
// دوال رفع وحذف صورة المنتج.
function upload_product_image(string $fieldName, array &$errors, ?string $oldImage = null): ?string
{
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return $oldImage;
    }
    $file = $_FILES[$fieldName];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'حدث خطأ أثناء رفع الصورة.';
        return $oldImage;
    }
    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxSize) {
        $errors[] = 'حجم الصورة يجب ألا يتجاوز 2MB.';
        return $oldImage;
    }
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!array_key_exists($mimeType, $allowedTypes)) {
        $errors[] = 'نوع الصورة غير مسموح. الأنواع المسموحة: JPG, PNG, WEBP, GIF.';
        return $oldImage;
    }
    $uploadDir = dirname(__DIR__) . '/uploads/products';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }
    $extension = $allowedTypes[$mimeType];
    $fileName = 'product_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
    $destination = $uploadDir . '/' . $fileName;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $errors[] = 'تعذر حفظ الصورة داخل مجلد uploads/products.';
        return $oldImage;
    }
    // إذا تم رفع صورة جديدة أثناء التعديل، نحذف الصورة القديمة لتجنب تراكم الملفات.
    if ($oldImage !== null && $oldImage !== '') {
        delete_product_image($oldImage);
    }
    return 'uploads/products/' . $fileName;
}

function delete_product_image(?string $imagePath): void
{
    if ($imagePath === null || $imagePath === '') {
        return;
    }
    $uploadRoot = realpath(dirname(__DIR__) . '/uploads/products');
    $fullPath = realpath(dirname(__DIR__) . '/' . $imagePath);
    // حماية بسيطة: لا نحذف إلا إذا كان الملف داخل مجلد صور المنتجات.
    if ($uploadRoot && $fullPath && strpos($fullPath, $uploadRoot) === 0 && is_file($fullPath)) {
        unlink($fullPath);
    }
}
