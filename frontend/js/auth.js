

document.addEventListener('DOMContentLoaded', function () {

    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const login = document.getElementById('login').value.trim();
            const password = document.getElementById('password').value;

            if (!login || !password) {
                pokazKomunikat('Wypełnij wszystkie pola', 'error');
                return;
            }

            try {
                const result = await apiCall('/login', 'POST', { login, password });

                localStorage.setItem('token', result.token);
                localStorage.setItem('user', JSON.stringify(result.user));

                switch (result.user.role) {
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
                } else if (err instanceof TypeError) {
                    pokazKomunikat('Błąd połączenia z serwerem. Upewnij się, że backend działa na ' + API_URL, 'error');
                } else {
                    pokazKomunikat('Błąd logowania: ' + (err.message || err), 'error');
                }
            }
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const dane = {
                first_name: document.getElementById('imie').value.trim(),
                last_name: document.getElementById('nazwisko').value.trim(),
                email: document.getElementById('email').value.trim(),
                phone_number: document.getElementById('telefon').value.trim(),
                password: document.getElementById('password').value,
            };

            if (!dane.first_name || !dane.last_name || !dane.phone_number || !dane.password) {
                pokazKomunikat('Wypełnij wszystkie wymagane pola', 'error');
                return;
            }

            const telRegex = /^[0-9]{9}$/;
            if (!telRegex.test(dane.phone_number)) {
                pokazKomunikat('Numer telefonu musi składać się z 9 cyfr', 'error');
                return;
            }

            if (dane.password.length < 8) {
                pokazKomunikat('Hasło musi mieć minimum 8 znaków', 'error');
                return;
            }

            if (dane.email) {
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (!emailRegex.test(dane.email)) {
                    pokazKomunikat('Nieprawidłowy format email', 'error');
                    return;
                }
            }

            try {
                const result = await apiCall('/register', 'POST', dane);
                let msg = result.message;
                if (result.login) {
                    msg += ' Twój login: ' + result.login;
                }
                pokazKomunikat(msg, 'success');
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
