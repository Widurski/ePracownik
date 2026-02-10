/* exported API_URL, escapeHtml, apiCall, getUser, getToken, isLoggedIn, wyloguj, pokazKomunikat, sprawdzUprawnienia, ustawHeader */

const API_URL = 'http://localhost:8000/api';

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

async function apiCall(endpoint, method, data) {
    method = method || 'GET';
    data = data || null;
    const token = localStorage.getItem('token');

    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    };

    if (token) {
        options.headers['Authorization'] = 'Bearer ' + token;
    }

    if (data && (method === 'POST' || method === 'PUT')) {
        options.body = JSON.stringify(data);
    }

    const response = await fetch(API_URL + endpoint, options);

    if (response.status === 401) {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        if (window.location.pathname.indexOf('/pages/') !== -1) {
            window.location.href = 'login.html';
        } else {
            window.location.href = 'pages/login.html';
        }
        return;
    }

    const result = await response.json();

    if (!response.ok) {
        throw { status: response.status, data: result };
    }

    return result;
}

function getUser() {
    const data = localStorage.getItem('user');
    if (!data) return null;
    try {
        return JSON.parse(data);
    } catch (_e) {
        localStorage.removeItem('user');
        return null;
    }
}

function getToken() {
    return localStorage.getItem('token');
}

function isLoggedIn() {
    return getToken() !== null;
}

function wyloguj() {
    apiCall('/logout', 'POST').catch(function () { });
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = '../index.html';
}

function pokazKomunikat(tekst, typ) {
    typ = typ || 'success';
    const kontener = document.getElementById('komunikaty');
    if (!kontener) return;

    const div = document.createElement('div');
    div.className = 'message message-' + typ;
    div.textContent = tekst;
    kontener.appendChild(div);

    setTimeout(function () { div.remove(); }, 5000);
}

function sprawdzUprawnienia(wymaganaRola) {
    const user = getUser();
    if (!user || !isLoggedIn()) {
        window.location.href = 'login.html';
        return false;
    }
    if (user.rola !== wymaganaRola) {
        window.location.href = '../index.html';
        return false;
    }
    return true;
}

function ustawHeader(user) {
    const info = document.getElementById('user-info');
    if (info) {
        info.textContent = user.imie + ' ' + user.nazwisko + ' (' + user.rola + ')';
    }
}
