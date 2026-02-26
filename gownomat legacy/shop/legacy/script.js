(function () {
    const overlay = document.getElementById('overlay');
    const audio = document.getElementById('bg-music');

    if (!overlay || !audio) return;

    audio.volume = 1.0;

    overlay.addEventListener('click', () => {
        audio.play().catch(() => {});
        overlay.style.display = 'none';
    });
})();

(function () {
    const audio = document.getElementById('bg-music');
    const muteBtn = document.getElementById('muteBtn');

    if (!audio || !muteBtn) return;

    const savedMuted = localStorage.getItem('muted') === 'true';
    audio.muted = savedMuted;

    function updateMuteUI() {
        if (audio.muted) {
            muteBtn.classList.add('muted');
            muteBtn.setAttribute('aria-pressed', 'true');
            muteBtn.title = 'Włącz dźwięk';
            muteBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
        } else {
            muteBtn.classList.remove('muted');
            muteBtn.setAttribute('aria-pressed', 'false');
            muteBtn.title = 'Wycisz dźwięk';
            muteBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
        }
    }

    muteBtn.addEventListener('click', () => {
        audio.muted = !audio.muted;
        localStorage.setItem('muted', audio.muted);
        updateMuteUI();
    });

    updateMuteUI();
})();

const cryptoAddresses = document.querySelectorAll('.crypto-address');
const notification = document.getElementById('copyNotification');

cryptoAddresses.forEach(address => {
    address.addEventListener('click', function () {
        const text = this.textContent.trim();

        navigator.clipboard.writeText(text).then(() => {
            notification.classList.add('show');
            setTimeout(() => notification.classList.remove('show'), 2000);
        }).catch(err => {
            console.error('Błąd kopiowania:', err);
            alert('Skopiowano: ' + text);
        });
    });
});

window.addEventListener('scroll', () => {
    const hero = document.querySelector('.hero');
    if (hero) {
        hero.style.transform = `translateY(${window.pageYOffset * 0.3}px)`;
    }
});