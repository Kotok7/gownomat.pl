<?php
require 'config.php';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gownomat sp. z o.o.</title>
    <meta name="description" content="Zaufane źródło z produktami najwyższej jakości!">
    <link rel="icon" href="photos/cert.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="splash-screen" id="splashScreen">
        <div class="splash-content">
            <h1 class="splash-title">gownomat.pl</h1>
            <button class="splash-btn" id="enterBtn">Kliknij aby wejść</button>
        </div>
    </div>

    <audio id="bgMusic" muted>
        <source src="photos/music.mp3" type="audio/mp4">
    </audio>

    <button id="musicToggle" class="music-btn">
        <span class="music-icon">♪</span>
    </button>

    <nav class="navbar">
        <div class="nav-wrapper">
            <div class="logo">gownomat.pl</div>
            <div class="nav-links">
                <a href="#home">Home</a>
                <a href="#product">Produkty</a>
                <a href="#about">O nas</a>
                <a href="#contact">Kontakt</a>
            </div>
            <div class="nav-actions">
                <button class="cart-btn" id="cartBtn">
                    <svg class="cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    <span class="cart-count">0</span>
                </button>
<a href="https://discord.gg/kotokkz" class="nav-link-btn discord" id="link1" target="_blank">Discord</a>
            </div>
        </div>
    </nav>

    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h2>Koszyk</h2>
            <button class="cart-close" id="cartClose">✕</button>
        </div>
        <div class="cart-content" id="cartContent">
            <div class="empty-cart">
                <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <p>Koszyk jest pusty</p>
            </div>
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Razem:</span>
                <span id="cartTotal">0.000 LTC</span>
            </div>
            <button class="checkout-btn" id="checkoutBtn">Przejdź do płatności</button>
        </div>
    </div>

    <div class="cart-overlay" id="cartOverlay"></div>

    <section id="home" class="hero">
        <div class="hero-content">
            <h1 class="hero-title">
                <span class="title-word">kup</span>
                <span class="title-word">qupke</span>
                <span class="title-word">za</span>
                <span class="title-word crypto">crypto</span>
            </h1>
            <p class="hero-subtitle">Premium produkty</p>
            <a href="#product" class="hero-btn">Zobacz ofertę</a>
        </div>
        <div class="ltc-widget">
            <div class="ltc-header">
                <img src="https://cryptologos.cc/logos/litecoin-ltc-logo.svg" alt="LTC" class="ltc-logo">
                <span class="ltc-label">Litecoin</span>
            </div>
            <div class="ltc-price" id="ltcPrice">$0.00</div>
            <div class="ltc-change" id="ltcChange">+0.00%</div>
            <canvas id="ltcChart" width="300" height="100"></canvas>
        </div>
    </section>

    <section id="product" class="product-section">
        <div class="container">
            <h2 class="section-title">Nasze Produkty</h2>
            
            <div class="products-grid">
                <div class="product-card" data-product="standard">
                    <div class="product-badge">Standard</div>
                    <div class="product-img">
                        <span class="product-emoji">🛒</span>
                    </div>
                    <h3 class="product-name">qupka standard</h3>
                    <p class="product-desc">Podstawowa wersja dla początkujących. Solidna jakość w przystępnej cenie.</p>
                    <div class="product-specs">
                        <div class="spec">
                            <span class="spec-label">Materiał:</span>
                            <span class="spec-value">Organiczny</span>
                        </div>
                        <div class="spec">
                            <span class="spec-label">Zapach:</span>
                            <span class="spec-value">Naturalny</span>
                        </div>
                        <div class="spec">
                            <span class="spec-label">Certyfikat:</span>
                            <span class="spec-value">ISO 9001</span>
                        </div>
                    </div>
                    <div class="product-price" id="price-standard">0.000 LTC/kg</div>
                    <button class="add-to-cart-btn" onclick="openProductModal('standard')">
                        <span>Dodaj do koszyka</span>
                        <span class="btn-icon">+</span>
                    </button>
                </div>

                <div class="product-card featured">
                    <div class="product-badge pro">Premium</div>
                    <div class="product-img">
                        <span class="product-emoji pro">💎</span>
                    </div>
                    <h3 class="product-name">qupka Pro</h3>
                    <p class="product-desc">Premium wersja z dodatkowymi funkcjami. Dla wymagających klientów.</p>
                    <div class="product-specs">
                        <div class="spec">
                            <span class="spec-label">Materiał:</span>
                            <span class="spec-value">Bio-premium</span>
                        </div>
                        <div class="spec">
                            <span class="spec-label">Zapach:</span>
                            <span class="spec-value">Perfumowany</span>
                        </div>
                        <div class="spec">
                            <span class="spec-label">Certyfikat:</span>
                            <span class="spec-value">ISO 14001</span>
                        </div>
                    </div>
                    <div class="product-price pro" id="price-pro">0.000 LTC/kg</div>
                    <button class="add-to-cart-btn pro" onclick="openProductModal('pro')">
                        <span>Dodaj do koszyka</span>
                        <span class="btn-icon">+</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="about-section">
        <div class="container">
            <h2 class="section-title">Dlaczego My?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>100% Bezpiecznie</h3>
                    <p>Wszystkie transakcje są szyfrowane i chronione przez blockchain.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Szybka Dostawa</h3>
                    <p>Wysyłka zamówienia w 24-48h. Współpracujemy z najlepszymi firmami kurierskimi.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💎</div>
                    <h3>Najwyższa Jakość</h3>
                    <p>Każdy produkt przechodzi rygorystyczną kontrolę jakości.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🌍</div>
                    <h3>Globalny Zasięg</h3>
                    <p>Dołącz do jednego z 1000+ zadowolonych klientów!</p>
                </div>
            </div>

            <div class="info-section">
                <div class="info-card">
                    <h3>O Produkcie</h3>
                    <p>Nasze produkty powstają z najwyższej jakości materiałów organicznych. Proces produkcji jest w pełni ekologiczny i zrównoważony. Każda partia jest testowana pod kątem jakości i bezpieczeństwa przez niezależne laboratoria.</p>
                    <p>Wykorzystujemy innowacyjne technologie pakowania, które zapewniają świeżość i zachowanie wszystkich właściwości produktu podczas transportu. Nasza firma jako jedna z nielicznych na rynku posiada certyfikat ISO 9001 i 14001.</p>
                </div>
                <div class="info-card">
                    <h3>O Firmie</h3>
                    <p>gownomat powstał w 2026 roku z inicjatywy grupy entuzjastów technologii blockchain. Naszym celem jest pokazanie, że nawet najbardziej niedostępne produkty mogą być sprzedawane w profesjonalny sposób.</p>
                    <p>Jesteśmy firmą w pełni legalną, zarejestrowaną w Kraśnik. Obsługujemy klientów z całej Polski, akceptując Litecoin. Do tej pory zrealizowaliśmy ponad 1000 zamówień z 99% poziomem zadowolenia klientów.</p>
                </div>
                <div class="info-card">
                    <h3>Nasz Zespół</h3>
                    <p>Za sukcesem gownomatu stoi Polski zespół specjalistów z zakresu e-commerce, technologii blockchain i logistyki. Nasz CEO to były pracownik Zoinbase Support, CTO pochodzi z zespołu Fakemink fan group.</p>
                    <p>Wierzymy w transparentność i otwartą komunikację z klientami. Nasz zespół support jest dostępny 24/7 i odpowiada na wszystkie pytania w ciągu maksymalnie 2 godzin. Jesteśmy dumni z tego, że budujemy społeczność wokół naszej marki.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="contact-form">
        <div class="container-small">
            <h2 class="section-title">Kontakt</h2>
            <form class="contact-form" id="messageForm">
<label for="name">Nick</label><br>
<input id="name" name="name" class="form-input" maxlength="20" required>

<label for="message">Wiadomość</label><br>
<textarea id="message" name="message" class="form-input" maxlength="200" required></textarea>

<div class="cf-turnstile"
     data-sitekey="<?php echo htmlspecialchars(TURNSTILE_SITE_KEY); ?>"
     data-callback="onTurnstileSuccess"
     data-error-callback="onTurnstileError"></div>

<div id="formError" role="alert" style="color:crimson;display:none"></div>

<button id="sendBtn" type="submit" class="submit-btn">Wyślij</button>
</form>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h4>gownomat.pl</h4>
                    <p>Premium produkty za crypto.</p>
                </div>
                <div class="footer-col">
                    <h4>Akceptujemy</h4>
                    <div class="crypto-logos">
                        <img src="https://cryptologos.cc/logos/litecoin-ltc-logo.svg" alt="LTC">
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2026 gownomat.pl | Wszystkie prawa zastrzeżone.</p>
            </div>
        </div>
    </footer>

    <div class="product-modal" id="productModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeProductModal()">✕</button>
            <h3 id="modalTitle">Dodaj do koszyka</h3>
            <div class="modal-product-info">
                <span class="modal-emoji" id="modalEmoji">💎</span>
                <div>
                    <div class="modal-product-name" id="modalProductName"></div>
                    <div class="modal-product-price" id="modalProductPrice"></div>
                </div>
            </div>
            <div class="modal-section">
                <label class="modal-label">Ilość (kg): <span id="modalKgValue">1.0</span> kg</label>
                <input type="range" class="modal-slider" id="modalKgSlider" min="0.5" max="50" step="0.5" value="1">
                <div class="modal-slider-labels">
                    <span>0.5 kg</span>
                    <span>50 kg</span>
                </div>
            </div>
            <div class="modal-total">
                <span>Razem:</span>
                <span id="modalTotal">0.000 LTC</span>
            </div>
            <button class="modal-add-btn" onclick="addToCart()">
                <span>Dodaj do koszyka</span>
                <span class="btn-icon">+</span>
            </button>
        </div>
    </div>

    <div class="checkout-modal" id="checkoutModal">
        <div class="checkout-content">
<button type="button" class="modal-close" onclick="closeCheckoutModal()">✕</button>
            <h3>Finalizacja zamówienia</h3>
            
            <div class="checkout-section">
                <h4>Podsumowanie koszyka</h4>
                <div id="checkoutItems"></div>
                <div class="checkout-subtotal">
                    <span>Produkty:</span>
                    <span id="checkoutSubtotal">0.000 LTC</span>
                </div>
            </div>

            <div class="checkout-section">
                <h4>Metoda dostawy</h4>
                <div class="delivery-options">
                    <label class="delivery-option">
                        <input type="radio" name="delivery" value="inpost" data-usd="1" checked>
                        <div class="delivery-box">
                            <img src="photos/inpost-logo.webp" alt="InPost" class="delivery-logo">
                            <div class="delivery-info">
                                <div class="delivery-name">InPost Paczkomat</div>
                                <div class="delivery-time">12-24h</div>
                            </div>
                            <div class="delivery-price" id="inpost-price">0.000 LTC</div>
                        </div>
                    </label>
                    <label class="delivery-option">
                        <input type="radio" name="delivery" value="dpd" data-usd="2">
                        <div class="delivery-box">
                            <img src="photos/dpd.png" alt="DPD" class="delivery-logo">
                            <div class="delivery-info">
                                <div class="delivery-name">DPD Automat</div>
                                <div class="delivery-time">24-48h</div>
                            </div>
                            <div class="delivery-price" id="dpd-price">0.000 LTC</div>
                        </div>
                    </label>
                    <label class="delivery-option">
                        <input type="radio" name="delivery" value="dhl" data-usd="5">
                        <div class="delivery-box">
                            <img src="photos/dhl.png" alt="DHL" class="delivery-logo">
                            <div class="delivery-info">
                                <div class="delivery-name">DHL Kurier</div>
                                <div class="delivery-time">48-72h</div>
                            </div>
                            <div class="delivery-price" id="dhl-price">0.000 LTC</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="checkout-section">
                <h4>Adres do przelewu</h4>
                <div class="crypto-address-box">
                    <div class="crypto-address-header">
                        <span class="crypto-address-label">Litecoin (LTC)</span>
                    </div>
                    <div class="crypto-address-value" id="ltcAddress">LQiFKLpuDyWcbkpG42Y9djgygrQWtUsFEa</div>
                    <button type="button" class="copy-address-btn" onclick="copyLTCAddress()">
                        <span>Kopiuj adres</span>
                    </button>
                </div>
                <div class="checkout-note">
                    💡 Wyślij dokładną kwotę na powyższy adres, następnie podaj swoje dane kontaktowe
                </div>
            </div>

<form id="checkoutForm" class="checkout-form" novalidate>
  <div class="checkout-section">
    <h4>Twoje dane</h4>
    <input type="text" name="full_name" id="checkoutFullName" class="checkout-input" placeholder="Imię i nazwisko" required>
    <input type="email" name="email" id="checkoutEmail" class="checkout-input" placeholder="Email" required>
    <textarea name="address" id="checkoutAddress" class="checkout-input" placeholder="Adres dostawy" rows="3" required></textarea>
  </div>

  <div class="cf-turnstile"
       data-sitekey="<?php echo htmlspecialchars(TURNSTILE_SITE_KEY); ?>"
       data-callback="onTurnstileCheckoutSuccess"
       data-error-callback="onTurnstileCheckoutError"></div>

  <div id="checkoutFormError" role="alert" style="color:crimson;display:none;margin-bottom:0.6rem"></div>

  <button type="submit" class="checkout-submit-btn">Złóż zamówienie</button>
</form>

            <div class="checkout-total-section">
                <div class="checkout-row">
                    <span>Produkty:</span>
                    <span id="finalSubtotal">0.000 LTC</span>
                </div>
                <div class="checkout-row">
                    <span>Dostawa:</span>
                    <span id="finalDelivery">0.000 LTC</span>
                </div>
                <div class="checkout-divider"></div>
                <div class="checkout-row total">
                    <span>Do zapłaty:</span>
                    <span id="finalTotal">0.000 LTC</span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modalOverlay"></div>
    <script src="script.js"></script>
</body>
</html>