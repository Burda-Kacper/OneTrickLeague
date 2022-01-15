<?php

namespace App\Message;

class QuizMessage
{
    const QUIZ_TOO_MANY_STARTED = "Zaczynasz zbyt dużo quizów! Odczekaj proszę trochę przed kolejnym.";
    const QUIZ_TOOK_TOO_LONG = "Quiz trwał zbyt długo i został niezaliczony.";
    const QUIZ_NOT_FOUND = "Aktywny quiz nie został odnaleziony. Prawdopodobnie został już zakończony.";
    const QUIZ_ANSWER_INVALID = "Wystąpił problem z odpowiedzią. Prawdopodobnie quiz jest zepsuty. Proszę, spróbuj rozpocząć nowy.";
    const QUIZ_UNKNOWN_ERROR = "Wystąpił nieokreślony błąd z quizem. Proszę, spróbuj rozpocząć nowy.";
    const QUIZ_SAVED_NOT_FOUND = "Podany link do zapisanego quizu jest błędny lub wygasł. Możesz rozpocząć nowy quiz.";
    const QUIZ_NEW_QUESTION_ERROR = "Wystąpił problem z dodaniem nowego pytania. Sprawdź proszę poprawność danych lub zaczekaj chwilę.";
    const QUIZ_NEW_QUESTION_SUCCESS = "Nowe pytanie zostało dodane. Administrator musi je zaakceptować przed pojawieniem się w Quizie. Dziękujemy za Twój wkład w rozwój OTL!";
}
