<?php

namespace App\Message;

class ProfileMessage
{
    const PROFILE_WRONG_SECTION = "Wystąpił błąd z załadowaniem sekcji. Spróbuj ponownie później.";
    const PROFILE_WRONG_PICTURE = "Wystąpił błąd z załadowaniem zdjęcia. Spróbuj wybrać inne.";
    const PROFILE_UNAVAILABLE_PICTURE = "Nie posiadasz tego zdjęcia. Spróbuj wybrać inne.";
    const PROFILE_CACHE_SUCCESSFULLY_CLEARED = "Twoje dane zostały zaktualizowane.";
    const PROFILE_CACHE_ALREADY_CLEARED = "Dane zostały niedawno odświeżone. Spróbuj ponownie za jakiś czas.";
}
