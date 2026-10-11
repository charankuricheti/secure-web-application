# Secure Contact Directory (HTML, JavaScript, PHP, SQL)

A small classroom example showing how a browser form can send data to PHP and store it in a SQLite database.

## Requirements
- PHP 8.1+ with `pdo_sqlite` enabled (and `mbstring` recommended).
- A modern web browser.
- Git and a GitHub account only if you want to publish the project.

SQLite is a database file, so a separate database server is not required. Do not open `index.html` directly; run it through PHP.

## Run locally
1. Install PHP if it is not already installed. Check with `php -v`.
2. In this project folder, run: `php -S localhost:8000`
3. Open `http://localhost:8000` in your browser.
4. Add a contact and refresh the page. PHP creates `data/contacts.sqlite` automatically.
5. Check required extensions with: `php -m`. Look for `PDO`, `pdo_sqlite`, and preferably `mbstring`.

## Security controls demonstrated
- JavaScript checks input for a better user experience; PHP validates again because browser checks can be bypassed.
- PDO prepared statements separate SQL instructions from user values and help prevent SQL injection.
- JavaScript uses `textContent` rather than inserting database values as HTML, reducing XSS risk.
- Database errors are logged on the server; generic errors are returned to the browser.
- Database file and environment files are excluded from Git using `.gitignore`.

## Important limitations
This is a learning demo, not a production-ready public website. It does not include login, authorization, CSRF protection, rate limiting, HTTPS configuration, or a production deployment setup. Do not store real personal information in it. Deploy only after adding the controls required for your use case.

## Publish to GitHub
Create an empty repository on GitHub, then run these commands from this folder:
```bash
git init
git add .
git commit -m "Add secure PHP contact directory example"
git branch -M main
git remote add origin https://github.com/YOUR-USERNAME/YOUR-REPOSITORY.git
git push -u origin main
```
Replace the URL with your own repository URL. Never commit passwords, API keys, real database files, or personal data.
