// Mobile nav
const hamburger = document.getElementById('hamburger');
const mainNav   = document.getElementById('mainNav');
if (hamburger && mainNav) {
    hamburger.addEventListener('click', () => mainNav.classList.toggle('open'));
}

// Size selector
document.querySelectorAll('.size-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('selected'));
        this.classList.add('selected');
        const input = document.getElementById('selected_size');
        if (input) input.value = this.dataset.size;
    });
});

// Qty controls
function qtyChange(delta, inputId) {
    const input = document.getElementById(inputId || 'qty');
    if (!input) return;
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > 99) val = 99;
    input.value = val;
}

// Auto-dismiss alerts
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => {
        el.style.transition = 'opacity .4s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
    }, 4500);
});

// Client-side validation
function validateForm(id, rules) {
    const form = document.getElementById(id);
    if (!form) return;
    form.addEventListener('submit', e => {
        let valid = true;
        rules.forEach(r => {
            const field = form.querySelector(`[name="${r.name}"]`);
            if (!field) return;
            const errEl = form.querySelector(`[data-err="${r.name}"]`);
            let msg = '';
            if (r.required && !field.value.trim()) msg = `${r.label} is required.`;
            else if (r.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) msg = 'Valid email required.';
            else if (r.min && field.value.length < r.min) msg = `Min ${r.min} characters.`;
            else if (r.match) {
                const other = form.querySelector(`[name="${r.match}"]`);
                if (other && field.value !== other.value) msg = 'Passwords do not match.';
            }
            if (errEl) errEl.textContent = msg;
            field.style.borderColor = msg ? '#dc2626' : '';
            if (msg) valid = false;
        });
        if (!valid) e.preventDefault();
    });
}

validateForm('registerForm', [
    { name: 'fullname', label: 'Full Name', required: true, min: 2 },
    { name: 'email',    label: 'Email',     required: true, email: true },
    { name: 'password', label: 'Password',  required: true, min: 6 },
    { name: 'confirm',  label: 'Confirm Password', required: true, match: 'password' }
]);
validateForm('loginForm', [
    { name: 'email',    label: 'Email',    required: true, email: true },
    { name: 'password', label: 'Password', required: true }
]);
validateForm('checkoutForm', [
    { name: 'ship_name',    label: 'Full Name', required: true },
    { name: 'ship_address', label: 'Address',   required: true },
    { name: 'ship_phone',   label: 'Phone',     required: true }
]);
