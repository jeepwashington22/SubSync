const initStarsBackground = () => {
    const canvas = document.querySelector('#stars-background');

    if (!(canvas instanceof HTMLCanvasElement)) {
        return;
    }

    const context = canvas.getContext('2d');

    if (!context) {
        return;
    }

    let animationFrame;
    let stars = [];

    const resizeCanvas = () => {
        const bounds = canvas.parentElement?.getBoundingClientRect();

        canvas.width = bounds?.width ?? window.innerWidth;
        canvas.height = bounds?.height ?? window.innerHeight;
        stars = Array.from({ length: Math.max(45, Math.floor((canvas.width * canvas.height) / 9000)) }, () => ({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            radius: Math.random() * 1.4 + 0.3,
            opacity: Math.random() * 0.7 + 0.25,
            phase: Math.random() * Math.PI * 2,
            speed: Math.random() * 0.02 + 0.008,
        }));
    };

    const animate = () => {
        const { width, height } = canvas;

        context.clearRect(0, 0, width, height);
        stars.forEach((star) => {
            star.phase += star.speed;
            const opacity = star.opacity * (0.7 + Math.sin(star.phase) * 0.3);

            context.beginPath();
            context.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
            context.fillStyle = `rgba(255, 255, 255, ${opacity})`;
            context.fill();
        });

        animationFrame = requestAnimationFrame(animate);
        return;

    };

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);
    animate();

    return () => {
        window.removeEventListener('resize', resizeCanvas);
        cancelAnimationFrame(animationFrame);
    };
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStarsBackground, { once: true });
} else {
    initStarsBackground();
}