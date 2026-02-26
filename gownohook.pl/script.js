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
  function enterSite(e) {
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