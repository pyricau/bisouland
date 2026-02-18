document.addEventListener('DOMContentLoaded', () => {
    const usernameInput = document.getElementById('username');
    const suggestions = document.getElementById('username-suggestions');
    if (usernameInput && suggestions) {
        usernameInput.addEventListener('input', async function () {
            const q = this.value;
            if (q.length < 2) { suggestions.style.display = 'none'; return; }
            const data = await fetch('/api/v1/usernames?q=' + encodeURIComponent(q)).then(r => r.json());
            if (data.usernames.length === 0) { suggestions.style.display = 'none'; return; }
            suggestions.innerHTML = data.usernames.map(u => `<li>${u}</li>`).join('');
            suggestions.querySelectorAll('li').forEach(li => {
                li.addEventListener('click', () => {
                    usernameInput.value = li.textContent;
                    suggestions.style.display = 'none';
                });
            });
            suggestions.style.display = 'block';
        });

        document.addEventListener('click', (e) => {
            if (!usernameInput.contains(e.target) && !suggestions.contains(e.target)) {
                suggestions.style.display = 'none';
            }
        });
    }

    document.querySelectorAll('form[data-api]').forEach((form) => {
        const resultEl = form.nextElementSibling;
        const expectedStatus = parseInt(form.dataset.expect || '201', 10);

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            resetResult(resultEl);

            const body = {};
            new FormData(form).forEach((value, key) => {
                const field = form.elements[key];
                body[key] = field && field.type === 'number' ? Number(value) : value;
            });

            try {
                const response = await fetch(form.dataset.api, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(body),
                });

                const data = await response.json();

                if (response.status === expectedStatus) {
                    showSuccess(resultEl, data);
                } else {
                    showError(resultEl, data);
                }
            } catch (err) {
                showError(resultEl, {error: 'Network error, please try again'});
            }
        });
    });
});

function showSuccess(resultEl, data) {
    const rows = Object.entries(data)
        .map(([key, value]) => {
            const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            const formatted = typeof value === 'number' ? value.toLocaleString() : value;
            return `<tr><th>${label}</th><td>${formatted}</td></tr>`;
        })
        .join('');
    resultEl.innerHTML = `<table>${rows}</table>`;
    resultEl.className = 'result success';
}

function showError(resultEl, data) {
    resultEl.textContent = data.error || 'An unexpected error occurred';
    resultEl.className = 'result error';
}

function resetResult(resultEl) {
    resultEl.className = 'result';
    resultEl.innerHTML = '';
}
