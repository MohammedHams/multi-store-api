@component('mail::message')
    # مرحباً {{ $user->name }}

    تم إنشاء حساب لك كموظف في متجر **{{ $store->name }}**

    **بيانات الدخول:**
    البريد الإلكتروني: {{ $user->email }}
    كلمة المرور: {{ $password }}

    @component('mail::button', ['url' => $verificationUrl])
        تفعيل الحساب
    @endcomponent

    **الصلاحيات الممنوحة لك:**
    - إدارة الطلبات: {{ $user->staffPermissions->manage_orders ? 'نعم' : 'لا' }}
    - إدارة المنتجات: {{ $user->staffPermissions->manage_products ? 'نعم' : 'لا' }}
    - إدارة الإعدادات: {{ $user->staffPermissions->manage_settings ? 'نعم' : 'لا' }}

    شكراً لك،
    {{ config('app.name') }}
@endcomponent
