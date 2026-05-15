import flatpickr from 'flatpickr';
window.flatpickr = flatpickr;

window.playSound = (complete) => {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const notes = complete ? [523.25, 659.25, 783.99] : [783.99, 659.25, 523.25];
    notes.forEach((freq, i) => {
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.value = freq;
        const start = ctx.currentTime + i * 0.12;
        gain.gain.setValueAtTime(0.18, start);
        gain.gain.exponentialRampToValueAtTime(0.001, start + 0.4);
        osc.start(start);
        osc.stop(start + 0.4);
    });
};
