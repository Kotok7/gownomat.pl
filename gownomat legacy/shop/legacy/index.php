<?php
require 'config.php';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Source+Serif+4:wght@300;400&display=swap" rel="stylesheet">
    <title>GÓWNOMAT sp. z o.o.</title>
    <meta name="description" content="Zaufane źródło z produktami najwyższej jakości!">
    <link rel="icon" href="photos/website-icon.jpg" type="image/png">
    <link rel="stylesheet" href="styles.css">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div id="overlay">[Kliknij, aby wejść]</div>

    <audio id="bg-music" loop>
        <source src="music.mp3" type="audio/mpeg">
    </audio>

    <div class="copy-notification" id="copyNotification">✓ Adres skopiowany!</div>

<header>
    <div class="logo-container">
        <div class="logo">GÓWNOMAT</div>
<a class="logo-sub logo-link" href="https://discord.gg/kotokkz" target="_blank" rel="noopener">.gg/kotokkz</a>
<a class="logo-sub logo-link" href="https://kotokk.xyz" target="_blank" rel="noopener">kotokk.xyz</a>
    </div>

    <nav class="top-links" aria-label="Szybkie linki">
  <a class="top-btn" href="clowns/index.php" target="_blank" rel="noopener">Hall of clowns</a>
<a class="top-btn" href="sprzedaz.mp4" target="_blank" rel="noopener">Wideo ze sprzedaży domeny</a>
</nav>

    <div class="controls">
        <div class="badge">Produkty najwyższej jakości</div>
        <button id="muteBtn" class="mute-btn" aria-pressed="false" title="Wycisz dźwięk">
            <i class="fas fa-volume-up"></i>
        </button>
    </div>
</header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Gówno <span class="highlight">Mat</span></h1>
                <p class="description">
                    Odkryj niezwykłą jakość i wyjątkowe właściwości. 
                    Ten produkt został stworzony z myślą o najbardziej wymagających klientach.
                </p>
                <div class="price-tag">$2.50/KG</div>
            </div>
            <div class="product-showcase">
                <img class="product-image" src="photos/cert.png">
            </div>
        </section>

        <section class="features">
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Najwyższa Jakość</h3>
                <p>Wykonany z najlepszych materiałów dostępnych na rynku. Każdy detal został dopracowany do perfekcji.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Bezpieczna Transakcja</h3>
                <p>Płatność kryptowalutami zapewnia pełną prywatność i bezpieczeństwo Twojej transakcji.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌟</div>
                <h3>Ekskluzywność</h3>
                <p>Limitowana edycja dostępna tylko dla wybranych. Nie przegap tej wyjątkowej okazji.</p>
            </div>
        </section>

        <section class="payment-section">
            <h2>Opcje Płatności Krypto</h2>
            
            <div class="info-box">
                <h3>Szczegóły Produktu</h3>
                <ul>
                    <li>Premium jakość wykonania</li>
                    <li>Gwarancja satysfakcji</li>
                    <li>Wysyłka w 24h od potwierdzenia płatności</li>
                    <li>Wsparcie klienta 24/7</li>
                    <li>Dożywotnia gwarancja</li>
                </ul>
            </div>

            <div class="crypto-addresses">
                <div class="crypto-item">
                    <div class="crypto-label">
                        <span>🟠</span> Bitcoin (BTC)
                    </div>
                    <div class="crypto-address">
                        bc1q5lfdr3pf4y98hh7dl70zwqxcf3v2kmcas9svez
                    </div>
                </div>

                <div class="crypto-item">
                    <div class="crypto-label">
                        <span>💎</span> Ethereum (ETH)
                    </div>
                    <div class="crypto-address">
                        0x112273b640A11e8F085767aaEAcBA937AcEDc99a
                    </div>
                </div>

                <div class="crypto-item">
                    <div class="crypto-label">
                        <span>🟣</span> Solana (SOL)
                    </div>
                    <div class="crypto-address">
                        ESK3yr5vvvzA3TKkLDcnDekBjCXDAxnsvvkBDbGhmv6d
                    </div>
                </div>

                <div class="crypto-item">
                    <div class="crypto-label">
                        <span>✖️</span> XRP
                    </div>
                    <div class="crypto-address">
                        r4SVAwsQUuYXt1na5eXVthE6srTtATUmA6
                    </div>
                </div>

                <div class="crypto-item">
                    <div class="crypto-label">
                        <span>⚪</span> Litecoin (LTC)
                    </div>
                    <div class="crypto-address">
                        LQiFKLpuDyWcbkpG42Y9djgygrQWtUsFEa
                    </div>
                </div>
            </div>

            <div class="info-box">
                <h3>Jak dokonać zakupu?</h3>
                <ul>
                    <li>Wybierz preferowaną kryptowalutę</li>
                    <li>Wyślij odpowiednią kwotę na podany adres</li>
                    <li>Skontaktuj się z nami podając hash transakcji</li>
                    <li>Otrzymasz potwierdzenie i informacje o ilości wysyłce</li>
                </ul>
            </div>

<form class="contactForm" id="messageForm">
<label for="name">Nick</label><br>
<input id="name" name="name" maxlength="20" required><br>

<label for="message">Wiadomość</label><br>
<textarea id="message" name="message" maxlength="200" required></textarea><br>

<div class="cf-turnstile"
     data-sitekey="<?php echo htmlspecialchars(TURNSTILE_SITE_KEY); ?>"
     data-callback="onTurnstileSuccess"
     data-error-callback="onTurnstileError"></div>

<div id="formError" role="alert" style="color:crimson;display:none"></div>

<button id="sendBtn" type="submit">Wyślij</button>
</form>

<script>
let turnstileToken = null;

function onTurnstileSuccess(token){
    turnstileToken = token;
    document.getElementById('formError').style.display='none';
}

function onTurnstileError(){
    turnstileToken = null;
    showError('Weryfikacja Turnstile nieudana — spróbuj ponownie.');
}

function showError(msg){
    const el = document.getElementById('formError');
    el.textContent = msg;
    el.style.display = 'block';
}

document.getElementById('messageForm').addEventListener('submit', async function(e){
    e.preventDefault();

    if(!turnstileToken){
        showError('Proszę przejść weryfikację (Turnstile) przed wysłaniem.  Jeśli wysyłałeś już jedną wiadomość, odśwież stronę.');
        return;
    }

    const formData = new FormData();
    formData.append('name', document.getElementById('name').value);
    formData.append('message', document.getElementById('message').value);
    formData.append('cf_turnstile_response', turnstileToken);

    try {
        const res = await fetch('verify.php', {
            method: 'POST',
            body: formData
        });

        const data = await res.json().catch(()=>null);
        if (!res.ok) {
            showError((data && data.error) ? data.error : 'Błąd wysyłania');
            return;
        }

        alert('Wiadomość wysłana ✔');
        document.getElementById('messageForm').reset();
        turnstileToken = null;

    } catch (err) {
        showError('Błąd sieci: ' + err.message);
    }
});
</script>

        </section>
    </main>

    <footer>
        <p>&copy; 2026 GÓWNOMAT sp. z o.o. Wszystkie prawa zastrzeżone.</p>
        <p>office@gownomat.pl</p>
        <p>Bezpieczne płatności kryptowalutowe | Dyskretna wysyłka | Wsparcie 24/7</p>
    </footer>
</body>
    <script src="script.js"></script>
</html>