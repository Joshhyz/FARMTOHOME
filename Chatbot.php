<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FarmToHome</title>

    <style>
        :root{
            --bg1:#0f766e;
            --bg2:#22c55e;
            --card:#ffffff;
            --text:#0f172a;
            --muted:#64748b;
            --primary:#16a34a;
            --primary2:#22c55e;
            --ring: rgba(34,197,94,.35);
            --shadow: 0 18px 50px rgba(2,6,23,.18);
            --shadow2: 0 10px 24px rgba(2,6,23,.12);
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
            color:var(--text);
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:28px;
            background:
                radial-gradient(1100px 500px at 10% 10%, rgba(34,197,94,.22), transparent 55%),
                radial-gradient(900px 450px at 90% 20%, rgba(15,118,110,.22), transparent 60%),
                linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 45%, #ecfeff 100%);
        }

        .shell{
            width: 100%;
            max-width: 980px;
            display:grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 22px;
            align-items: center;
        }

        @media (max-width: 820px){
            .shell{ grid-template-columns: 1fr; }
        }

        .brand{
            padding: 16px 10px;
        }

        .brand-badge{
            display:inline-flex;
            gap:10px;
            align-items:center;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.6);
            border: 1px solid rgba(15,118,110,.12);
            box-shadow: 0 10px 30px rgba(2,6,23,.06);
            backdrop-filter: blur(6px);
        }

        .leaf{
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display:flex;
            align-items:center;
            justify-content:center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary2) 100%);
            color: #fff;
            font-size: 18px;
            box-shadow: 0 10px 20px rgba(34,197,94,.35);
        }

        .brand h1{
            margin: 14px 0 8px;
            font-size: 42px;
            line-height: 1.05;
            letter-spacing: -0.03em;
        }

        .brand p{
            margin: 0;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.6;
        }

        .login-card{
            background: rgba(255,255,255,.88);
            border: 1px solid rgba(15,118,110,.12);
            border-radius: 20px;
            box-shadow: var(--shadow2);
            backdrop-filter: blur(10px);
            padding: 22px;
        }

        .login-card h2{
            margin: 0 0 6px;
            font-size: 22px;
        }

        .login-card .sub{
            margin: 0 0 16px;
            color: var(--muted);
            font-size: 14px;
        }

        .field{
            margin: 12px 0;
        }

        label{
            display:block;
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .input-wrap{
            position:relative;
        }

        .input-icon{
            position:absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(15,118,110,.9);
            pointer-events:none;
            font-size: 16px;
        }

        input[type="email"],
        input[type="password"]{
            width:100%;
            padding: 12px 12px 12px 40px;
            border-radius: 14px;
            border: 1px solid rgba(15,118,110,.18);
            background: rgba(255,255,255,.95);
            outline:none;
            font-size: 14px;
            transition: box-shadow .2s ease, border-color .2s ease, transform .05s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus{
            border-color: rgba(34,197,94,.6);
            box-shadow: 0 0 0 6px var(--ring);
        }

        .row{
            display:flex;
            justify-content: space-between;
            align-items:center;
            gap: 10px;
            margin-top: 10px;
        }

        .help-link{
            font-size: 13px;
            color: rgba(15,118,110,.95);
            text-decoration:none;
        }

        .help-link:hover{ text-decoration: underline; }

        .btn{
            width:100%;
            margin-top: 14px;
            padding: 12px;
            border: 0;
            border-radius: 14px;
            font-weight: 700;
            cursor:pointer;
            color:#fff;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary2) 100%);
            box-shadow: 0 14px 30px rgba(34,197,94,.35);
            transition: transform .08s ease, filter .2s ease, box-shadow .2s ease;
        }

        .btn:hover{ filter: brightness(1.03); box-shadow: 0 16px 34px rgba(34,197,94,.42); }
        .btn:active{ transform: translateY(1px); }

        /* CHATBOT */
        #chatbot-btn{
            position: fixed;
            right: 18px;
            bottom: 18px;
            z-index: 999;
            width: 56px;
            height: 56px;
            border-radius: 18px;
            display:flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
            border: 0;
            color:#fff;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary2) 100%);
            box-shadow: 0 18px 40px rgba(34,197,94,.35);
            transition: transform .15s ease;
            user-select:none;
            font-size: 20px;
        }

        #chatbot-btn:hover{ transform: translateY(-2px); }

        #chatbot-box{
            position: fixed;
            right: 18px;
            bottom: 86px;
            z-index: 998;
            width: min(360px, calc(100vw - 36px));
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(15,118,110,.14);
            border-radius: 18px;
            box-shadow: var(--shadow);
            display:none;
            flex-direction: column;
            overflow:hidden;
            backdrop-filter: blur(10px);
            transform-origin: 100% 100%;
        }

        #chatbot-box.open{ display:flex; animation: pop .18s ease-out; }

        @keyframes pop{
            from{ transform: scale(.98); opacity:.6; }
            to{ transform: scale(1); opacity:1; }
        }

        .chat-header{
            background: linear-gradient(135deg, #0f766e 0%, #22c55e 100%);
            color:#fff;
            padding: 12px 14px;
            display:flex;
            align-items:center;
            justify-content: space-between;
            gap: 12px;
        }

        .chat-title{
            display:flex;
            align-items:center;
            gap: 10px;
            font-weight: 800;
        }

        .chat-close{
            cursor:pointer;
            opacity:.95;
            padding: 6px 10px;
            border-radius: 12px;
            background: rgba(255,255,255,.15);
        }
        .chat-close:hover{ background: rgba(255,255,255,.22); }

        #chat-messages{
            height: 240px;
            overflow-y:auto;
            padding: 14px;
            font-size: 14px;
            display:flex;
            flex-direction: column;
            gap: 10px;
        }

        .msg{
            max-width: 85%;
            padding: 10px 12px;
            border-radius: 16px;
            line-height: 1.3;
            word-break: break-word;
        }

        .msg.bot{
            background: rgba(15,118,110,.08);
            border: 1px solid rgba(15,118,110,.14);
            color:#0f3d3a;
            align-self:flex-start;
        }

        .msg.user{
            background: linear-gradient(135deg, rgba(34,197,94,.20) 0%, rgba(15,118,110,.12) 100%);
            border: 1px solid rgba(34,197,94,.22);
            color:#053b2c;
            align-self:flex-end;
        }

        .msg .who{
            display:block;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 4px;
            opacity:.9;
        }

        .typing{
            display:flex;
            align-items:center;
            gap: 6px;
            color: var(--muted);
            font-size: 13px;
            margin-top: 2px;
        }

        .dot{
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(15,118,110,.6);
            animation: bounce 1s infinite;
        }
        .dot:nth-child(2){ animation-delay: .15s; }
        .dot:nth-child(3){ animation-delay: .3s; }
        @keyframes bounce{
            0%, 80%, 100% { transform: translateY(0); opacity:.55; }
            40% { transform: translateY(-4px); opacity:1; }
        }

        .chat-input{
            display:flex;
            gap: 10px;
            padding: 12px;
            border-top: 1px solid rgba(15,118,110,.12);
            background: rgba(255,255,255,.7);
        }

        #user-input{
            flex: 1;
            border-radius: 14px;
            border: 1px solid rgba(15,118,110,.18);
            padding: 10px 12px;
            font-size: 14px;
            outline:none;
            background:#fff;
        }
        #user-input:focus{ box-shadow: 0 0 0 6px var(--ring); }

        .send-btn{
            border: 0;
            cursor:pointer;
            color:#fff;
            font-weight: 800;
            padding: 0 14px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary2) 100%);
        }
        .send-btn:hover{ filter: brightness(1.04); }
    </style>
</head>

<body>
    <div class="shell">
        <div class="brand">
            <div class="brand-badge">
                <div class="leaf">🌱</div>
                <div>
                    <div style="font-weight:900; letter-spacing:-0.02em;">FarmToHome</div>
                    <div style="color:var(--muted); font-size:13px;">Fresh connections for farmers & families</div>
                </div>
            </div>

            <h1>Welcome back.</h1>
            <p>Log in to continue. The bot can help you with login, password, and registration questions.</p>
        </div>

        <div class="login-card">
            <h2>Login</h2>
            <p class="sub">Enter your details below.</p>

            <form method="POST" action="login_process.php" autocomplete="on">
                <div class="field">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <span class="input-icon">✉️</span>
                        <input id="email" type="email" name="email" placeholder="Enter Email" required />
                    </div>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒</span>
                        <input id="password" type="password" name="password" placeholder="Enter Password" required />
                    </div>
                </div>

                <div class="row">
                    <a class="help-link" href="#" onclick="return false;">Forgot password?</a>
                </div>

                <button class="btn" type="submit">Login</button>
            </form>
        </div>
    </div>

    <!-- CHATBOT BUTTON -->
    <div id="chatbot-btn" onclick="toggleChat()" role="button" aria-label="Open chatbot">💬</div>

    <!-- CHATBOT BOX -->
    <div id="chatbot-box" aria-live="polite">
        <div class="chat-header">
            <div class="chat-title">
                <img
                    src="logo.jpg"
                    alt="FarmToHome"
                    style="width:34px;height:34px;border-radius:12px;object-fit:contain;display:block;background:rgba(255,255,255,.22);padding:4px;"
                />

                <span>FarmToHome Bot</span>
            </div>
            <div class="chat-close" onclick="toggleChat()" aria-label="Close chatbot">✖</div>
        </div>

        <div id="chat-messages"></div>

        <div class="chat-input">
            <input type="text" id="user-input" placeholder="Type a message..." />
            <button class="send-btn" onclick="sendMessage()" type="button">Send</button>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        function escapeHtml(str){
            return String(str)
                .replaceAll('&','&amp;')
                .replaceAll('<','<')
                .replaceAll('>','>')
                .replaceAll('"','"')
                .replaceAll("'",'&#039;');
        }

        function toggleChat() {
            const box = document.getElementById("chatbot-box");
            const isOpen = box.classList.contains('open');
            if (isOpen) {
                box.classList.remove('open');
                box.style.display = 'none';
            } else {
                box.style.display = 'flex';
                box.classList.add('open');
                setTimeout(() => {
                    const inp = document.getElementById('user-input');
                    if (inp) inp.focus();
                }, 0);
            }
        }

        function addMessage(who, text, kind){
            const chat = document.getElementById("chat-messages");
            const div = document.createElement('div');
            div.className = 'msg ' + kind;

            div.innerHTML = '<span class="who">' + escapeHtml(who) + ':</span>' + escapeHtml(text);
            chat.appendChild(div);
            chat.scrollTop = chat.scrollHeight;
        }

        function showTyping(){
            const chat = document.getElementById("chat-messages");
            const t = document.createElement('div');
            t.className = 'typing';
            t.id = 'typing-indicator';
            t.innerHTML = '<span class="dot"></span><span class="dot"></span><span class="dot"></span> <span>Bot is typing...</span>';
            chat.appendChild(t);
            chat.scrollTop = chat.scrollHeight;
        }

        function hideTyping(){
            const el = document.getElementById('typing-indicator');
            if (el) el.remove();
        }

        window.onload = function() {
            // Avoid double welcome message
            const chat = document.getElementById("chat-messages");
            if (!chat.dataset.welcomeShown){
                addMessage('Bot', 'Hi! 👋 Need help logging in or signing up?', 'bot');
                chat.dataset.welcomeShown = 'true';
            }
        };

        function sendMessage() {
            const input = document.getElementById("user-input");
            const message = input.value;
            if (message.trim() === "") return;

            addMessage('You', message, 'user');
            input.value = "";

            // Bot reply with small delay for UX
            showTyping();
            const reply = getBotReply(message);
            setTimeout(() => {
                hideTyping();
                addMessage('Bot', reply, 'bot');
            }, 450);
        }

        function getBotReply(msg) {
            msg = String(msg).toLowerCase();

            // 👋 GREETING
            if (msg.includes("hi") || msg.includes("hello") || msg.includes("hey")) {
                return "Hi! 👋 I’m FarmToHome Bot. Ask me about login, password reset, signup/register, or farmers.";
            }

            // 🔐 LOGIN
            if (msg.includes("login") || msg.includes("sign in") || msg.includes("log in")) {
                return "To log in: enter your email + password, then click the Login button.";
            }

            // 🔑 PASSWORD
            if (msg.includes("password") || msg.includes("forgot") || msg.includes("reset")) {
                return "If you forgot your password, use “Forgot password?” and follow the reset steps.";
            }

            // 📝 REGISTER
            if (msg.includes("register") || msg.includes("signup") || msg.includes("sign up") || msg.includes("create account")) {
                return "To register: click Sign Up / Get Started, then fill in your details.";
            }

            // 🌱 PRODUCTS / AVAILABLE
            if (msg.includes("products") || msg.includes("available") || msg.includes("vegetables") || msg.includes("what")) {
                return "We offer fresh produce. Tell me what you’re looking for (e.g., tomatoes, lettuce, okra).";
            }

            // 🛒 ORDER
            if (msg.includes("order") || msg.includes("buy") || msg.includes("cart")) {
                return "You can order by selecting products and adding them to your cart.";
            }

            // 🚚 DELIVERY
            if (msg.includes("deliver") || msg.includes("delivery")) {
                return "We deliver depending on your location. Delivery timing is usually 1–2 days.";
            }

            // 💰 PAYMENT
            if (msg.includes("payment") || msg.includes("gcash") || msg.includes("cash") || msg.includes("bank")) {
                return "We accept GCash, bank transfer, and cash on delivery (if available).";
            }

            // 👨‍🌾 FARMERS / SELL
            if (msg.includes("farmer") || msg.includes("sell") || msg.includes("selling")) {
                return "Farmers can register to sell products. Choose the Farmer option during sign up.";
            }

            // 📦 TRACKING / STATUS
            if (msg.includes("track") || msg.includes("status") || msg.includes("order status")) {
                return "To track your order, please provide your order number.";
            }

            // ❌ CANCEL
            if (msg.includes("cancel")) {
                return "You can cancel your order before it is shipped.";
            }

            // 🆘 HELP
            if (msg.includes("help") || msg.includes("support") || msg.includes("what can you do")) {
                return "I can help with: login, password reset, signup/register, products & ordering, delivery, payments, and farmer selling info.";
            }

            return "Sorry, I didn’t understand. Try: “login”, “password”, “signup”, or “farmer”.";
        }


        // Enter-to-send
        document.addEventListener('keydown', function(e){
            const input = document.getElementById('user-input');
            if (!input) return;
            if (e.key === 'Enter' && document.activeElement === input){
                e.preventDefault();
                sendMessage();
            }
        });
    </script>
</body>
</html>
