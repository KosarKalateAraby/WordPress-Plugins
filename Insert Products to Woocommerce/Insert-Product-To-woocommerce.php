<?php
/**
 * Plugin Name: Insert Product To woocommerce
 *
 * Description: Made a item dashboard to Add Products and show products in another pages with woocommerce
 *
 * Version: 1.0
 *
 * Author: Kosar
 */

if (!defined('ABSPATH')){
    exit;
}

function Add_Styles(){
    wp_enqueue_style(
        'tailwind',
        plugin_dir_url(__FILE__) . './src/output.css', 
        [],
       
    );
    wp_enqueue_style(
        'style-css' ,
        plugin_dir_url(__FILE__). 'style.css', 
        [],
    );

}

function Add_script(){
    wp_enqueue_media();

    wp_enqueue_script('jquery');

    wp_enqueue_script(
        'javascript' ,
        plugin_dir_url(__FILE__). 'script.js', 
        ['jquery'],
        false,
        true
    );

    wp_localize_script('javascript', 'MyPluginData', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
}

add_action('admin_enqueue_scripts','Add_Styles');
add_action('wp_enqueue_scripts','Add_Styles');

add_action('admin_enqueue_scripts','Add_script');
add_action('wp_enqueue_scripts','Add_script');

function Add_Products_Menu(): void
{
    add_menu_page(
        'Add Products',           
        'Add Products',          
        'manage_options',      
        'Add_Products',           
        'Add_Products_page',       
        'dashicons-products',   
    );

}

add_action('admin_menu', 'Add_Products_Menu');


function Add_Products_page(){
 ?>
    <div dir="rtl" class="font-[Shabnam] min-h-screen flex items-center bg-slate-100 justify-center">
        <form id="ProductForm" method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>" class="bg-white p-6 rounded-xl shadow-md w-full max-w-md space-y-4">
            <input type="hidden" name="action" value="handle_add_custom_product">

            <div class="flex items-center gap-2">
                <label for="Name" class="w-24 text-right text-sm font-medium text-gray-700">نام محصول:</label>
                <input type="text" name="name" id="Name" class="flex-1 border border-gray-300 rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-center gap-2">
                <label for="price" class="w-24 text-right text-sm font-medium text-gray-700">قیمت محصول:</label>
                <input type="number" name="price" id="price" class="flex-1 border border-gray-300 rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-center gap-2">
                <label for="stock" class="w-24 text-right text-sm font-medium text-gray-700">تعداد موجودی:</label>
                <input type="number" name="stock" id="stock" class="flex-1 border border-gray-300 rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-center gap-2">
                <label for="categorie" class="w-24 text-right text-sm font-medium text-gray-700">دسته‌بندی:</label>
                <?php 
                    $categories = get_terms([
                        'taxonomy' => 'product_cat',
                        'hide_empty' => false
                    ])
                
                ?>
                <select type="text" name="categorie" id="categorie" class="flex-1 border border-gray-300 rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">دسته بندی را انتخاب کنید</option>
                    <?php foreach ($categories as $category): ?>
                        <option value=" <?php echo esc_attr($category ->term_id); ?>">
                            <?php echo esc_html($category ->name); ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="new">دسته بندی جدید+</option>
                </select>
            </div>

            <div id="new_category_wrraper" class="hidden flex items-center gap-2">
                <label class="w-24 text-right text-sm font-medium text-gray-700">نام دسته بندی جدید:</label>
                <input type="text" name="new_category" id="new_category" class="flex-1 border border-gray-300 rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-center gap-2">
                <label class="w-24 text-right text-sm font-medium text-gray-700">تصویر محصول:</label>
                <div class="flex-1">
                    <img id="preview-image" src="" class="w-32 h-32 object-cover mb-2 hidden rounded border">
                    <button type="button" id="upload_image_button" class="font-medium bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">انتخاب از کتاب‌خانه</button>
                    <input type="hidden" name="image_url" id="image_url">

                </div>            
            </div>

            <div class="text-center mt-3">
                <button type="submit" class=" bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-700 p-2">ثبت محصول</button>
            </div>

            <p id="result" class="text-green-600 text-center mt-4 hidden">محصول با موفقیت اضافه شد!</p>
        </form>
    </div>
<?php
}

add_action('wp_ajax_handle_add_custom_product', 'handle_add_custom_product');
// add_action('wp_ajax_nopriv_handle_add_custom_product', 'handle_add_custom_product');


function handle_add_custom_product(){

    if(!current_user_can('manage_options')){
        wp_die('شما دسترسی لازم رو ندارید!');
    }

    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $price = isset($_POST['price']) ? absint($_POST['price']) : 0;

    $categorie = '';
    if (isset($_POST['categorie']) && $_POST['categorie'] === 'new' && !empty($_POST['new_category'])) {
        $categorie = sanitize_text_field($_POST['new_category']);
    } elseif (isset($_POST['categorie'])) {
        $categorie = sanitize_text_field($_POST['categorie']);
    }
    
    $stock = isset($_POST['stock']) ? absint($_POST['stock']) : 0;


    // اضافه کردن محصول به ووکامرس
    $product = new WC_Product_Simple();
    $product->set_name($name);
    $product->set_regular_price($price);

    $product -> set_manage_stock(true);
    $product -> set_stock_quantity($stock);
    $product -> set_stock_status('instock');

    //سیو نهایی محصول
    $product_id = $product-> save();

    $term = term_exists($categorie, 'product_cat');
    if (!$term) {
        $term = wp_insert_term($categorie, 'product_cat');
    }

    // اگر مقدار ورودی ارایه دسته بندی بود، آیدی همون ارایه رو بگیر وگرنه عدد وارد شده را مستقیم وارد کن به عنوان id
    $category_id = is_array($term) ? $term['term_id'] : $term;
    wp_set_object_terms($product_id, [$category_id], 'product_cat');




    // بارگذاری تصویر
    if (isset($_POST['image_url']) && !empty($_POST['image_url'])){

        // یک فایل PHP خارجی رو به این فایل فعلی اضافه می‌کنه (load کردن)

        // require_once(ABSPATH . 'wp-admin/includes/file.php');

        // require_once(ABSPATH . 'wp-admin/includes/media.php'); 
        // این فایل media.php رو (که بیرون از فایل پلاگینه) فقط یک بار لود کن، تا بتونم از توابع داخلش استفاده کنم

        // require_once(ABSPATH . 'wp-admin/includes/image.php');

        // media_handle_upload() فایل آپلودی (مثلاً عکس) رو بگیره، به کتابخانه رسانه وردپرس (Media Library) اضافه کنه، و در نهایت ID اون فایل رو برگردونه.
        // media_handle_upload($field_name, $post_id);
        // $post_id این مشخص می‌کنه که فایل آپلودی مربوط به چه پست یا محصولی باشه.
        // اگه عدد 0 بدی، وردپرس فایل رو آپلود می‌کنه ولی به هیچ پستی attach نمی‌کنه.
        // ولی اگه بخوای به یک محصول خاص وصلش کنی، باید id اون محصول رو بدی
        // $attachment_id = media_handle_upload('pic', 0);

        // $attachment_id = isset($_POST['pic_id']) ? absint($_POST['pic_id']) : 0;


        // اگر برگردوندن آیدی محصول، خطا نداشت، تصویر رو به اون بچسبون
        // WP_Error یه کلاس داخلی وردپرسه که برای مدیریت خطاها

        // if (!is_wp_error($attachment_id)) {
        //     set_post_thumbnail($product_id, $attachment_id);
        // }

        // if ($attachment_id) {
        //     set_post_thumbnail($product_id, $attachment_id);
        // }

        // اگر از انتخاب از کتابخانه استفاده شده بود و فیلد فایل خالیه

            // esc_url_raw اطمینان حاصل می‌کنه که URL امن و بدون آسیب باشه.
            $image_url = esc_url_raw($_POST['image_url']);

            // attachment_url_to_postid این URL تبدیل به آیدی رسانه (عکس) می‌شه
            $attachment_id = attachment_url_to_postid($image_url);

            if ($attachment_id) {
                set_post_thumbnail($product_id, $attachment_id);
            }


    }

    // چون از AJAX استفاده کردیم، این چاپ توی صفحه اصلی نمایش داده نمیشه و توی js نمایش داده میشه 
    echo 'success';
    wp_die();
}




function show_custom_products_shortcode() {
    $args = array(
        'post_type' => 'product', //محصولات ووکامرس رو نمایش میده
        'posts_per_page' => 10, //حداقل تعداد نمایش محصول
    );

    $products = new WP_Query($args); //WP_Query برای نمایش لیستی از محصولات و سپس با حلقه یکی یکی نمایش داده میشه
    ob_start();
    // هر چیزی که از این به بعد echo بشه، به‌جای اینکه مستقیم روی صفحه چاپ بشه، میره توی یه حافظه موقتی (بافر).
    // چون توی شورت‌کدها نمی‌خوایم مستقیماً چیزی چاپ بشه، بلکه می‌خوایم خروجی رو جمع کنیم و در نهایت به وردپرس برگردونیم. پس خروجی‌ها رو با ob_start() نگه می‌داریم.

    ?>
    <div dir='rtl' class='font-[Shabnam] grid grid-cols-1 md:grid-cols-2 gap-6'>
    
    <?php 

    while ($products->have_posts()) {
        $products->the_post(); //هر بار یکی از محصولات رو به‌عنوان پست فعال (current post) تنظیم می‌کنه.
        global $product; //سراسری کردن متغیر محصولات ووکامرس
    
    ?>
        <div class='border p-4 rounded-xl text-center bg-white'>
        <?php echo get_the_post_thumbnail(get_the_ID(), 'medium', ['class' => 'w-full h-auto']); ?>
        <h2 class='font-[Shabnam] font-bold mt-4'> <?php echo get_the_title() ?> </h2>
        <p class='text-blue-600 mt-2 font-medium text-sm'> <?php echo wc_price($product->get_price()) ?></p>
        <p class='text-blue-600 mt-2 font-medium text-sm'> <?php echo wc_price($product->get_categorie()) ?></p>
        <p class='text-blue-600 mt-2 font-medium text-sm'> <?php echo wc_price($product->get_()) ?></p>
        </div>


     <?php 

    }
    echo '</div>';
    wp_reset_postdata(); // بازگرداندن وضعیت حلقه وردپرس (Loop) به حالت قبل
    return ob_get_clean(); //دریافت تمام خروجی‌های بافر شده + پاک کردن بافر
}
add_shortcode('show_custom_products', 'show_custom_products_shortcode');

?>