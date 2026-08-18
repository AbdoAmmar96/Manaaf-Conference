<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'email' => 'يجب إدخال بريد إلكتروني صحيح.',
    'string' => 'قيمة :attribute غير صالحة.',
    'array' => 'يجب اختيار :attribute.',
    'min' => ['string' => 'حقل :attribute قصير جدًا.', 'array' => 'اختر عنصرًا واحدًا على الأقل من :attribute.'],
    'max' => ['string' => 'حقل :attribute طويل جدًا.'],
    'in' => 'القيمة المختارة في :attribute غير صالحة.',
    'unique' => ':attribute مستخدم من قبل.',
    'attributes' => [
        'name' => 'الاسم', 'organization' => 'الجهة', 'position' => 'المنصب',
        'mobile' => 'رقم الجوال', 'email' => 'البريد الإلكتروني', 'guest_type' => 'نوع الضيف',
        'subject' => 'الموضوع', 'body' => 'الرسالة', 'interests' => 'الاهتمامات',
        'company' => 'الشركة', 'notes' => 'الملاحظات', 'action' => 'الاختيار',
    ],
];
