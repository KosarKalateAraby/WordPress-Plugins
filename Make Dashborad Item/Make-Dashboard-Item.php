<?php

// Plugin Name: Make Dashboard Item
// Description: Add Item To Dashboard Menu And Complate Fields To Show Another Page Whith ShortCode
// Version: 1.0
// Author: Kosar


// میگه که ثابت ABSPATH تعریف شده یا نه
// به این منظور که دستورات داره از هسته وردپرس اجرا میشه یا از طریق مرورگر
// برای جلوگیری از هک و سواستفاده
if (!defined('ABSPATH')) {
    exit;
}



function Add_Styles(){
    wp_enqueue_style(
        'tailwind-local',
        plugin_dir_url(__FILE__) . './src/output.css', // plugin_dir_url برای فایلی که آدرس دادیم، یک url مرورگر میسازه
        [],
       
    );
    wp_enqueue_style(
        'style-css' ,
        plugin_dir_url(__FILE__). 'style.css', 
        [],
    );
    wp_enqueue_script(
        'javascript' ,
        plugin_dir_url(__FILE__). 'script.js', 
        [],
        false,
        true
    );

    // انتقال متغیر به جاوااسکریپت
    wp_localize_script('javascript', 'MyPluginData', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
}

add_action('admin_enqueue_scripts', 'Add_Styles');
add_action('wp_enqueue_scripts', 'Add_Styles');

// ساخت آیتم منو داشبورد به نام Save Data
function Add_Item_Menu(): void
{
    add_menu_page(
        'Save Data',           // عنوان صفحه
        'Save Data',           // نام آیتم در پنل ادمین
        'manage_options',      // سطح دسترسی که اینجا یعنی فقط مدیران بتونن این صفحه رو ببینن
        'save_data',           // اسلاگ مخصوص برای این صفحه برای ساختن URL: ?page=save_data
        'save_data_page',       // تابعی برای نمایش محتوای این صفحه
        'dashicons-edit',       // آیکون آیتم در منو داشبورد
        // Dashicons مجموعه‌ای از آیکون‌های SVG آماده هست که وردپرس داخل هسته خودش بارگزاری کرده.

        // 26                      // پوزیشن قرارگیری در صفحه
    );

}

add_action('admin_menu', 'Add_Item_Menu');


// محتوای صفحه در داشبورد
/**
 * Summary of save_data_page
 * @return void
 */

function save_data_page(){

    // برای ذخیره مقدار در input ها
    $name = get_option('DashBoard-Field-name', '');
    $age  = get_option('DashBoard-Field-age', '');


    ?>
    <div dir="rtl" class="font-[Shabnam] min-h-screen flex items-center justify-center bg-slate-100">
        <form id="customForm" method="post" action="<?php echo admin_url('admin-post.php'); ?>"
            class="bg-white p-6 rounded-xl shadow-md w-full max-w-md space-y-4">

            <input type="hidden" name="action" value="save_custom_data">

            <div class="flex items-center gap-2">
                <label class="w-24 text-right text-sm font-medium text-gray-700">نام:</label>
                <input type="text" name="name" value="<?php echo esc_attr($name); ?>" class="flex-1 border border-gray-300 rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <!-- esc_attr() مقدار رو به صورت امن برای قرار دادن داخل value پاکسازی می‌کنه برای جلوگیری از مشکل امنیتی -->
            </div>

            <div class="flex items-center gap-2">
                <label class="w-24 text-right text-sm font-medium text-gray-700">سن:</label>
                <input type="number" name="age" value="<?php echo esc_attr($age); ?>" class="flex-1 border border-gray-300 rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="text-center pt-2">
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                    ارسال
                </button>
                <p id="result" class="text-green-900 hidden">اطلاعات ذخیره شد!</p>
            </div>
        </form>
    </div>

    <?php

}

// add_action('admin_post_save_custom_data', 'handle_custom_form_submit');

// function handle_custom_form_submit() {
//     // بررسی سطح دسترسی
//     // اگر الان کاربر دسترسی مدیریت تنظیمات سایت رو نداره، اجرای کد متوقف بشه
//     // و پیغام خطای 'شما اجازه انجام این کار را ندارید'

//     if (!current_user_can('manage_options')) {
//         wp_die('شما اجازه انجام این کار را ندارید.');
//     }

//     // گرفتن و پاک‌سازی داده‌ها
//     // تابع sanitize_text_field برای پاکسازی رشته‌های متنی
//     $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';

//     // absint() مقدار را به عدد صحیح مثبت تبدیل می‌کند
//     $age  = isset($_POST['age']) ? absint($_POST['age']) : 0;

//     // ذخیره در پایگاه داده
//     update_option('DashBoard-Field-name', $name);
//     update_option('DashBoard-Field-age', $age);

//     // ریدایرکت به URL که مشخص کردیم
//     // صفحه admin-post.php فقط برای پردازش هستش و اصلا ظاهر گرافیکی نداره
//     // اما بعد پردازش باید بره به صفحه admin.php

//     wp_redirect(location: admin_url('admin.php?page=save_data&status=success'));
//     exit;
// }

add_action('wp_ajax_save_custom_data_ajax', 'handle_ajax_form_submit');

function handle_ajax_form_submit() {
    if (!current_user_can('manage_options')) {
        wp_die('شما اجازه انجام این کار را ندارید.');
    }

    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $age  = isset($_POST['age']) ? absint($_POST['age']) : 0;

    update_option('DashBoard-Field-name', $name);
    update_option('DashBoard-Field-age', $age);

    echo 'success';
    wp_die();
}


function show_custom_data_shortcode() {
    $name = get_option('DashBoard-Field-name', '');
    $age  = get_option('DashBoard-Field-age', '');
    return "
    <div class='bg-white p-6 rounded-xl shadow-md w-full max-w-md space-y-4 font-[Shabnam]'>
        <div class='flex items-center gap-2'>
            <label class='flex-1 w-24 text-right text-sm font-medium text-gray-700'>نام: $name</label>
        </div>

        <div class='flex items-center gap-2'>
            <label class='flex-1 w-24 text-right text-sm font-medium text-gray-700'>سن: $age</label>
        </div>
    </div>";
}

add_shortcode('my_custom_data', 'show_custom_data_shortcode');

?>