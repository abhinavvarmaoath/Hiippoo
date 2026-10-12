# Modernisation notes

Running log for the Hiippoo modernisation effort. Keep this updated as work
progresses so a new session can pick up without re-discovering everything.

## What this is

A 2016 class project (SJSU) called "Hiippoo" — a study-group site: sign up,
create/join a "team", group chat per team, share files per team, a location
page, a couple of embedded games, a contact-us emailer. Single git commit
(2016-04-26, author `abhi <abhinavvarma7565@gmail.com>`), no history to mine
for intent. Plain PHP, procedural, no framework, no build step. ~30
first-party PHP/HTML files at the repo root; everything else (`assets/`,
`js/`, `css/`, `font-awesome/`, `fonts/`) is vendored third-party front-end
libraries, mostly unused.

## Stack as found

- PHP, procedural, mixed `mysql_*` (removed in PHP 7) and `mysqli`/OO-`mysqli` APIs across different files.
- MySQL, schema inferred from the queries — **no `.sql` file exists in the repo.**
- No composer, no npm, no build tool, no `.gitignore` for the app itself (vendored libs bring their own).
- Bootstrap 3 admin dashboard template ("Creative" by GeeksLabs) for the logged-in pages; a separate Bootstrap "stylish-portfolio" template for the public landing page.
- No tests, no CI, no Dockerfile, no deployment scripts of any kind.

## Verified by running it (scratch instance, this session only — not kept)

Installed MariaDB + ran `php -S` locally against PHP 8.3.6 with an inferred
schema and two seeded test users, strictly in the sandbox scratchpad; nothing
from this was committed. Confirmed:
- `login.php` and `signup.php` fatal (`Call to undefined function mysql_connect()`) on PHP 7+. The app as committed cannot run on anything but PHP 5.
- `login.php` sets `$_SESSION['username']` from `$_POST['username']` **before** the password is checked (line 3, unconditional). A session cookie carrying any username is accepted by every "protected" page because those pages only check `isset($_SESSION['username'])`, never re-verify against the DB.
- Pages guarded by the `isset($_SESSION['username'])` check still render their full body even when unauthenticated, because the `header('Location: ...')` redirect is never followed by `exit`/`die`. Confirmed on `home1.php`: an unauthenticated request gets HTTP 302 *and* the full page body in the same response.
- `add_file.php`, `list_files.php`, `get_file.php` have no session check at all — not even the cosmetic one.

## Key risk areas (full detail in the Phase 2 report given in chat)

- Hardcoded DB credentials (`abhi7565` / `Qweasdzxc1`) and two personal Gmail addresses, repeated verbatim in ~10 files. **Flagged to the user as a secret to rotate/remove — see chat.**
- SQL built by string concatenation almost everywhere (`login.php`, `signup.php`, `list_files.php`, others) — classic SQLi, several unauthenticated.
- Unsalted MD5 passwords.
- No output escaping anywhere — stored/reflected XSS throughout (chat messages, team names, profile fields all echoed raw).
- "Join team" has no ownership concept: typing an existing team name joins it outright (`created.php`/`joined.php` run the identical `INSERT INTO team`).
- `get_file.php` is a sequential-ID, unauthenticated file download (IDOR) across all teams.
- Dead/broken links: `videocall.php` (linked from every sidebar, file doesn't exist), `img/favicon.png`, `js/respond.min.js`, and several asset paths on `location.php` that assume a different (vendored, not-present) template tree.
- 717 tracked files total; most of `assets/` (ckeditor, jquery-ui, jquery-file-upload, morris.js) is vendored and **unreferenced by any first-party page** — dead weight. 34 tracked `.DS_Store` files.

## Decisions log

- (none yet — Phase 2 report delivered, awaiting direction)

## Open questions (asked in chat)

See the questions asked alongside the Phase 2 report: is this still deployed/used by real people, is the DB password above still live anywhere (rotate it regardless), what should happen to `videocall.php`, keep PHP or migrate off it, and what hosting/deployment target to design for.

## What's left to do

Nothing implemented yet. Waiting on the user to pick items from the Phase 2
roadmap before any code changes start (Phase 3).
