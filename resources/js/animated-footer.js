const initAnimatedFooter = () => {
    const footer = document.querySelector('[data-animated-footer]');

    if (!(footer instanceof HTMLElement)) {
        return;
    }

    const canvases = [...footer.querySelectorAll('[data-footer-canvas]')];
    const images = ['/animated-footer/hand-left.jpg', '/animated-footer/hand-right.jpg'];
    const contexts = canvases.map((canvas) => canvas.getContext('2d'));
    const cellsByCanvas = [];
    const columns = 48;
    const cellSize = 12;
    const characters = ' .:-=+*#%@';
    let animationFrame;

    const drawCanvas = (canvas, context, hand, pointer, time, side) => {
        if (!context) {
            return;
        }

        const width = columns * cellSize;
        const height = hand.rows * cellSize;
        const scale = Math.min(1, canvas.parentElement.clientWidth / width);

        canvas.width = width;
        canvas.height = height;
        canvas.style.width = `${width * scale}px`;
        canvas.style.height = `${height * scale}px`;
        context.clearRect(0, 0, width, height);
        context.font = `${cellSize}px monospace`;
        context.textAlign = 'center';
        context.textBaseline = 'middle';
        const driftX = Math.sin(time * 0.0012 + side) * 8;
        const driftY = Math.cos(time * 0.0009 + side) * 4;

        hand.cells.forEach((cell) => {
            const { column, row } = cell;
            const distance = Math.hypot(column - pointer.column, row - pointer.row);
            const highlighted = distance < 5;
            const x = column * cellSize + cellSize / 2 + driftX;
            const y = row * cellSize + cellSize / 2 + driftY;

            if (highlighted) {
                context.fillStyle = 'rgba(255, 106, 0, 0.85)';
                context.fillRect(x - cellSize / 2, y - cellSize / 2, cellSize, cellSize);
            }

            context.fillStyle = highlighted ? '#0f0f0f' : '#d86b2c';
            context.fillText(cell.character, x, y);
        });
    };

    const loadImage = (source, canvas, context) => new Promise((resolve) => {
        const image = new Image();
        image.onload = () => {
            const sampleCanvas = document.createElement('canvas');
            const rows = Math.max(1, Math.round(columns / (image.naturalWidth / image.naturalHeight || 1)));
            sampleCanvas.width = columns;
            sampleCanvas.height = rows;
            const sampleContext = sampleCanvas.getContext('2d');

            if (!sampleContext) {
                resolve();
                return;
            }

            sampleContext.drawImage(image, 0, 0, columns, rows);
            const pixels = sampleContext.getImageData(0, 0, columns, rows).data;
            const backgroundCharacterIndex = characters.indexOf('.');
            const cells = Array.from({ length: columns * rows }, (_, index) => {
                const column = index % columns;
                const row = Math.floor(index / columns);
                const offset = index * 4;
                const brightness = (pixels[offset] * 0.299 + pixels[offset + 1] * 0.587 + pixels[offset + 2] * 0.114) / 255;
                const characterIndex = Math.min(characters.length - 1, Math.floor((1 - brightness) * characters.length));

                if (characterIndex <= backgroundCharacterIndex) {
                    return null;
                }

                return { column, row, character: characters[characterIndex] };
            }).filter(Boolean);

            cellsByCanvas.push({ cells, rows, canvas, context });
            resolve();
        };
        image.src = source;
    });

    const pointers = canvases.map(() => ({ column: -20, row: -20 }));
    const handlePointerMove = (event) => {
        canvases.forEach((canvas, index) => {
            const bounds = canvas.getBoundingClientRect();
            pointers[index] = {
                column: ((event.clientX - bounds.left) / bounds.width) * columns,
                row: ((event.clientY - bounds.top) / bounds.height) * (canvas.height / cellSize),
            };
        });
    };

    footer.addEventListener('pointermove', handlePointerMove);
    Promise.all(canvases.map((canvas, index) => loadImage(images[index], canvas, contexts[index]))).then(() => {
        const render = () => {
            const time = performance.now();
            cellsByCanvas.forEach((hand, index) => drawCanvas(hand.canvas, hand.context, hand, pointers[index], time, index === 0 ? 0 : Math.PI));
            animationFrame = requestAnimationFrame(render);
        };

        render();
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAnimatedFooter, { once: true });
} else {
    initAnimatedFooter();
}
