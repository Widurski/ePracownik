/* global apiCall, pokazKomunikat */

document.addEventListener('DOMContentLoaded', function () {

    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            if (!email || !password) {
                pokazKomunikat('Wypełnij wszystkie pola', 'error');
                return;
            }

            try {
                const result = await apiCall('/login', 'POST', { email, password });

                localStorage.setItem('token', result.token);
                localStorage.setItem('user', JSON.stringify(result.user));

                switch (result.user.rola) {
                    case 'administrator':
                        window.location.href = 'admin.html';
                        break;
                    case 'przelozony':
                        window.location.href = 'przelozony.html';
                        break;
                    case 'pracownik':
                        window.location.href = 'pracownik.html';
                        break;
                }
            } catch (err) {
                if (err.data && err.data.error) {
                    pokazKomunikat(err.data.error, 'error');
                } else {
                    pokazKomunikat('Błąd logowania', 'error');
                }
            }
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const dane = {
                imie: document.getElementById('imie').value.trim(),
                nazwisko: document.getElementById('nazwisko').value.trim(),
                email: document.getElementById('email').value.trim(),
                telefon: document.getElementById('telefon').value.trim(),
                password: document.getElementById('password').value,
            };

            if (!dane.imie || !dane.nazwisko || !dane.email || !dane.telefon || !dane.password) {
                pokazKomunikat('Wypełnij wszystkie pola', 'error');
                return;
            }

            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(dane.email)) {
                pokazKomunikat('Nieprawidłowy format email', 'error');
                return;
            }

            const telRegex = /^[0-9]{9}$/;
            if (!telRegex.test(dane.telefon)) {
                pokazKomunikat('Numer telefonu musi składać się z 9 cyfr', 'error');
                return;
            }

            if (dane.password.length < 8) {
                pokazKomunikat('Hasło musi mieć minimum 8 znaków', 'error');
                return;
            }

            try {
                const result = await apiCall('/register', 'POST', dane);
                pokazKomunikat(result.message, 'success');
                registerForm.reset();
            } catch (err) {
                if (err.data && err.data.errors) {
                    const bledy = Object.values(err.data.errors).flat();
                    bledy.forEach(function (b) { pokazKomunikat(b, 'error'); });
                } else if (err.data && err.data.error) {
                    pokazKomunikat(err.data.error, 'error');
                } else {
                    pokazKomunikat('Błąd rejestracji', 'error');
                }
            }
        });
    }
});
