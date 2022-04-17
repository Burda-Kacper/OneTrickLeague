<?php

namespace App\Message;

class ProfileMessage
{
    const PROFILE_WRONG_SECTION = "Wystąpił błąd z załadowaniem sekcji. Spróbuj ponownie później.";
    const PROFILE_WRONG_PICTURE = "Wystąpił błąd z załadowaniem zdjęcia. Spróbuj wybrać inne.";
    const PROFILE_UNAVAILABLE_PICTURE = "Nie posiadasz tego zdjęcia. Spróbuj wybrać inne.";
    const PROFILE_CACHE_SUCCESSFULLY_CLEARED = "Twoje dane zostały zaktualizowane.";
    const PROFILE_CACHE_ALREADY_CLEARED = "Dane zostały niedawno odświeżone. Spróbuj ponownie za jakiś czas.";
    const PROFILE_PASSWORD_OLD_INCORRECT = "Podane przez Ciebie obecne hasło jest nieprawidłowe.";
    const PROFILE_PASSWORD_REPEAT_INCORRECT = "Nowe hasło i powtórzone nowe hasło nie są jednakowe.";
    const PROFILE_PASSWORD_NEW_SHORT = "Nowe hasło jest zbyt krótkie.";
    const PROFILE_PASSWORD_SAVED = "Twoje hasło zostało zmienione.";
    const PROFILE_USER_NOT_FOUND = "Wystąpił błąd podczas odczytywania danych użytkownika.";
    const PROFILE_USER_NOT_PUBLIC = "Ten użytkownik ustawił swój profil jako prywatny.";
}
