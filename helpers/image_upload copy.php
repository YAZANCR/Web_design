<?php
/**
 * دوال رفع وحذف صورة المنتج.
 * تحتوي هذه الملفات على المنطق الآمن للتعامل مع ملفات الصور (رفع، تحقق من الحجم والنوع، وحذف).
 */

/**
 * دالة رفع صورة المنتج مع التحقق من صحتها وحماية النظام، والتعامل مع الصورة القديمة عند التعديل.
 *
 * @param string      $fieldName حقل الإدخال المرتبط بالملف في نموذج HTML (مثل 'image').
 * @param array       $errors    مصفوفة لتخزين رسائل الخطأ إن وجدت (تمرير بالمرجع &).
 * @param string|null $oldImage  مسار الصورة القديمة (في حالة التعديل) لحذفها إذا تم رفع صورة جديدة.
 * @return string|null           يعيد مسار الصورة الجديدة النسبي، أو مسار الصورة القديمة عند الفشل، أو null.
 */
function upload_product_image(string $fieldName, array &$errors, ?string $oldImage = null): ?string
{
    // 1. التحقق مما إذا كان الملف غير مرفوع أو لم يتم اختيار ملف من الأساس
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return $oldImage; // إعادة الصورة القديمة إن وجدت وعدم إجراء أي تغيير
    }

    $file = $_FILES[$fieldName];

    // 2. التحقق من وجود أخطاء أخرى أثناء عملية الرفع من المتصفح
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'حدث خطأ أثناء رفع الصورة.';
        return $oldImage;
    }

    // 3. التحقق من حجم الملف (الحد الأقصى المسموح به هو 2 ميجابايت)
    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxSize) {
        $errors[] = 'حجم الصورة يجب ألا يتجاوز 2MB.';
        return $oldImage;
    }

    // 4. تحديد أنواع الملفات المسموح بها وما يقابلها من امتدادات
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    // 5. التحقق الفعلي من نوع الملف عبر فحص محتواه (MIME Type) وليس الاعتماد على امتداد الملف
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    // التحقق هل النوع المستخرج موجود ضمن قائمة الأنواع المسموحة
    if (!array_key_exists($mimeType, $allowedTypes)) {
        $errors[] = 'نوع الصورة غير مسموح. الأنواع المسموحة: JPG, PNG, WEBP, GIF.';
        return $oldImage;
    }

    // 6. تحديد مجلد الحفظ والتأكد من وجوده، وإذا لم يكن موجوداً يتم إنشاؤه بصلاحيات آمنة
    $uploadDir = dirname(__DIR__) . '/uploads/products';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    // 7. توليد اسم فريد وآمن للملف لتجنب تكرار الأسماء أو اختراق النظام
    $extension = $allowedTypes[$mimeType];
    $fileName = 'product_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
    $destination = $uploadDir . '/' . $fileName;

    // 8. نقل الملف المؤقت إلى مجلد الحفظ النهائي
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $errors[] = 'تعذر حفظ الصورة داخل مجلد uploads/products.';
        return $oldImage;
    }

    // 9. إذا تم رفع صورة جديدة بنجاح أثناء عملية التعديل، نحذف الصورة القديمة لتجنب تراكم الملفات وتوفير مساحة التخزين
    if ($oldImage !== null && $oldImage !== '') {
        delete_product_image($oldImage);
    }

    // 10. إرجاع المسار النسبي للصورة ليتم حفظه في قاعدة البيانات
    return 'uploads/products/' . $fileName;
}

/**
 * دالة لحذف صورة المنتج من الخادم بشكل آمن.
 *
 * @param string|null $imagePath مسار الصورة المراد حذفها.
 * @return void
 */
function delete_product_image(?string $imagePath): void
{
    // إذا كان المسار فارغاً، لا يتم تنفيذ أي عملية
    if ($imagePath === null || $imagePath === '') {
        return;
    }
    // تحديد المسار الجذري لمجلد المنتجات والمسار الكامل للملف المراد حذفه بصيغة حقيقية (Absolute Real Path)
    $uploadRoot = realpath(dirname(__DIR__) . '/uploads/products');
    $fullPath = realpath(dirname(__DIR__) . '/' . $imagePath);
    // حماية أمنية (Path Traversal Protection): 
    // التأكد من أن المسار الحقيقي للملف يقع فعلياً داخل مجلد الصور المسموح به وأنه ملف صالح للحذف
    if ($uploadRoot && $fullPath && strpos($fullPath, $uploadRoot) === 0 && is_file($fullPath)) {
    //strpos تُستخدم للتحقق مما إذا كان مسار الملف ($fullPath) يبدأ فعلياً بمسار المجلد الأساسي المسموح به
        unlink($fullPath); // حذف الملف من الخادم
    }
}