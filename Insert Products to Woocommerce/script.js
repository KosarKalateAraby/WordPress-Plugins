document.getElementById("ProductForm").addEventListener("submit", async function(e){

    // e مخفف Event هست.
    // وقتی کاربر روی دکمه "ارسال فرم" کلیک می‌کنه، یک رویداد (event) از نوع submit ایجاد می‌شه.
    e.preventDefault();

    const formData = new FormData(this);

    // یک فیلد جدید به نام action با مقدار handle_add_custom_product به دیتای فرم اضافه کن.
    formData.append("action", "handle_add_custom_product");


    const res = await fetch(MyPluginData.ajax_url, {
      method: "POST",
      body: formData,
    });

    const result = await res.text();
    if (result === "success") {
      document.getElementById("result").classList.remove("hidden");
    }

    setTimeout(() => {
      document.getElementById("result").classList.add("hidden");
    }, 3000);
});

document.addEventListener('DOMContentLoaded' , function(){
  const select = document.getElementById('categorie');
  const wrraper = document.getElementById('new_category_wrraper');


  select.addEventListener('change',function(){
    if (this.value === 'new'){
      wrraper.classList.remove('hidden');
    } else{
      wrraper.classList.add('hidden');
    }

  })
})



jQuery(document).ready(function($) {
    let mediaUploader;

    $('#upload_image_button').on('click', function(e) {
        e.preventDefault();

       
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

     
        mediaUploader = wp.media({
            title: 'انتخاب تصویر',
            button: {
                text: 'استفاده از این تصویر'
            },
            multiple: false
        });

   
        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#image_url').val(attachment.url);
        });
        mediaUploader.open();
    });
});
