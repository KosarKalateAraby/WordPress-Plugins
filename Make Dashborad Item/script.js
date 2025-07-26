document
  .getElementById("customForm")
  .addEventListener("submit", async function (e) {
    // async رو قبل تابع مینویسیم چون قراره داخل تابع از await استفاده کنیم
    // await باعث میشه اون خط کد صبر کنه تا از سرور پاسخ بگیره بعدش اجرا بشه
    // و در همین حین، کدهای بعدی اجرا میشن و صبر نمیکنن

    // جلوگیری می‌کنه از اتفاق پیش‌فرض فرم، که همون ارسال فرم و رفرش صفحه هست.
    e.preventDefault();

    // یک آبجکت جدید از کلاس FormData بساز که همه فیلدهای فرم رو به همراه مقادیرش در خودش داشته باشه
    const formData = new FormData(this);

    //  یک فیلد جدید به فرم اضافه می‌کنیم به اسم action.
    // وردپرس حتماً باید این فیلد رو داشته باشه تا بفهمه کدوم هوک PHP رو اجرا کنه.
    formData.append("action", "save_custom_data_ajax");

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

// <?php echo admin_url('admin-ajax.php'); ?>
