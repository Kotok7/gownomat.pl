<?php
require 'config.php';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>gownomat.pl</title>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<link rel="stylesheet" href="style.css">
<meta name="description" content="kup sobie qupke byczqu! .gg/kotokkz">
<link rel="icon" href="photos/logo.png" type="image/png">
</head>
<body>

<button id="intro">
  <div id="intro-text">Kliknij aby wejść</div>
  <div id="intro-sub">[ kotokkz ]</div>
</button>

<div id="wp-modal">
  <div id="wp-modal-box">
    <button id="wp-close" aria-label="Zamknij">✕</button>
    <h3>Tapety</h3>
    <p>Wybierz wersję do pobrania</p>
    <div class="wp-btns">
      <a href="photos/gownomat-na-komputr.png" download class="wp-btn">
        <span class="wp-icon">🖥</span>
        Komputer
        <span class="wp-label">tapeta na zimnioka</span>
      </a>
      <a href="photos/gownomat-na-trapphone.png" download class="wp-btn">
        <span class="wp-icon">📱</span>
        Telefon
        <span class="wp-label">tapeta na trapphona</span>
      </a>
    </div>
  </div>
</div>

<div id="main-page">
  <video id="bg" autoplay muted loop playsinline>
  <source src="photos/pozar.mp4" type="video/mp4">
</video>

  <nav>
    <button id="wp-open-btn" class="nav-btn" type="button">Tapety</button>
    <button id="change-music" class="nav-btn" type="button">Change music</button>
    <a href="/shop/photos/sprzedaz.mp4" target="_blank">Wideo z zakupu domeny</a>
    <a href="shop/index.php" target="_blank">Shop</a>
    <a href="clowns/index.php" target="_blank">Hall of Clowns</a>
    <a href="https://kotokk.xyz" target="_blank">kotokk.xyz</a>
  </nav>

  <div id="discord-banner">
    <a href="https://discord.gg/kotokkz" target="_blank">.gg/kotokkz</a>
  </div>

  <div id="counters" style="position:relative;z-index:10;text-align:center;padding:6px 12px;color:#fff;font-family:'Russo One',sans-serif;">
    👁️<span id="daily-counter">Dzisiaj: -</span> &nbsp;|&nbsp; <span id="all-counter">Wszystkie: -</span>
  </div>

  <div id="panel-left">
    <div id="discord-div">
      <div id="game-div">
        <div class="image-status">
          <img src="photos/discord-profile.png" style="border-radius: 50px;">
          <div id="status-mini-icon">
            <div id="status-mini-icon-2"></div>
          </div>
        </div>
        <div class="status-div">
          <strong>@kotokkz</strong>
          <p>
            <strong>Status:</strong>
            <span id="status"></span>
          </p>
          <p id="game"></p>
          <div id="game-time-container" style="display:none;"></div>
          <strong id="game-time"></strong>
          <p>
            <strong>Note:</strong>
            <span id="note"></span>
          </p>
        </div>
      </div>
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
        </div>
</section>

  <div id="panel-right">
    <div class="crypto-card" id="card-usd">
      <div class="crypto-name">USD / PLN</div>
      <div class="crypto-price crypto-loading">...</div>
    </div>
    <div class="crypto-card" id="card-btc">
      <div class="crypto-name">Bitcoin</div>
      <div class="crypto-price crypto-loading">...</div>
    </div>
    <div class="crypto-card" id="card-sol">
      <div class="crypto-name">Solana</div>
      <div class="crypto-price crypto-loading">...</div>
    </div>
    <div class="crypto-card" id="card-ltc">
      <div class="crypto-name">Litecoin</div>
      <div class="crypto-price crypto-loading">...</div>
    </div>
  </div>

  <div id="vol-wrap">
    <label for="vol-slider">Vol</label>
    <input type="range" id="vol-slider" min="0" max="100" value="50">
    <span id="vol-val">50%</span>
  </div>

  <footer>© kotokkz<br>
contact: support@gownomat.pl / null@kotokk.xyz
</footer>
</div>

<audio id="bg-music" src="photos/music1.mp3" loop></audio>
<script>let currentLanguage = 'pl';</script>
<script src="discord.js"></script>
<script>
const intro    = document.getElementById('intro');
const mainPage = document.getElementById('main-page');
const music    = document.getElementById('bg-music');
const slider   = document.getElementById('vol-slider');
const volVal   = document.getElementById('vol-val');

if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
  document.getElementById('vol-wrap').style.display = 'none';
}

function setupMobile() {
  const isMobile = window.innerWidth <= 768;
  const existing = document.getElementById('panels-mobile');
  if (isMobile && !existing) {
    const wrapper = document.createElement('div');
    wrapper.id = 'panels-mobile';
    const left  = document.getElementById('panel-left');
    const right = document.getElementById('panel-right');
    const vol   = document.getElementById('vol-wrap');
    left.parentNode.insertBefore(wrapper, left);
    wrapper.appendChild(left);
    wrapper.appendChild(right);
    wrapper.appendChild(vol);
  } else if (!isMobile && existing) {
    const left  = document.getElementById('panel-left');
    const right = document.getElementById('panel-right');
    const vol   = document.getElementById('vol-wrap');
    existing.parentNode.insertBefore(left, existing);
    existing.parentNode.insertBefore(right, existing);
    existing.parentNode.insertBefore(vol, existing);
    existing.remove();
  }
}
setupMobile();
window.addEventListener('resize', setupMobile);

let entered = false;
async function enterSite(e) {
  e.preventDefault();
  if (entered) return;
  entered = true;
  intro.classList.add('hidden');
  setTimeout(() => { intro.style.display = 'none'; }, 800);
  mainPage.classList.add('visible');
  music.volume = 0.5;
  music.play().catch(() => {});
  fetchCrypto();
  fetchUSD();
  try {
    const r = await fetch('visitor.php', { method: 'GET', cache: 'no-store' });
    const j = await r.json().catch(()=>null);
    if (j) {
      document.getElementById('daily-counter').textContent = 'Dzisiaj: ' + j.daily;
      document.getElementById('all-counter').textContent = 'Wszystkie: ' + j.all;
    }
  } catch(_) {}
}
intro.addEventListener('click', enterSite);
intro.addEventListener('touchend', enterSite);

slider.addEventListener('input', () => {
  music.volume = slider.value / 100;
  volVal.textContent = slider.value + '%';
});

const wpModal   = document.getElementById('wp-modal');
const wpOpenBtn = document.getElementById('wp-open-btn');
const wpClose   = document.getElementById('wp-close');
wpOpenBtn.addEventListener('click', () => wpModal.classList.add('open'));
wpClose.addEventListener('click',   () => wpModal.classList.remove('open'));
wpModal.addEventListener('click', (e) => { if (e.target === wpModal) wpModal.classList.remove('open'); });

async function fetchCrypto() {
  let ok = false;
  try {
    const res = await fetch(
      'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,solana,litecoin&vs_currencies=usd&include_24hr_change=true',
      { signal: AbortSignal.timeout(7000) }
    );
    if (res.ok) {
      const d = await res.json();
      if (d.bitcoin && d.solana && d.litecoin) {
        renderCrypto('card-btc', d.bitcoin.usd,  d.bitcoin.usd_24h_change);
        renderCrypto('card-sol', d.solana.usd,   d.solana.usd_24h_change);
        renderCrypto('card-ltc', d.litecoin.usd, d.litecoin.usd_24h_change);
        ok = true;
      }
    }
  } catch(_) {}
  if (!ok) {
    const map = [
      { id: 'btc-bitcoin',  card: 'card-btc' },
      { id: 'sol-solana',   card: 'card-sol' },
      { id: 'ltc-litecoin', card: 'card-ltc' }
    ];
    await Promise.all(map.map(async ({ id, card }) => {
      try {
        const r = await fetch(`https://api.coinpaprika.com/v1/tickers/${id}`, { signal: AbortSignal.timeout(7000) });
        if (r.ok) {
          const j = await r.json();
          renderCrypto(card, j.quotes.USD.price, j.quotes.USD.percent_change_24h);
          ok = true;
        }
      } catch(_) {}
    }));
  }
  if (!ok) {
    ['card-btc','card-sol','card-ltc'].forEach(id => {
      const el = document.querySelector(`#${id} .crypto-price`);
      if (el) { el.textContent = 'N/A'; el.classList.remove('crypto-loading'); }
    });
  }
  setTimeout(fetchCrypto, 60000);
}

function renderCrypto(cardId, price, change) {
  const card    = document.getElementById(cardId);
  const priceEl = card.querySelector('.crypto-price');
  priceEl.classList.remove('crypto-loading');
  priceEl.textContent = '$' + Number(price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  let changeEl = card.querySelector('.crypto-change');
  if (!changeEl) { changeEl = document.createElement('div'); card.appendChild(changeEl); }
  const c = Number(change) || 0;
  changeEl.textContent = (c >= 0 ? '+' : '') + c.toFixed(2) + '%';
  changeEl.className = 'crypto-change ' + (c >= 0 ? 'up' : 'down');
}

async function fetchUSD() {
  const priceEl = document.querySelector('#card-usd .crypto-price');
  let ok = false;
  try {
    const r = await fetch('https://api.nbp.pl/api/exchangerates/rates/a/usd/?format=json', { signal: AbortSignal.timeout(7000) });
    if (r.ok) {
      const j = await r.json();
      priceEl.classList.remove('crypto-loading');
      priceEl.textContent = j.rates[0].mid.toFixed(4) + ' PLN';
      ok = true;
    }
  } catch(_) {}
  if (!ok) {
    try {
      const r = await fetch('https://api.frankfurter.app/latest?from=USD&to=PLN', { signal: AbortSignal.timeout(7000) });
      if (r.ok) {
        const j = await r.json();
        priceEl.classList.remove('crypto-loading');
        priceEl.textContent = j.rates.PLN.toFixed(4) + ' PLN';
        ok = true;
      }
    } catch(_) {}
  }
  if (!ok) { priceEl.classList.remove('crypto-loading'); priceEl.textContent = 'N/A'; }
  setTimeout(fetchUSD, 3600000);
}

const tracks = [
  'photos/music1.mp3',
  'photos/music2.mp3',
  'photos/music3.mp3',
  'photos/music4.mp3',
  'photos/music5.mp3'
];
let trackIndex = 0;
const changeBtn = document.getElementById('change-music');
changeBtn.addEventListener('click', () => {
  trackIndex = (trackIndex + 1) % tracks.length;
  music.src = tracks[trackIndex];
  music.play().catch(()=>{});
});

</script>
<script src="script.js"></script>
</body>
</html>