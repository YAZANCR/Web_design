<?php

function clean_text(string $value): string
{
    return trim($value);
}
// دالة لتنظيف المخرجات لمنع ثغرات XSS
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function validate_product_input(array $source): array
{
    $data = [
        'category_id' => (int)($source['category_id'] ?? 0),
        'name' => clean_text($source['name'] ?? ''),
        'description' => clean_text($source['description'] ?? ''),
        'price' => clean_text($source['price'] ?? ''),
        'quantity' => clean_text($source['quantity'] ?? ''),
    ];

    $errors = [];

    if ($data['category_id'] <= 0) {
        $errors[] = 'يجب اختيار الصنف.';
    }

    if ($data['name'] === '') {
        $errors[] = 'اسم المنتج مطلوب.';
    } elseif (mb_strlen($data['name']) < 3) {
        $errors[] = 'اسم المنتج يجب ألا يقل عن 3 أحرف.';
    }

    if ($data['price'] === '' || !is_numeric($data['price']) || (float)$data['price'] <= 0) {
        $errors[] = 'السعر يجب أن يكون رقمًا أكبر من صفر.';
    }

    if ($data['quantity'] === '' || !ctype_digit((string)$data['quantity'])) {
        $errors[] = 'الكمية يجب أن تكون رقمًا صحيحًا لا يحتوي على كسور.';
    }

    return [$data, $errors];
}
