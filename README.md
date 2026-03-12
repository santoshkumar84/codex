# CodeArena (LeetCode/HackerRank-style PHP platform)

A full-stack coding practice application where admins (teachers) create coding problems and students solve them online with server-side judging.

## Folder structure

```
/project
    /admin
    /student
    /api
    /includes
    /editor
    /submissions
    /assets
    /database
    index.php
    login.php
    register.php
```

## Implemented feature map

### Authentication + roles
- Student registration/login/logout
- Admin login
- Password hashing (`password_hash` / `password_verify`)
- Session-based auth and role guards
- CSRF tokens for state-changing forms

### Admin features
- Dashboard analytics (problems, students, submissions, accepted count)
- Add/Edit/Delete problems
- Upload test cases while creating problems
- Add tags to problems
- Manage students
- View all submissions

### Student features
- Browse problem list
- Search by title/description
- Filter by difficulty
- Filter by tag
- Solved/unsolved status badge
- Open full problem statement
- Code editor (CodeMirror)
- Submit code and view history/results
- Leaderboard by solved problems

### Judge/runner
- Language support: `c`, `cpp`, `java`, `python`, `php`
- Compile (where required) then run for each test case
- Verdicts: `Accepted`, `Wrong Answer`, `Runtime Error`, `Time Limit Exceeded`
- Timeout and output-size guard
- Temporary runtime cleanup after execution

## APIs
- `GET /api/problems.php`
- `GET /api/submissions.php`
- `POST /api/submit.php`
- `GET /api/logout.php`

## Setup
1. Import DB schema:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
2. Update DB credentials in `includes/config.php`.
3. Start PHP server:
   ```bash
   php -S 0.0.0.0:8000
   ```
4. Open `http://localhost:8000`.

Default seeded admin:
- Email: `admin@codearena.local`
- Password: `admin123`

## Important note
This project uses system calls to execute user code. For production usage, run execution inside an isolated sandbox/container with strict OS-level limits.

## Download / Export this project
You can generate a downloadable zip archive from the repository root:

```bash
./scripts/package_project.sh
```

The command outputs the path to a zip file in `dist/` (for example: `dist/codearena-YYYYMMDD-HHMMSS.zip`).
