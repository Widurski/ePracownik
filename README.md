# ePracownik - Rejestr pracowników

System do zarządzania rejestrem pracowników w firmie XYZ.

## Wymagania
- PHP 8.1+
- Composer
- MariaDB / MySQL
- Node.js (opcjonalnie, do serwera frontendu)

## Instalacja

### 1. Backend (Laravel)

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Baza danych

Utwórz bazę `epracownik` w MariaDB, potem:

```bash
cd backend
php artisan migrate
php artisan db:seed
```

### 3. Sanctum

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 4. Uruchomienie

Backend:
```bash
cd backend
php artisan serve
```

Frontend:
- otworzyć `frontend/index.html` w przeglądarce
- lub użyć Live Server w VS Code

## Konta testowe

Po uruchomieniu seedera dostępne są konta:

| Email | Hasło | Rola |
|-------|-------|------|
| admin@epracownik.pl | admin123 | administrator |
| jan.kowalski@epracownik.pl | pracownik123 | przelozony |
| anna.nowak@epracownik.pl | pracownik123 | pracownik |

## Funkcjonalności

- Rejestracja i logowanie
- Aktywacja konta przez email
- Trzy role: pracownik, przełożony, administrator
- Panel admina z CRUD użytkowników
- Dodawanie godzin pracy przez przełożonego
- Historia przepracowanych godzin
- System komentarzy do dni pracy
- Eksport raportów do CSV
- Walidacja formularzy (regex)
- Zabezpieczenie przed SQL Injection
