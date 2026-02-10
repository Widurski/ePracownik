/* global apiCall, escapeHtml, pokazKomunikat, getToken, API_URL */
/* exported ladujUzytkownikow, dodajUzytkownika, edytujUzytkownika, zapiszEdycje, usunUzytkownika,
   ladujRaportyAdmin, eksportCSV, otworzModalDodaj, zamknijModal,
   ladujZespol, dodajGodziny, pokazGodzinyPracownika, dodajKomentarzPrzelozony, ladujStatystykiPrzelozony,
   ladujPodsumowanie, ladujHistoriePracownik, dodajKomentarzPracownik */

// ======== ADMIN ========
async function ladujUzytkownikow() {
    try {
        const data = await apiCall('/admin/uzytkownicy');
        const tbody = document.getElementById('tabela-uzytkownikow');
        if (!tbody) return;

        tbody.innerHTML = '';

        data.forEach(function (u) {
            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td>' + escapeHtml(u.id) + '</td>' +
                '<td>' + escapeHtml(u.imie) + ' ' + escapeHtml(u.nazwisko) + '</td>' +
                '<td>' + escapeHtml(u.email) + '</td>' +
                '<td>' + escapeHtml(u.telefon) + '</td>' +
                '<td>' + escapeHtml(u.role ? u.role.nazwa : '') + '</td>' +
                '<td>' + (u.is_active ? 'Tak' : 'Nie') + '</td>' +
                '<td class="actions">' +
                '<button class="btn btn-warning btn-sm" onclick="edytujUzytkownika(' + u.id + ')">Edytuj</button> ' +
                '<button class="btn btn-danger btn-sm" onclick="usunUzytkownika(' + u.id + ')">Usuń</button>' +
                '</td>';
            tbody.appendChild(tr);
        });
    } catch (_err) {
        pokazKomunikat('Błąd ładowania użytkowników', 'error');
    }
}

async function dodajUzytkownika() {
    const dane = {
        imie: document.getElementById('modal-imie').value.trim(),
        nazwisko: document.getElementById('modal-nazwisko').value.trim(),
        email: document.getElementById('modal-email').value.trim(),
        telefon: document.getElementById('modal-telefon').value.trim(),
        password: document.getElementById('modal-password').value,
        role_id: parseInt(document.getElementById('modal-rola').value),
    };

    if (!dane.imie || !dane.nazwisko || !dane.email || !dane.telefon || !dane.password) {
        pokazKomunikat('Wypełnij wszystkie pola', 'error');
        return;
    }

    try {
        await apiCall('/admin/uzytkownicy', 'POST', dane);
        pokazKomunikat('Użytkownik dodany', 'success');
        zamknijModal();
        ladujUzytkownikow();
    } catch (err) {
        if (err.data && err.data.errors) {
            Object.values(err.data.errors).flat().forEach(function (b) {
                pokazKomunikat(b, 'error');
            });
        } else if (err.data && err.data.error) {
            pokazKomunikat(err.data.error, 'error');
        } else {
            pokazKomunikat('Błąd dodawania', 'error');
        }
    }
}

async function edytujUzytkownika(id) {
    try {
        const user = await apiCall('/admin/uzytkownicy/' + id);
        document.getElementById('modal-title').textContent = 'Edytuj użytkownika';
        document.getElementById('modal-imie').value = user.imie || '';
        document.getElementById('modal-nazwisko').value = user.nazwisko || '';
        document.getElementById('modal-email').value = user.email || '';
        document.getElementById('modal-telefon').value = user.telefon || '';
        document.getElementById('modal-password').value = '';
        document.getElementById('modal-rola').value = user.role_id || '1';
        document.getElementById('modal-submit').textContent = 'Zapisz';
        document.getElementById('modal-submit').onclick = function () { zapiszEdycje(id); };
        document.getElementById('modal-overlay').classList.add('active');
    } catch (_err) {
        pokazKomunikat('Błąd ładowania danych użytkownika', 'error');
    }
}

async function zapiszEdycje(id) {
    const dane = {
        imie: document.getElementById('modal-imie').value.trim(),
        nazwisko: document.getElementById('modal-nazwisko').value.trim(),
        email: document.getElementById('modal-email').value.trim(),
        telefon: document.getElementById('modal-telefon').value.trim(),
        role_id: parseInt(document.getElementById('modal-rola').value),
    };

    const haslo = document.getElementById('modal-password').value;
    if (haslo) dane.password = haslo;

    try {
        await apiCall('/admin/uzytkownicy/' + id, 'PUT', dane);
        pokazKomunikat('Użytkownik zaktualizowany', 'success');
        zamknijModal();
        ladujUzytkownikow();
    } catch (err) {
        if (err.data && err.data.errors) {
            Object.values(err.data.errors).flat().forEach(function (b) {
                pokazKomunikat(b, 'error');
            });
        } else {
            pokazKomunikat('Błąd edycji', 'error');
        }
    }
}

async function usunUzytkownika(id) {
    if (!confirm('Na pewno chcesz usunąć tego użytkownika?')) return;

    try {
        await apiCall('/admin/uzytkownicy/' + id, 'DELETE');
        pokazKomunikat('Użytkownik usunięty', 'success');
        ladujUzytkownikow();
    } catch (err) {
        if (err.data && err.data.error) {
            pokazKomunikat(err.data.error, 'error');
        } else {
            pokazKomunikat('Błąd usuwania', 'error');
        }
    }
}

async function ladujRaportyAdmin() {
    const rokEl = document.getElementById('raport-rok');
    const miesiacEl = document.getElementById('raport-miesiac');
    const rok = rokEl ? rokEl.value : new Date().getFullYear();
    const miesiac = miesiacEl ? miesiacEl.value : new Date().getMonth() + 1;

    try {
        const data = await apiCall('/admin/raporty?rok=' + rok + '&miesiac=' + miesiac);

        const statEl = document.getElementById('statystyki-admin');
        if (statEl) {
            statEl.innerHTML =
                '<div class="card"><h3>Użytkownicy</h3><div class="value">' + escapeHtml(data.statystyki.liczba_uzytkownikow) + '</div></div>' +
                '<div class="card"><h3>Pracownicy</h3><div class="value">' + escapeHtml(data.statystyki.liczba_pracownikow) + '</div></div>' +
                '<div class="card"><h3>Godziny w miesiącu</h3><div class="value">' + escapeHtml(data.statystyki.suma_godzin_miesiac) + '</div></div>';
        }

        const tbody = document.getElementById('tabela-raportow');
        if (tbody) {
            tbody.innerHTML = '';
            data.godziny.forEach(function (g) {
                const tr = document.createElement('tr');
                tr.innerHTML =
                    '<td>' + escapeHtml(g.pracownik) + '</td>' +
                    '<td>' + escapeHtml(g.email) + '</td>' +
                    '<td>' + escapeHtml(g.dni_pracy) + '</td>' +
                    '<td>' + escapeHtml(g.suma_godzin) + '</td>';
                tbody.appendChild(tr);
            });
        }
    } catch (_err) {
        pokazKomunikat('Błąd ładowania raportów', 'error');
    }
}

function eksportCSV() {
    const rokEl = document.getElementById('raport-rok');
    const miesiacEl = document.getElementById('raport-miesiac');
    const rok = rokEl ? rokEl.value : new Date().getFullYear();
    const miesiac = miesiacEl ? miesiacEl.value : new Date().getMonth() + 1;

    const token = getToken();
    fetch(API_URL + '/admin/eksport-csv?rok=' + rok + '&miesiac=' + miesiac, {
        headers: { 'Authorization': 'Bearer ' + token }
    })
        .then(function (response) { return response.blob(); })
        .then(function (blob) {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'raport_' + rok + '_' + miesiac + '.csv';
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        })
        .catch(function () {
            pokazKomunikat('Błąd eksportu CSV', 'error');
        });
}

function otworzModalDodaj() {
    document.getElementById('modal-title').textContent = 'Dodaj użytkownika';
    document.getElementById('modal-imie').value = '';
    document.getElementById('modal-nazwisko').value = '';
    document.getElementById('modal-email').value = '';
    document.getElementById('modal-telefon').value = '';
    document.getElementById('modal-password').value = '';
    document.getElementById('modal-rola').value = '1';
    document.getElementById('modal-submit').textContent = 'Dodaj';
    document.getElementById('modal-submit').onclick = dodajUzytkownika;
    document.getElementById('modal-overlay').classList.add('active');
}

function zamknijModal() {
    document.getElementById('modal-overlay').classList.remove('active');
}


// ======== PRZEŁOŻONY ========
async function ladujZespol() {
    try {
        const data = await apiCall('/przelozony/zespol');
        const select = document.getElementById('pracownik-select');
        if (!select) return;

        select.innerHTML = '<option value="">-- wybierz pracownika --</option>';
        data.forEach(function (p) {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = p.imie + ' ' + p.nazwisko;
            select.appendChild(opt);
        });

        const tbody = document.getElementById('tabela-zespolu');
        if (tbody) {
            tbody.innerHTML = '';
            data.forEach(function (p) {
                const tr = document.createElement('tr');
                tr.innerHTML =
                    '<td>' + escapeHtml(p.imie) + ' ' + escapeHtml(p.nazwisko) + '</td>' +
                    '<td>' + escapeHtml(p.email) + '</td>' +
                    '<td>' + escapeHtml(p.telefon) + '</td>' +
                    '<td><button class="btn btn-primary btn-sm" onclick="pokazGodzinyPracownika(' + p.id + ', \'' + escapeHtml(p.imie) + ' ' + escapeHtml(p.nazwisko) + '\')">Historia</button></td>';
                tbody.appendChild(tr);
            });
        }
    } catch (_err) {
        pokazKomunikat('Błąd ładowania zespołu', 'error');
    }
}

async function dodajGodziny() {
    const selectEl = document.getElementById('pracownik-select');
    const dane = {
        user_id: selectEl.value ? parseInt(selectEl.value) : 0,
        data_pracy: document.getElementById('data-pracy').value,
        liczba_godzin: parseFloat(document.getElementById('liczba-godzin').value),
    };

    if (!dane.user_id || !dane.data_pracy || !dane.liczba_godzin) {
        pokazKomunikat('Wypełnij wszystkie pola', 'error');
        return;
    }

    try {
        await apiCall('/przelozony/godziny', 'POST', dane);
        pokazKomunikat('Godziny dodane', 'success');
        document.getElementById('form-godziny').reset();
    } catch (err) {
        if (err.data && err.data.error) {
            pokazKomunikat(err.data.error, 'error');
        } else if (err.data && err.data.errors) {
            Object.values(err.data.errors).flat().forEach(function (b) {
                pokazKomunikat(b, 'error');
            });
        } else {
            pokazKomunikat('Błąd dodawania godzin', 'error');
        }
    }
}

async function pokazGodzinyPracownika(userId, nazwa) {
    try {
        const data = await apiCall('/przelozony/godziny/' + userId);

        const sekcja = document.getElementById('historia-pracownika');
        if (!sekcja) return;

        sekcja.classList.remove('hidden');
        document.getElementById('historia-tytul').textContent = 'Historia godzin: ' + nazwa;

        const tbody = document.getElementById('tabela-historii-przelozony');
        tbody.innerHTML = '';

        data.godziny.forEach(function (g) {
            const tr = document.createElement('tr');
            let komentarzeHtml = '';
            if (g.komentarze && g.komentarze.length > 0) {
                g.komentarze.forEach(function (k) {
                    komentarzeHtml += '<div class="komentarz"><span class="autor">' + escapeHtml(k.autor.imie) + ' ' + escapeHtml(k.autor.nazwisko) + ':</span><div class="tresc">' + escapeHtml(k.tresc) + '</div></div>';
                });
            }
            tr.innerHTML =
                '<td>' + escapeHtml(g.data_pracy ? g.data_pracy.substring(0, 10) : '') + '</td>' +
                '<td>' + escapeHtml(g.liczba_godzin) + '</td>' +
                '<td>' + escapeHtml(g.dodajacy ? g.dodajacy.imie : '') + ' ' + escapeHtml(g.dodajacy ? g.dodajacy.nazwisko : '') + '</td>' +
                '<td>' + komentarzeHtml +
                '<button class="btn btn-sm btn-primary" style="margin-top:5px" onclick="dodajKomentarzPrzelozony(' + g.id + ')">Komentuj</button>' +
                '</td>';
            tbody.appendChild(tr);
        });

        document.getElementById('suma-godzin-przelozony').textContent = 'Suma: ' + data.suma + ' godz.';
    } catch (_err) {
        pokazKomunikat('Błąd ładowania historii', 'error');
    }
}

async function dodajKomentarzPrzelozony(godzinaId) {
    const tresc = prompt('Wpisz komentarz:');
    if (!tresc || tresc.trim() === '') return;

    try {
        await apiCall('/przelozony/komentarze', 'POST', {
            godzina_pracy_id: godzinaId,
            tresc: tresc,
        });
        pokazKomunikat('Komentarz dodany', 'success');
    } catch (_err) {
        pokazKomunikat('Błąd dodawania komentarza', 'error');
    }
}

async function ladujStatystykiPrzelozony() {
    const rokEl = document.getElementById('stat-rok');
    const miesiacEl = document.getElementById('stat-miesiac');
    const rok = rokEl ? rokEl.value : new Date().getFullYear();
    const miesiac = miesiacEl ? miesiacEl.value : new Date().getMonth() + 1;

    try {
        const data = await apiCall('/przelozony/statystyki?rok=' + rok + '&miesiac=' + miesiac);

        const tbody = document.getElementById('tabela-statystyk');
        if (!tbody) return;

        tbody.innerHTML = '';
        data.pracownicy.forEach(function (p) {
            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td>' + escapeHtml(p.imie) + ' ' + escapeHtml(p.nazwisko) + '</td>' +
                '<td>' + escapeHtml(p.dni_pracy) + '</td>' +
                '<td>' + escapeHtml(p.suma_godzin) + '</td>';
            tbody.appendChild(tr);
        });
    } catch (_err) {
        pokazKomunikat('Błąd ładowania statystyk', 'error');
    }
}


// ======== PRACOWNIK ========
async function ladujPodsumowanie() {
    try {
        const data = await apiCall('/pracownik/podsumowanie');

        const el1 = document.getElementById('godziny-miesiac');
        const el2 = document.getElementById('dni-miesiac');
        if (el1) el1.textContent = data.godziny_miesiac;
        if (el2) el2.textContent = data.dni_miesiac;

        const tbody = document.getElementById('tabela-ostatnie');
        if (tbody) {
            tbody.innerHTML = '';
            data.ostatnie_wpisy.forEach(function (g) {
                const tr = document.createElement('tr');
                tr.innerHTML =
                    '<td>' + escapeHtml(g.data_pracy ? g.data_pracy.substring(0, 10) : '') + '</td>' +
                    '<td>' + escapeHtml(g.liczba_godzin) + '</td>' +
                    '<td>' + escapeHtml(g.dodajacy ? g.dodajacy.imie : '') + ' ' + escapeHtml(g.dodajacy ? g.dodajacy.nazwisko : '') + '</td>';
                tbody.appendChild(tr);
            });
        }
    } catch (_err) {
        pokazKomunikat('Błąd ładowania podsumowania', 'error');
    }
}

async function ladujHistoriePracownik() {
    const rokEl = document.getElementById('hist-rok');
    const miesiacEl = document.getElementById('hist-miesiac');
    const rok = rokEl ? rokEl.value : new Date().getFullYear();
    const miesiac = miesiacEl ? miesiacEl.value : new Date().getMonth() + 1;

    try {
        const data = await apiCall('/pracownik/godziny/' + rok + '/' + miesiac);

        const tbody = document.getElementById('tabela-historii');
        if (!tbody) return;

        tbody.innerHTML = '';
        data.godziny.forEach(function (g) {
            let komentarzeHtml = '';
            if (g.komentarze && g.komentarze.length > 0) {
                g.komentarze.forEach(function (k) {
                    komentarzeHtml += '<div class="komentarz"><span class="autor">' + escapeHtml(k.autor.imie) + ' ' + escapeHtml(k.autor.nazwisko) + ':</span><div class="tresc">' + escapeHtml(k.tresc) + '</div></div>';
                });
            }

            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td>' + escapeHtml(g.data_pracy ? g.data_pracy.substring(0, 10) : '') + '</td>' +
                '<td>' + escapeHtml(g.liczba_godzin) + '</td>' +
                '<td>' + escapeHtml(g.dodajacy ? g.dodajacy.imie : '') + ' ' + escapeHtml(g.dodajacy ? g.dodajacy.nazwisko : '') + '</td>' +
                '<td>' + komentarzeHtml +
                '<button class="btn btn-sm btn-primary" style="margin-top:5px" onclick="dodajKomentarzPracownik(' + g.id + ')">Komentuj</button>' +
                '</td>';
            tbody.appendChild(tr);
        });

        document.getElementById('suma-godzin').textContent = 'Suma: ' + data.suma + ' godz.';
    } catch (_err) {
        pokazKomunikat('Błąd ładowania historii', 'error');
    }
}

async function dodajKomentarzPracownik(godzinaId) {
    const tresc = prompt('Wpisz komentarz:');
    if (!tresc || tresc.trim() === '') return;

    try {
        await apiCall('/pracownik/komentarze', 'POST', {
            godzina_pracy_id: godzinaId,
            tresc: tresc,
        });
        pokazKomunikat('Komentarz dodany', 'success');
        ladujHistoriePracownik();
    } catch (err) {
        if (err.data && err.data.error) {
            pokazKomunikat(err.data.error, 'error');
        } else {
            pokazKomunikat('Błąd dodawania komentarza', 'error');
        }
    }
}
