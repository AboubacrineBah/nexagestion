import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const lines = document.querySelector('[data-sale-lines]');
    const addLine = document.querySelector('[data-add-line]');
    const gmtClock = document.querySelector('#gmt-clock');

    if (gmtClock) {
        const formatter = new Intl.DateTimeFormat('fr-FR', {
            timeZone: 'UTC',
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hourCycle: 'h23',
        });

        const updateGmtClock = () => {
            const now = new Date();

            gmtClock.textContent = `${formatter.format(now)} GMT`;
            gmtClock.dateTime = now.toISOString();
        };

        updateGmtClock();
        window.setInterval(updateGmtClock, 1000);
    }
    const updateTotal = () => {
        let total = 0;
        document.querySelectorAll('[data-line]').forEach((line) => {
            const quantity = Number(line.querySelector('[name$="[quantity]"]')?.value || 0);
            const price = Number(line.querySelector('[name$="[unit_price]"]')?.value || 0);
            const subtotal = quantity * price;
            line.querySelector('[data-line-total]').textContent = new Intl.NumberFormat('fr-FR').format(subtotal) + ' XOF';
            total += subtotal;
        });
        const output = document.querySelector('[data-document-total]');
        if (output) output.textContent = new Intl.NumberFormat('fr-FR').format(total) + ' XOF';
    };
    const bindLine = (line) => {
        line.querySelectorAll('input').forEach((input) => input.addEventListener('input', updateTotal));
        line.querySelector('[data-remove-line]')?.addEventListener('click', () => {
            if (document.querySelectorAll('[data-line]').length > 1) { line.remove(); updateTotal(); }
        });
    };
    lines?.querySelectorAll('[data-line]').forEach(bindLine);
    addLine?.addEventListener('click', () => {
        const clone = document.querySelector('#sale-line-template').content.cloneNode(true);
        const index = lines.querySelectorAll('[data-line]').length;
        clone.querySelectorAll('[name]').forEach((input) => { input.name = input.name.replace('__INDEX__', index); });
        const line = clone.querySelector('[data-line]');
        lines.append(clone);
        bindLine(line);
    });
    updateTotal();
});
