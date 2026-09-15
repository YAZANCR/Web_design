<?php
// ملف الاتصال بقاعدة البيانات باستخدام PDO
// ======================================================
// معلومات الاتصال بقاعدة البيانات
$host = 'localhost';//  خادم قاعدة البيانات الموجود على نفس الجهاز
$dbname = 'mini_store';// اسم قاعدة البيانات التي أنشأناها في MySQL
$username = 'root';// اسم مستخدم MySQL
$password = '';// كلمة مرور MySQL
// في XAMPP غالبًا تكون فارغة بشكل افتراضي
// DSN = Data Source Name
// يحدد نوع قاعدة البيانات، والخادم، واسم قاعدة البيانات
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
// إعدادات PDO
$options = [
    // جعل PDO يظهر Exception عند حدوث خطأ
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    // إرجاع نتائج SELECT على شكل Associative Array
    // مثال: $row['name'] بدل $row[0]
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // استخدام Prepared Statements الحقيقية في MySQL
    // وهي مهمة للحماية من SQL Injection
    PDO::ATTR_EMULATE_PREPARES => false,
];
// إنشاء الاتصال
try {
    // إنشاء كائن PDO والاتصال بقاعدة البيانات
    $pdo = new PDO($dsn,$username,$password,$options );
    //$con =new mysqli($host,$username,$password,$dbname);
    // إذا وصل التنفيذ إلى هنا فهذا يعني أن الاتصال نجح
} catch (PDOException $e) {
    // PDOException هو نوع الخطأ الذي قد يحدث أثناء الاتصال
    // $e يحتوي على معلومات الخطأ
    // die() توقف تنفيذ البرنامج وتعرض الرسالة
       die(
        'فشل الاتصال بقاعدة البيانات: '
        . $e->getMessage()
    );
}
   session_start();

