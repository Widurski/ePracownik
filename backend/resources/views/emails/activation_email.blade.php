<!DOCTYPE html>
<html>
<head>
    <title>Aktywacja Konta</title>
</head>
<body>
    <h1>Witaj {{ $user->first_name }}!</h1>
    <p>Dziękujemy za rejestrację w systemie ePracownik.</p>
    <p>Aby aktywować swoje konto, kliknij w poniższy link:</p>
    <a href="{{ url('/api/activate-account/' . $token) }}">Aktywuj konto</a>
    <br>
    <p>Jeśli to nie Ty zakładałeś konto, zignoruj ten email.</p>
</body>
</html>
