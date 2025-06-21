# Projekt: TeamFlow - System Zarządzania Projektami

## 1. Wprowadzenie

**TeamFlow** to aplikacja webowa stworzona w oparciu o framework Laravel, przeznaczona do zarządzania projektami i zadaniami.
System został zaprojektowany z myślą o małych zespołach oraz użytkownikach indywidualnych, 
aby usprawnić organizację pracy, ułatwić współpracę oraz umożliwić przejrzyste śledzenie postępów w realizacji zadań i całych projektów. 
Aplikacja demonstruje implementację kluczowych wzorców projektowych, mechanizmów frameworka Laravel oraz dobrych praktyk programistycznych.

## 2. Główne Funkcjonalności

Aplikacja oferuje funkcjonalności, które składają się na kompletne narzędzie do zarządzania projektami.

* **System Uwierzytelniania:**
    * Bezpieczna rejestracja i logowanie użytkowników z hashowaniem haseł.
    * System autoryzacji chroniący dostęp do zasobów (z wykorzystaniem Laravel Policies).

* **Zarządzanie Projektami (CRUD):**
    * Tworzenie, odczytywanie, aktualizacja i usuwanie (miękkie) projektów.
    * Każdy projekt ma jednego właściciela.

* **Zarządzanie Zadaniami (CRUD):**
    * Tworzenie, odczytywanie, aktualizacja i usuwanie (miękkie) zadań w kontekście konkretnego projektu (relacja jeden-do-wielu).
    * Możliwość definiowania statusu i priorytetu dla każdego zadania przy użyciu typów wyliczeniowych (Enum).

* **Zarządzanie Zespołem i Rolami:**
    * Implementacja relacji wiele-do-wielu między użytkownikami a projektami.
    * Możliwość dodawania i usuwania członków zespołu z projektu.
    * System ról (Właściciel, Edytor, Członek) z uprawnieniami zaimplementowanymi przy użyciu dedykowanej tabeli `roles` i mechanizmu Laravel Policies.

* **Dodawanie komentarzy do zadań:**
    * Każdy członek projektu posiada możliwość dodania komentarza do konkretnego zadania oraz usunięcia swoich komentarzy 
    * Komentarze wyświetlają się wszystkim członkom projektu

* **Obsługa Plików:**
    * Możliwość przesyłania i dołączania wielu plików (załączników) do konkretnych zadań.
    * Funkcjonalność podglądu miniatur dla plików graficznych oraz bezpiecznego usuwania załączników.

* **Funkcje Dodatkowe:**
    * **Spersonalizowany Dashboard:** Wyświetla podsumowanie kluczowych informacji dla zalogowanego użytkownika (statystyki, ostatnie zadania).
    * **Wyszukiwanie:** Możliwość przeszukiwania listy projektów oraz zadań (wewnątrz projektu i globalnie).
    * **Eksportowanie do CSV:** Możliwość eksportowania swoich zadań do pliku CSV wraz z informacjami na temat statusu obecnych zadań
    * **Reprezentacja wykresowa:** Wyświetlanie ilości zadań podzielonych na konkretne statusy w formie wykresu słupkowego na Dashboard zalogowanego użytkownika

## 3. Użyte Technologie i Narzędzia

* **Backend:** PHP 8.x, Laravel 12.x
* **Frontend:** HTML5, CSS3 (Tailwind CSS), JavaScript (Alpine.js)
* **Baza Danych:** MySQL
* **Środowisko deweloperskie:**
    * Serwer WWW i PHP: Laravel Herd
    * Klient Bazy Danych: DBeaver
    * Edytor Kodu: PhpStorm
    * Menedżer pakietów PHP: Composer
    * Menedżer pakietów JS i bundler: NPM / Vite

## 4. Architektura i Baza Danych

Aplikacja została zbudowana w oparciu o architekturę **Model-View-Controller (MVC)**, co zapewnia separację logiki biznesowej od warstwy prezentacji.

### Schemat Bazy Danych

Baza danych składa się z kluczowych tabel, które modelują logikę aplikacji:

| Nazwa Tabeli | Opis |
| :--- | :--- |
| `users` | Przechowuje dane użytkowników i informacje do logowania. |
| `projects` | Główna tabela projektów, powiązana z właścicielem (`user_id`). |
| `tasks` | Tabela zadań, powiązana z projektami (`project_id`). |
| `roles` | Słownikowa tabela ról (np. Właściciel, Edytor). |
| `project_user` | Tabela pośrednicząca (pivot) dla relacji wiele-do-wielu `projects` i `users`, zawiera `role_id`. |
| `attachments` | Przechowuje informacje o załącznikach do zadań. |
| `comments` |  Przechowuje komentarze do zadań. |

## 5. Instrukcja Instalacji i Uruchomienia

1.  Sklonuj repozytorium projektu: `git clone <adres-repozytorium>`
2.  Przejdź do folderu projektu: `cd teamflow`
3.  Zainstaluj zależności PHP: `composer install`
4.  Skopiuj plik `.env.example` do `.env`: `cp .env.example .env`
5.  Wygeneruj klucz aplikacji: `php artisan key:generate`
6.  W pliku `.env` skonfiguruj połączenie z bazą danych MySQL (np. używając zmiennej `DB_URL`).
7.  Uruchom serwer MySQL i stwórz pustą bazę danych o nazwie podanej w `.env`.
8.  Uruchom migracje i seedery (stworzą tabele i wypełnią tabelę `roles`): `php artisan migrate:fresh --seed`
9.  Stwórz dowiązanie symboliczne dla przechowywania plików: `php artisan storage:link`
10. Zainstaluj zależności frontendu: `npm install`
11. Uruchom serwer deweloperski Vite w osobnym terminalu: `npm run dev`
12. Skonfiguruj lokalną domenę w Laravel Herd (lub użyj `php artisan serve`).
13. Aplikacja jest dostępna pod skonfigurowanym adresem (np. `https://teamflow.test`).

## 6. Podsumowanie

Projekt "TeamFlow" jest kompletną aplikacją webową, która realizuje wszystkie założone cele. W trakcie jego tworzenia wykorzystano kluczowe mechanizmy
frameworka Laravel, takie jak system tras, ORM Eloquent z relacjami, system autentykacji i autoryzacji (Policies), walidację danych, obsługę plików oraz 
nowoczesne narzędzia front-endowe. Architektura oparta na wzorcu MVC oraz relacyjna baza danych zapewniają skalowalność i łatwość w dalszej rozbudowie systemu.
