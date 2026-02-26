window.contactTurnstileToken = null;
window.checkoutTurnstileToken = null;

window.onTurnstileSuccess = function(token) {
  window.contactTurnstileToken = token;
  const el = document.getElementById('formError');
  if (el) el.style.display = 'none';
  const hid = document.getElementById('cf-turnstile-response');
  if (hid) hid.value = token;
};

window.onTurnstileError = function() {
  window.contactTurnstileToken = null;
  const el = document.getElementById('formError');
  if (el) { el.textContent = 'Weryfikacja Turnstile nieudana — spróbuj ponownie.'; el.style.display = 'block'; }
};

window.onTurnstileCheckoutSuccess = function(token) {
  window.checkoutTurnstileToken = token;
  const el = document.getElementById('checkoutFormError');
  if (el) el.style.display = 'none';
  const hid = document.getElementById('cf-turnstile-response-checkout');
  if (hid) hid.value = token;
};

window.onTurnstileCheckoutError = function() {
  window.checkoutTurnstileToken = null;
  const el = document.getElementById('checkoutFormError');
  if (el) { el.textContent = 'Weryfikacja Turnstile nieudana — spróbuj ponownie.'; el.style.display = 'block'; }
};

(() => {
  let isMusicPlaying = false;
  let ltcPrice = 0;
  let cart = JSON.parse(localStorage.getItem('cart') || '[]');
  let currentProduct = null;

  const $ = (sel) => document.querySelector(sel);
  const $$ = (sel) => Array.from(document.querySelectorAll(sel));

  function ltcToUsd(ltcAmount) {
    return (ltcAmount * ltcPrice);
  }
  function usdToLtc(usdAmount) {
    if (!ltcPrice || ltcPrice === 0) return 0;
    return (usdAmount / ltcPrice);
  }

  function updatePricesInLTC() {
    if (!ltcPrice || ltcPrice === 0) return;

    const priceStdEl = $('#price-standard');
    const priceProEl = $('#price-pro');
    if (priceStdEl) priceStdEl.textContent = `${usdToLtc(1.5).toFixed(5)} LTC/kg`;
    if (priceProEl) priceProEl.textContent = `${usdToLtc(2.5).toFixed(5)} LTC/kg`;

    $$('input[name="delivery"]').forEach(input => {
      const usdPrice = parseFloat(input.dataset.usd || input.getAttribute('data-usd') || 0);
      const ltc = usdToLtc(usdPrice);
      input.dataset.ltc = ltc.toString();

      const box = input.closest('.delivery-option') || input.closest('.delivery-box') || input.parentElement;
      if (box) {
        const priceEl = box.querySelector('.delivery-price');
        if (priceEl) priceEl.textContent = `${ltc.toFixed(5)} LTC`;
      }
    });
  }

  function updateCartUI() {
    const cartCount = document.querySelector('.cart-count');
    const cartContent = document.getElementById('cartContent');
    const cartTotal = document.getElementById('cartTotal');

    if (cartCount) cartCount.textContent = cart.length;

    if (!cartContent) return;

    if (cart.length === 0) {
      cartContent.innerHTML = `
        <div class="empty-cart">
          <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1"/>
            <circle cx="20" cy="21" r="1"/>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
          </svg>
          <p>Koszyk jest pusty</p>
        </div>
      `;
      if (cartTotal) cartTotal.textContent = '0.000 LTC';
      return;
    }

    let total = 0;
    cartContent.innerHTML = cart.map((item, index) => {
      const itemTotal = item.price * item.kg;
      total += itemTotal;
      return `
        <div class="cart-item">
          <div class="cart-item-header">
            <span class="cart-item-name">${item.name}</span>
            <button class="cart-item-remove" onclick="removeFromCart(${index})">✕</button>
          </div>
          <div class="cart-item-details">
            <span>${item.kg} kg</span>
            <span class="cart-item-price">${itemTotal.toFixed(3)} LTC</span>
          </div>
        </div>
      `;
    }).join('');
    if (cartTotal) cartTotal.textContent = `${total.toFixed(3)} LTC`;
  }

  window.removeFromCart = function(index) {
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
  };

  window.copyLTCAddress = function(e) {

    const btn = (e && (e.currentTarget || e.target && e.target.closest('.copy-address-btn'))) || document.querySelector('.copy-address-btn');
    const addressEl = document.getElementById('ltcAddress');
    if (!addressEl) return;
    const address = addressEl.textContent.trim();
    navigator.clipboard.writeText(address).then(() => {
      if (!btn) return;
      const originalHTML = btn.innerHTML;
      btn.innerHTML = '<span>Skopiowano! ✓</span>';
      btn.classList.add('copied');
      setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.classList.remove('copied');
      }, 2000);
    }).catch(()=>{ /* optional error feedback */ });
  };

  function openProductModal(productType) {
    currentProduct = productType;
    const modal = document.getElementById('productModal');
    const overlay = document.getElementById('modalOverlay');

    const priceStandard = parseFloat(usdToLtc(1.5));
    const pricePro = parseFloat(usdToLtc(2.5));

    const products = {
      standard: { name: 'qupka Standard', emoji: '🛒', price: priceStandard },
      pro: { name: 'qupka Pro', emoji: '💎', price: pricePro }
    };

    const product = products[productType];
    if (!product) return;

    const modalTitle = document.getElementById('modalTitle');
    const modalEmoji = document.getElementById('modalEmoji');
    const modalProductName = document.getElementById('modalProductName');
    const modalProductPrice = document.getElementById('modalProductPrice');

    if (modalTitle) modalTitle.textContent = 'Dodaj do koszyka';
    if (modalEmoji) modalEmoji.textContent = product.emoji;
    if (modalProductName) modalProductName.textContent = product.name;
    if (modalProductPrice) modalProductPrice.textContent = `${product.price.toFixed(5)} LTC/kg`;

    const slider = document.getElementById('modalKgSlider');
    if (slider) slider.value = 1;
    const kgValue = document.getElementById('modalKgValue');
    if (kgValue) kgValue.textContent = '1.0';
    updateModalTotal();

    if (modal) modal.classList.add('active');
    if (overlay) overlay.classList.add('active');
  }

  function closeProductModal() {
    const modal = document.getElementById('productModal');
    const overlay = document.getElementById('modalOverlay');
    if (modal) modal.classList.remove('active');
    if (overlay) overlay.classList.remove('active');
  }

  function updateModalTotal() {
    if (!currentProduct) return;
    const priceStandard = parseFloat(usdToLtc(1.5));
    const pricePro = parseFloat(usdToLtc(2.5));
    const products = { standard: priceStandard, pro: pricePro };
    const slider = document.getElementById('modalKgSlider');
    const kg = slider ? parseFloat(slider.value) : 1;
    const price = products[currentProduct] || 0;
    const total = (price * kg).toFixed(3);
    const modalTotal = document.getElementById('modalTotal');
    if (modalTotal) modalTotal.textContent = `${total} LTC`;
  }

  function addToCart() {
    const priceStandard = parseFloat(usdToLtc(1.5));
    const pricePro = parseFloat(usdToLtc(2.5));
    const products = {
      standard: { name: 'qupka Standard', price: priceStandard },
      pro: { name: 'qupka Pro', price: pricePro }
    };
    const product = products[currentProduct];
    if (!product) return;
    const slider = document.getElementById('modalKgSlider');
    const kg = slider ? parseFloat(slider.value) : 1;
    cart.push({ name: product.name, price: product.price, kg: kg });
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
    closeProductModal();
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.getElementById('cartOverlay');
    if (cartSidebar) cartSidebar.classList.add('active');
    if (cartOverlay) cartOverlay.classList.add('active');
  }

  function openCheckoutModal() {
    const modal = document.getElementById('checkoutModal');
    const overlay = document.getElementById('modalOverlay');
    const checkoutItems = document.getElementById('checkoutItems');
    if (!checkoutItems) return;
    let subtotal = 0;
    checkoutItems.innerHTML = cart.map(item => {
      const itemTotal = item.price * item.kg;
      subtotal += itemTotal;
      return `
        <div class="checkout-item">
          <span>${item.name} (${item.kg} kg)</span>
          <span>${itemTotal.toFixed(3)} LTC</span>
        </div>
      `;
    }).join('');
    const checkoutSubtotal = document.getElementById('checkoutSubtotal');
    const finalSubtotal = document.getElementById('finalSubtotal');
    if (checkoutSubtotal) checkoutSubtotal.textContent = `${subtotal.toFixed(3)} LTC`;
    if (finalSubtotal) finalSubtotal.textContent = `${subtotal.toFixed(3)} LTC`;

    const selectedDelivery = document.querySelector('input[name="delivery"]:checked');
    if (selectedDelivery) {
      const deliveryPrice = parseFloat(selectedDelivery.dataset.ltc || 0);
      const total = subtotal + deliveryPrice;
      const finalDelivery = document.getElementById('finalDelivery');
      const finalTotal = document.getElementById('finalTotal');
      if (finalDelivery) finalDelivery.textContent = `${deliveryPrice.toFixed(5)} LTC`;
      if (finalTotal) finalTotal.textContent = `${total.toFixed(5)} LTC`;
    }

    if (modal) modal.classList.add('active');
    if (overlay) overlay.classList.add('active');
  }

  function closeCheckoutModal() {
    const modal = document.getElementById('checkoutModal');
    const overlay = document.getElementById('modalOverlay');
    if (modal) modal.classList.remove('active');
    if (overlay) overlay.classList.remove('active');
  }

  function updateCheckoutTotal() {
    let subtotal = 0;
    cart.forEach(item => subtotal += item.price * item.kg);
    const selectedDelivery = document.querySelector('input[name="delivery"]:checked');
    const deliveryPrice = parseFloat(selectedDelivery ? (selectedDelivery.dataset.ltc || 0) : 0);
    const total = subtotal + deliveryPrice;
    const finalDelivery = document.getElementById('finalDelivery');
    const finalTotal = document.getElementById('finalTotal');
    if (finalDelivery) finalDelivery.textContent = `${deliveryPrice.toFixed(5)} LTC`;
    if (finalTotal) finalTotal.textContent = `${total.toFixed(5)} LTC`;
  }

  function submitOrder() {
    const btn = document.querySelector('.checkout-submit-btn');
    if (!btn) return;
    btn.innerHTML = '<span>Zamówienie złożone!</span><span class="btn-icon">✓</span>';
    btn.style.background = '#10b981';
    btn.disabled = true;

    setTimeout(() => {
      cart = [];
      localStorage.setItem('cart', JSON.stringify(cart));
      updateCartUI();
      closeCheckoutModal();

      setTimeout(() => {
        btn.innerHTML = '<span>Złóż zamówienie</span><span class="btn-icon">→</span>';
        btn.style.background = '';
        btn.disabled = false;
      }, 500);
    }, 3000);
  }

  async function fetchLTCPrice() {
    try {
      const response = await fetch('https://api.coingecko.com/api/v3/simple/price?ids=litecoin&vs_currencies=usd&include_24hr_change=true');
      const data = await response.json();
      const price = data.litecoin.usd;
      const change = data.litecoin.usd_24h_change;
      ltcPrice = price;
      const ltcPriceEl = document.getElementById('ltcPrice');
      if (ltcPriceEl) ltcPriceEl.textContent = `$${price.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
      const changeEl = document.getElementById('ltcChange');
      if (changeEl) {
        changeEl.textContent = `${change >= 0 ? '+' : ''}${change.toFixed(2)}%`;
        changeEl.className = `ltc-change ${change >= 0 ? 'positive' : 'negative'}`;
      }
      updatePricesInLTC();
      drawChart(change);
    } catch (error) {
      console.error('Error fetching LTC price:', error);
      ltcPrice = 100;
      const ltcPriceEl = document.getElementById('ltcPrice');
      if (ltcPriceEl) ltcPriceEl.textContent = '$100.00';
      const changeEl = document.getElementById('ltcChange');
      if (changeEl) { changeEl.textContent = '+2.34%'; changeEl.className = 'ltc-change positive'; }
      updatePricesInLTC();
      drawChart(2.34);
    }
  }

  function drawChart(change) {
    const canvas = document.getElementById('ltcChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    const points = 30;
    const data = [];
    const trend = change >= 0 ? 1 : -1;
    for (let i = 0; i < points; i++) {
      const base = 45 + (i / points) * trend * 20;
      const noise = (Math.random() - 0.5) * 12;
      data.push(base + noise);
    }
    ctx.strokeStyle = change >= 0 ? '#10b981' : '#ef4444';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.beginPath();
    data.forEach((value, index) => {
      const x = (index / (points - 1)) * canvas.width;
      const y = canvas.height - value;
      if (index === 0) ctx.moveTo(x, y);
      else ctx.lineTo(x, y);
    });
    ctx.stroke();
    ctx.fillStyle = change >= 0 ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)';
    ctx.lineTo(canvas.width, canvas.height);
    ctx.lineTo(0, canvas.height);
    ctx.closePath();
    ctx.fill();
  }

  function initObserver() {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.animation = 'fadeIn 0.6s ease forwards';
        }
      });
    }, { threshold: 0.1 });
    $$(' .product-card, .feature-card, .info-card'.trim()).forEach(el => observer.observe(el));
  }

  document.addEventListener('DOMContentLoaded', () => {
    const bgMusic = document.getElementById('bgMusic');
    const musicToggle = document.getElementById('musicToggle');
    const splashScreen = document.getElementById('splashScreen');
    const enterBtn = document.getElementById('enterBtn');

    if (enterBtn && splashScreen && bgMusic) {
      enterBtn.addEventListener('click', () => {
        splashScreen.classList.add('hidden');
        bgMusic.play().then(() => { isMusicPlaying = true; }).catch(() => { if (musicToggle) musicToggle.classList.add('muted'); });
      });
    }

    if (musicToggle && bgMusic) {
      musicToggle.addEventListener('click', () => {
        if (isMusicPlaying) { bgMusic.pause(); if (musicToggle) musicToggle.classList.add('muted'); isMusicPlaying = false; }
        else { bgMusic.play(); if (musicToggle) musicToggle.classList.remove('muted'); isMusicPlaying = true; }
      });
    }

    $$('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (!href || href.length <= 1) return;
        const target = document.querySelector(href);
        if (!target) return;
        e.preventDefault();
        window.scrollTo({ top: target.offsetTop - 70, behavior: 'smooth' });
      });
    });

    window.addEventListener('scroll', () => {
      const navbar = document.querySelector('.navbar');
      if (!navbar) return;
      if (window.scrollY > 50) navbar.classList.add('scrolled'); else navbar.classList.remove('scrolled');
    });

    const cartBtn = document.getElementById('cartBtn');
    const cartClose = document.getElementById('cartClose');
    const cartOverlay = document.getElementById('cartOverlay');

    if (cartBtn) cartBtn.addEventListener('click', () => {
      const sidebar = document.getElementById('cartSidebar');
      if (sidebar) sidebar.classList.add('active');
      if (cartOverlay) cartOverlay.classList.add('active');
    });
    if (cartClose) cartClose.addEventListener('click', () => {
      const sidebar = document.getElementById('cartSidebar');
      if (sidebar) sidebar.classList.remove('active');
      if (cartOverlay) cartOverlay.classList.remove('active');
    });
    if (cartOverlay) cartOverlay.addEventListener('click', () => {
      const sidebar = document.getElementById('cartSidebar');
      if (sidebar) sidebar.classList.remove('active');
      if (cartOverlay) cartOverlay.classList.remove('active');
    });

    window.openProductModal = openProductModal;
    window.closeProductModal = closeProductModal;
    window.addToCart = addToCart;

    const modalSlider = document.getElementById('modalKgSlider');
    if (modalSlider) {
      modalSlider.addEventListener('input', (e) => {
        const value = parseFloat(e.target.value).toFixed(1);
        const kgEl = document.getElementById('modalKgValue');
        if (kgEl) kgEl.textContent = value;
        updateModalTotal();
      });
    }

    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
      checkoutBtn.addEventListener('click', () => {
        if (!cart || cart.length === 0) return;
        const sidebar = document.getElementById('cartSidebar');
        if (sidebar) sidebar.classList.remove('active');
        if (cartOverlay) cartOverlay.classList.remove('active');
        setTimeout(openCheckoutModal, 300);
      });
    }

    $$('input[name="delivery"]').forEach(radio => {
      radio.addEventListener('change', updateCheckoutTotal);
    });

    $$('.copy-address-btn').forEach(btn => {
      btn.addEventListener('click', window.copyLTCAddress);
    });

    const messageForm = document.getElementById('messageForm');
    if (messageForm) {
      messageForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (!window.contactTurnstileToken) {
          const el = document.getElementById('formError');
          if (el) { el.textContent = 'Proszę przejść weryfikację (Turnstile) przed wysłaniem.'; el.style.display = 'block'; }
          return;
        }
        const sendBtn = document.getElementById('sendBtn');
        const original = sendBtn ? sendBtn.innerHTML : null;
        if (sendBtn) { sendBtn.disabled = true; sendBtn.innerHTML = 'Wysyłanie…'; }

        const formData = new FormData();
        formData.append('type', 'message');
        formData.append('name', (document.getElementById('name') || {}).value || '');
        formData.append('message', (document.getElementById('message') || {}).value || '');
        formData.append('cf_turnstile_response', window.contactTurnstileToken);

        try {
          const res = await fetch('verify.php', { method: 'POST', body: formData, credentials: 'same-origin' });
          const data = await res.json().catch(()=>null);
          if (!res.ok) {
            const el = document.getElementById('formError');
            if (el) { el.textContent = (data && data.error) ? data.error : 'Błąd wysyłania'; el.style.display = 'block'; }
            if (sendBtn) { sendBtn.disabled = false; sendBtn.innerHTML = original; }
            return;
          }
          alert('Wiadomość wysłana ✔');
          messageForm.reset();
          window.contactTurnstileToken = null;
          if (sendBtn) { sendBtn.innerHTML = 'Wysłano!'; setTimeout(()=>{ sendBtn.innerHTML = original; sendBtn.disabled = false; }, 1200); }
        } catch (err) {
          const el = document.getElementById('formError');
          if (el) { el.textContent = 'Błąd sieci: ' + (err && err.message ? err.message : 'nieznany'); el.style.display = 'block'; }
          if (sendBtn) { sendBtn.disabled = false; sendBtn.innerHTML = original; }
        }
      });
    }

    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
      checkoutForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const name = (document.getElementById('checkoutFullName') || {}).value || '';
        const email = (document.getElementById('checkoutEmail') || {}).value || '';
        const address = (document.getElementById('checkoutAddress') || {}).value || '';

        if (!name.trim() || !email.trim() || !address.trim()) {
          const el = document.getElementById('checkoutFormError');
          if (el) { el.textContent = 'Wypełnij wszystkie pola.'; el.style.display = 'block'; }
          return;
        }
        if (!window.checkoutTurnstileToken) {
          const el = document.getElementById('checkoutFormError');
          if (el) { el.textContent = 'Proszę przejść weryfikację Turnstile przed złożeniem zamówienia.'; el.style.display = 'block'; }
          return;
        }

        const submitBtn = checkoutForm.querySelector('.checkout-submit-btn');
        const originalHTML = submitBtn ? submitBtn.innerHTML : null;
        if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = 'Wysyłanie…'; }

        const cartData = cart || JSON.parse(localStorage.getItem('cart') || '[]');
        const selectedDelivery = document.querySelector('input[name="delivery"]:checked');
        const deliveryLtc = selectedDelivery ? (selectedDelivery.dataset.ltc || '') : '';

        const formData = new FormData();
        formData.append('type', 'order');
        formData.append('name', name.trim());
        formData.append('email', email.trim());
        formData.append('address', address.trim());
        formData.append('delivery', selectedDelivery ? selectedDelivery.value : '');
        formData.append('delivery_ltc', deliveryLtc);
        formData.append('order', JSON.stringify(cartData));
        formData.append('cf_turnstile_response', window.checkoutTurnstileToken);

        try {
          const res = await fetch('verify.php', { method: 'POST', body: formData, credentials: 'same-origin' });
          const data = await res.json().catch(()=>null);
          if (!res.ok) {
            const el = document.getElementById('checkoutFormError');
            if (el) { el.textContent = (data && data.error) ? data.error : 'Błąd wysyłania zamówienia'; el.style.display = 'block'; }
            if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalHTML; }
            return;
          }
          alert('Zamówienie wysłane ✔');
          cart = [];
          localStorage.setItem('cart', JSON.stringify(cart));
          if (typeof updateCartUI === 'function') updateCartUI();
          if (typeof closeCheckoutModal === 'function') closeCheckoutModal();
          if (submitBtn) { submitBtn.innerHTML = 'Wysłano!'; setTimeout(()=>{ submitBtn.innerHTML = originalHTML; submitBtn.disabled = false; }, 1400); }
          checkoutForm.reset();
          window.checkoutTurnstileToken = null;
        } catch (err) {
          const el = document.getElementById('checkoutFormError');
          if (el) { el.textContent = 'Błąd sieci: ' + (err && err.message ? err.message : 'nieznany'); el.style.display = 'block'; }
          if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalHTML; }
        }
      });
    }

    try { initObserver(); } catch(e){/* ignore */}

    window.submitOrder = submitOrder;

    updateCartUI();
    fetchLTCPrice();
    setInterval(fetchLTCPrice, 60000);
  });

})();