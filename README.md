كيفية تشغيل المشروع
1- يتم سحب المشروع من github


2-تنفيذ الامر composer install



2-تنفيذ أمر php artisan migrate:fresh --seed



اليوزرات 

admin - super@admin.com , secret
store owner - storeowner@example.com , password
staff - staff@example.com , password
لتسجيل الدخول 
api 
http://127.0.0.1:8000/api/auth/login
لتشغيل خدمة ارسال الايميل عبر env

للتحقق من الotp verfiy
http://127.0.0.1:8000/api/auth/verify-otp

http://127.0.0.1:8000/api/store/
للوصول لكافة المنتجات عبر الadmin
http://127.0.0.1:8000/api/store/1/products
للوصول للمنتجات حسب الصلاحية
