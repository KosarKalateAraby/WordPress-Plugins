<?php
/*
* Plugin name: Chat Online
* Description: Open the Box of Chat Online
* Version: 1.0
* Author: Kosar
*/


add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'tailwind-local',
        plugin_dir_url(__FILE__) . './src/output.css',
        [],
       
    );
    wp_enqueue_style(
        'style-css' ,
        plugin_dir_url(__FILE__). 'style.css', 
        [],
    );
});

add_action('wp_footer', function() {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const chatIcon = document.getElementById("chat-icon");
            const chatBox = document.getElementById("chat-box");
            const sendBtn = document.getElementById("chat-send");
            const input = document.getElementById("chat-input");
            const messages = document.getElementById("chat-messages");

            // باز و بسته شدن باکس چت
            chatIcon.addEventListener("click", function () {
                chatBox.classList.toggle("hidden");
            });

            sendBtn.addEventListener("click", function () {
                const text = input.value.trim();
                if (text === "") return;


                const p = document.createElement("p");
                p.className = "mb-2 text-right bg-blue-100 px-3 py-2 rounded-xl self-end max-w-[80%] break-words whitespace-pre-wrap";
                p.innerText = text;
                messages.appendChild(p);

                messages.scrollTop = messages.scrollHeight;

                input.value = "";

                // پاسخ با switch
                switch (text) {
                    case "سلام":
                        botReply("هوی کوثر!");
                        break;
                    case "چطوری":
                        botReply("خوبم برار");
                        break;
                    case "اسمت چیه؟":
                        botReply("من چت‌بات پلاگین کوثرم 🤖");
                        break;
                    case "خداحافظ":
                        botReply("خداحافظ! هر زمان خواستی من اینجام 👋");
                        break;
                    default:
                        botReply("متوجه نشدم. لطفاً سوال رو واضح‌تر بپرس.");
                        break;
                }

                function botReply(message) {
                    const reply = document.createElement("p");
                    reply.className = "mb-2 text-left bg-gray-200 px-3 py-2 rounded-xl self-start max-w-[75%]";
                    reply.innerText = message;
                    messages.appendChild(reply);

                    messages.scrollTop = messages.scrollHeight;
                }

            });


            // ارسال پیام با Enter
            input.addEventListener("keydown", function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    sendBtn.click();
                }
            });

            
        });
    </script>
    <?php
});



function chat_online_ui(){

    $icon_url = plugin_dir_url(__FILE__) . 'assets/images/icon2.png';
    $icon_sent = plugin_dir_url(__FILE__) . 'assets/images/SentIcon.png';

    echo '<div dir="rtl" id="chat-widget-root" class="fixed bottom-6 right-6 z-[9999]" style="font-family: Shabnam;">

        <!-- آیکون چت آنلاین -->
        <button id="chat-icon" class="w-14 h-14 p-3 lg:w-20 lg:h-20 lg:p-4 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-gray-200 transition border-[2px] border-[#093FB4]">
            <img src="' . esc_url($icon_url) . '" alt="Chat" class="w-14">
        </button>

        <!-- باکس چت -->
        <div id="chat-box" class="hidden mt-4 lg:w-80 lg:h-96 w-72 h-72 bg-white border border-gray-300 rounded-xl shadow-2xl p-4 flex flex-col justify-between">
            <div id="chat-messages" class="flex-1 overflow-y-auto custom-scrollbar lg:text-sm text-xs text-gray-800 flex flex-col gap-2">
                <p class="bg-gray-200 text-gray-800 px-4 py-2 rounded-xl max-w-[75%] self-start text-sm leading-relaxed shadow-sm ">سلام! چطور می‌تونم کمکتون کنم؟</p>
            </div>
            <div class="mt-4">
                <div class="lg:h-[50px] flex justify-between items-center p-2 border border-gray-300 rounded-2xl lg:text-sm text-xs break-words whitespace-pre-wrap">
                    <input id="chat-input" type="text" placeholder="پیام را بنویسید ..." class="lg:p-3 p-2 focus:outline-none ">
                    <img src="' . esc_url($icon_sent) . '" alt="chat-send" id="chat-send" class="lg:w-[20px] w-[18px] max-w-none object-contain cursor-pointer">
                </div>
                
            </div>
        </div>

    </div>';
}

add_action('wp_footer' , 'chat_online_ui');

?>

