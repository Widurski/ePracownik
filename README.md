# ePracownik - Rejestr pracowników

System do zarządzania rejestrem pracowników

## Wymagania
- PHP 8.1+
- Composer
- Node.js (opcjonalnie, do serwera frontendu)

## Zalecane rozszerzenia VS Code

- **Laravel Extra Intellisense** (`amiralizadeh9480.laravel-extra-intellisense`) - autouzupełnianie route'ów, widoków, konfiguracji i innych elementów Laravela
- **Intelephense** (`bmewburn.vscode-intelephense-client`) - analiza statyczna PHP

Aby wygenerowac pliki pomocnicze IDE (eliminuje fałszywe błędy w edytorze):

```bash
cd backend
php artisan ide-helper:generate
php artisan ide-helper:models -N
```

## Funkcjonalności

- Rejestracja i logowanie (login generowany automatycznie z imienia i nazwiska)
- Aktywacja konta przez email (opcjonalnie)
- Trzy role: pracownik, przełożony, administrator
- Panel admina z CRUD użytkowników
- Dodawanie godzin pracy przez przełożonego
- Historia przepracowanych godzin
- System komentarzy do dni pracy
- Eksport raportów do CSV
- Walidacja formularzy (regex)
- Zabezpieczenie przed SQL Injection

## Instalacja

### 1. Backend (Laravel)

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Baza danych

Projekt korzysta domyślnie z SQLite. Po konfiguracji:

```bash
cd backend
php artisan migrate:fresh --seed
```

### 3. Uruchomienie

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

| Login | Hasło | Rola |
|-------|-------|------|
| admin.systemowy | student123 | administrator |
| jan.kowalski | student123 | przelozony |
| anna.nowak | student123 | pracownik |
| piotr.wisniewski | student123 | pracownik |
