# TodoApp — Collaborative Task Manager

A full-stack collaborative task management application built with the latest Laravel ecosystem. Designed for teams — create workspaces, assign todos, track projects, schedule meetings, and stay on top of deadlines through a built-in calendar, all without leaving the browser.

---

## What It Does

- Personal todo management with priorities, due dates, and status tracking
- Team workspaces — create teams, invite members, manage roles
- Project organisation within teams
- Todo assignment to specific team members and projects
- Team meeting scheduling
- Project milestones with progress tracking
- Built-in calendar aggregating all due dates, meetings, and milestones
- In-app and email notifications for key events
- Full admin panel for user and team oversight

---

## Tech Stack

| Layer | Technology | Version |
|---|---|---|
| Language | PHP | 8.4 |
| Framework | Laravel | 13 |
| Reactivity | Livewire | 4 |
| Single-file components | Volt | 1 |
| Admin panel | Filament | 5 |
| CSS framework | Tailwind CSS | 3 |
| Testing | Pest | 4 |
| Static analysis | Larastan | 3 |
| Code formatting | Laravel Pint | 1 |
| Authentication | Laravel Breeze | 2 |
| Dev tooling | Laravel Boost | 2 |
| Database | SQLite (dev) / MySQL (prod) | — |

---

## Quick Start

```bash
git clone <repo>
cd TodoApp
composer install && npm install

cp .env.example .env
php artisan key:generate
php artisan migrate --seed

php artisan app:create-admin

npm run build
php artisan serve
```

---

## Running Tests

```bash
php artisan test --compact
```
