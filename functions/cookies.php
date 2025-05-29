<?php 
function cookies() {
    if(!isset($_COOKIE['cookie_consent'])) {
        echo '<div id="cookie-banner" class="cookie-banner">
            <div class="cookie-content">
                <p class="text-white">Táto stránka používa cookies pre zlepšenie používateľskej skúsenosti.</p>
                <button onclick="acceptCookies()" class="cookie-btn">Súhlasím</button>
            </div>
        </div>

        <style>
        .cookie-banner {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.85);
            color: white;
            padding: 15px 20px;
            z-index: 1050;
            display: flex;
            justify-content: center;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
        }

        .cookie-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            width: 100%;
        }

        .cookie-content p {
            margin: 0;
            padding-right: 20px;
        }

        .cookie-btn {
            background: #b78c51; /* Zlatá farba podľa vašej schémy */
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            white-space: nowrap;
        }

        .cookie-btn:hover {
            background: #d4a76a;
        }

        @media (max-width: 768px) {
            .cookie-content {
                flex-direction: column;
                text-align: center;
            }
            
            .cookie-content p {
                margin-bottom: 10px;
                padding-right: 0;
            }
        }
        </style>

        <script>
        function acceptCookies() {
            document.cookie = "cookie_consent=1; max-age=31536000; path=/; SameSite=Lax";
            document.getElementById("cookie-banner").style.display = "none";
        }
        </script>';
    }
}