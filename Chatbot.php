function getBotReply(msg){
    msg = msg.toLowerCase();

    // 👋 GREETING
    if(msg.includes("hi") || msg.includes("hello") || msg.includes("hey")){
        return "Hi! 👋 / Kamusta! 🇵🇭\nWelcome to FarmToHome. How can I help you today? / Paano kita matutulungan?";
    }

    // 🔐 LOGIN
    if(msg.includes("login")){
        return "To log in: enter your email and password then click Login button.\n\nPara mag-login: ilagay ang email at password mo then pindutin ang Login.";
    }

    // 🔑 PASSWORD
    if(msg.includes("password") || msg.includes("forgot")){
        return "If you forgot your password, click 'Forgot Password' to reset it.\n\nKung nakalimutan mo password mo, i-click ang 'Forgot Password' para mag-reset.";
    }

    // 📝 REGISTER
    if(msg.includes("register") || msg.includes("signup") || msg.includes("sign up")){
        return "To register, fill up the sign-up form to create an account.\n\nPara mag-register, fill up mo lang ang form para makagawa ng account.";
    }

    // 🌱 PRODUCTS
    if(msg.includes("products") || msg.includes("available") || msg.includes("vegetables")){
        return "We have fresh vegetables like tomatoes, lettuce, okra, and eggplant.\n\nMay fresh vegetables kami tulad ng tomatoes, lettuce, okra at eggplant 🌱";
    }

    // 🛒 ORDER
    if(msg.includes("order") || msg.includes("buy")){
        return "To order, add products to your cart and proceed to checkout.\n\nPara mag-order, i-add sa cart ang products then checkout na.";
    }

    // 🚚 DELIVERY
    if(msg.includes("deliver") || msg.includes("delivery")){
        return "We deliver within Quezon City (1–2 days).\n\nNagdedeliver kami sa Quezon City 🇵🇭 (1–2 araw).";
    }

    // 💰 PAYMENT
    if(msg.includes("payment") || msg.includes("gcash") || msg.includes("cash")){
        return "We accept GCash, bank transfer, and Cash on Delivery.\n\nPwede magbayad via GCash, bank transfer, at Cash on Delivery.";
    }

    // 👨‍🌾 FARMER
    if(msg.includes("farmer") || msg.includes("sell")){
        return "Farmers can register to sell products directly.\n\nAng farmers ay puwedeng mag-register para magbenta ng products.";
    }

    // 📦 TRACK
    if(msg.includes("track") || msg.includes("status")){
        return "Please provide your order number to track your order.\n\nPakibigay ang order number para ma-check ang status.";
    }

    // ❌ CANCEL
    if(msg.includes("cancel")){
        return "You can cancel your order before it is shipped.\n\nPwede i-cancel ang order bago ma-ship.";
    }

    // 🆘 HELP
    if(msg.includes("help")){
        return "I can help with products, orders, delivery, payments, and login.\n\nPwede kitang tulungan sa products, orders, delivery, payments, at login.";
    }

    return "Sorry 😅 I didn’t understand.\nPasensya 😅 hindi ko naintindihan.\nTry asking about products, orders, delivery, or login.";
}