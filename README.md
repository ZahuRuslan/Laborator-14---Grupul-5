
# Notepad Online — Grupul 5

## Descriere

Notepad Online este o aplicație web care permite crearea, editarea și gestionarea notelor text.

## Tehnologii utilizate

- PHP
- HTML5
- Tailwind CSS
- JavaScript
- Git și GitHub

## Funcționalități

- Crearea notelor text
- Editarea și salvarea notelor
- Listarea notelor existente
- Căutarea notelor după titlu
- Descărcarea notelor în format TXT
- Ștergerea notelor cu confirmare
- Limitarea conținutului la 10.000 de caractere
- Validarea datelor și protecție CSRF
- Tratarea erorilor de citire și scriere

## Structura proiectului

- `index.php` — interfața principală
- `functions.php` — funcții comune
- `save.php` — salvarea și actualizarea notelor
- `delete.php` — ștergerea notelor
- `download.php` — descărcarea fișierelor
- `script.js` — căutare și interacțiuni
- `style.css` — stiluri suplimentare
- `note/` — directorul fișierelor text

## Instalare și pornire

1. Instalați PHP 8 sau o versiune mai nouă.
2. Clonați repository-ul sau descărcați proiectul.
3. Deschideți terminalul în directorul proiectului.
4. Executați:

   `php -S localhost:8000`

5. Deschideți în browser:

   `http://localhost:8000`

Directorul `note` trebuie să permită scrierea de către procesul PHP.

Este necesară extensia PHP `mbstring`. Interfața Tailwind CSS utilizează CDN și necesită conexiune la internet.

## Stocarea datelor

Notele sunt salvate ca fișiere `.txt` în directorul `note`.

## Securitate

Aplicația validează datele, limitează lungimea notelor, folosește tokenuri CSRF și escapează textul afișat în HTML.

Versiunea de laborator nu include autentificare. Nu o publicați pe internet cu date reale fără autentificare și autorizarea accesului la fișiere.
