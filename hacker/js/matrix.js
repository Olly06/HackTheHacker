/* ============================================================
   matrix.js — Animazione pioggia Matrix (sfondo sito hacker)
   ============================================================ */

(function () {
    const canvas = document.getElementById('matrix-canvas');
    if (!canvas) return;

    const ctx    = canvas.getContext('2d');
    const chars  = 'アイウエオカキクケコサシスセソタチツテトナニヌネノ0123456789ABCDEF<>{}[]|\\/?!@#$%^&*';
    const fontSize = 13;
    let cols, drops;

    function resize() {
        canvas.width  = window.innerWidth;
        canvas.height = window.innerHeight;
        cols  = Math.floor(canvas.width / fontSize);
        drops = Array(cols).fill(1);
    }

    function draw() {
        ctx.fillStyle = 'rgba(0,0,0,0.05)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        ctx.fillStyle = '#00ff41';
        ctx.font      = fontSize + 'px Share Tech Mono, monospace';

        for (let i = 0; i < drops.length; i++) {
            const char = chars[Math.floor(Math.random() * chars.length)];
            ctx.fillStyle = drops[i] * fontSize < canvas.height * 0.1
                ? '#ffffff'   // testa della colonna più luminosa
                : `rgba(0,${Math.floor(180 + Math.random() * 75)},60,${0.6 + Math.random() * 0.4})`;
            ctx.fillText(char, i * fontSize, drops[i] * fontSize);

            if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                drops[i] = 0;
            }
            drops[i]++;
        }
    }

    resize();
    window.addEventListener('resize', resize);
    setInterval(draw, 40);
})();
