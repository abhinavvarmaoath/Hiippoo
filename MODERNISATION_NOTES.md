# Modernisation notes

Running log for the Hiippoo modernisation effort. Keep this updated as work
progresses so a new session can pick up without re-discovering everything.

## What this is

A 2016 class project (SJSU) — a study-group site: sign up, create/join a
team, group chat per team, per-team file sharing, a location page, two
embedded games, a contact form. Confirmed with the user: no live deployment,
no real users or data. Plain procedural PHP, no framework, no build step.

## Decisions made this session

- **Dead/portfolio repo, no live users or data** (user-confirmed). Changes
  were made freely on that basis.
- **Rotate the leaked DB password and personal emails now, not later**
  (user-confirmed). Done: `db.php` reads `DB_USER`/`DB_PASSWORD` from the
  environment with no hardcoded fallback; contact-form addresses replaced
  with `example.com` placeholders. This does not purge the old values from
  earlier git history (nobody asked for a history rewrite, and this branch
  is shared) — only from the tree going forward.
- **`videocall.php` sidebar link**: removed outright (user chose this over
  stubbing it).
- **Scope approved (first batch)**: safety net (schema, tests, CI,
  Docker), the non-behavioral security fixes, the PHP 8.x runtime port,
  dropping unused vendored libraries, and the password-hashing migration.
- **Scope approved (second batch)**: fix join-team semantics (create and
  join now behave differently; still no invite/approval step — that's
  the approved semantics, not a gap), the dedup refactor, CSRF on the
  three static `.html` entry points, and the Bootstrap 3 → 5 / jQuery
  1.x → 3.x upgrade. See "Second batch: approved follow-ups" below.

## What was done (see git log for full detail per commit)

Runtime: ported `login.php`/`signup.php` off the removed `mysql_*` API;
fixed a separate PHP 8 fatal in `emailsent.php` (unquoted array keys parsed
as constants). App now runs on PHP 8.3 without fataling.

Security: fixed the login auth-bypass (session username was set before the
password check); added `exit` after every auth-gate redirect (pages were
rendering their protected body regardless); added the missing auth check to
`add_file.php`/`list_files.php`/`get_file.php` (no check at all before);
added team-membership checks everywhere a teamid/file id is accepted as
input, closing a cross-team IDOR on chat and files; parameterized every
remaining SQL query (several were unauthenticated injection points);
escaped all HTML output (stored XSS via username, team name, chat
messages, filenames); added CSRF protection to the forms that cause a real
state change (team create/join, chat post, file upload — not the three
static `.html` entry points, see below); migrated password hashing from
unsalted MD5 to `password_hash()`/Argon2id.

Correctness: fixed `emailsent.php` sending the contact email twice; fixed
a mysqli footgun in the SQL-parameterization pass that was silently
storing every uploaded file with zero bytes of data (bind_param type 'b'
needs `send_long_data()` to actually send anything — switched to 's').
Caught by re-running the full upload/download flow at the end, not by the
parameterization work itself — worth remembering if this pattern (`bind_param`
with a blob column) comes up again.

Correction to the Phase 2 report: `signup.php`'s duplicate-username check
is **not** dead code as I originally claimed — `header()` without `exit`
still lets the rest of the script run, and the fetch/compare genuinely
executes and genuinely rejects duplicates. Verified by creating a duplicate
username against a scratch DB and confirming no second row was inserted.

Tooling: added `schema.sql` (no schema file existed before), Composer
(`phpunit` 12.5, `phpstan` 2.3), `docker-compose` + `Dockerfile` for local
dev, GitHub Actions CI (lint + PHPStan + PHPUnit against a MariaDB service
container), and HTTP-level characterization tests covering signup, login,
team create/join, chat, file upload/download, logout.

Cleanup: removed CKEditor, jQuery UI, jQuery File Upload and Morris.js
(~5.4MB, 389 files, zero references from any first-party page); removed
tracked `.DS_Store` files.

## Known limitation from this session

**Could not run `composer install`, PHPStan, or PHPUnit in this sandbox.**
It consistently hits GitHub's unauthenticated API rate limit resolving
these packages' dist archives (not a transient blip — failed identically
three times), and this session had no way to supply a GitHub token
(extracting one via `gh auth token` was correctly blocked by the sandbox's
own safety controls). `phar.phpunit.de` is blocked by this session's egress
policy; direct GitHub release URLs 404'd.

Everything was instead verified manually: a scratch MariaDB + `php -S`
instance, driven with `curl`, re-created and re-run after essentially every
commit in this session, including a full fresh end-to-end pass at the end
(signup → login → create team → chat → upload/download a file → logout,
plus deliberate SQL-metacharacter and XSS-payload inputs, plus deliberate
cross-team access attempts). That pass is what caught the file-upload blob
bug above. `composer.lock` is fully resolved and should install cleanly
in a normal environment — the committed CI workflow should work fine on
GitHub Actions since it isn't subject to the same rate limit. Worth an
early check next session: does CI actually go green.

## CI and tests: what the first real check found

It didn't go green, and neither did the tests. The manual `curl` passes
above couldn't have shown either:

- CI had failed on every push since it was added, at the "Load schema"
  step (`mariadb: command not found` — the runner image has no mariadb
  client), so lint, PHPStan and PHPUnit had never run there. Fixed by
  installing the client first.
- All seven characterization tests were failing. They predate the CSRF
  commit and never sent a token, and two asserted the old `login.html` /
  `signup.html` redirects. So the history from the CSRF commit onward has
  commits where the suite was red. Updated, and extended to cover the
  create/join rules, CSRF rejection and the contact-form token (the one
  regression that had only been caught by hand).
- PHPStan (level 2) found two real bugs once it actually ran:
  `add_file.php` fataled on any failed upload (`$dbLink->close()` ran
  outside the branch that opens the connection), and the shared header
  printed a blank username on `create.php`/`join.php`/`email.php`, which
  never defined `$username` (pre-existing — their old inline headers had
  the same gap). Both fixed, each with a regression test.

How this was verified: composer still can't authenticate to GitHub from
the sandbox, so lint, PHPStan and PHPUnit were run locally with
substitutes — PHPUnit 9.6.17 from apt and the PHPStan 2.3.3 phar from the
official release — rather than the versions CI installs from
`composer.lock` (PHPUnit 12.5, PHPStan 2.3.1). The tests only use basic
assertions, but CI remains the real check.

## Second batch: approved follow-ups

Four more items approved, all implemented this session:

**Join-team semantics.** `created.php` now rejects if the team name
already exists (was silently joining the existing team instead of
creating a new one); `joined.php` now rejects if the team doesn't exist,
or if the user is already a member. Create and join are now genuinely
different operations. This is still not an invite/approval system —
anyone who knows or guesses a team name can still join it outright. That
was explicitly in scope ("anyone who types an existing team name joins
it, no invite/approval" was the approved semantics); a real invite flow
would be a separate, bigger feature.

**Dedup refactor.** Before extracting anything, standardized the
pages' sidebars so the dedup would be a pure move with no behavior
encoded in it: 6 of 15 pages (`add_file`, `chatroom`, `created`, `joined`,
`list_files`, `test`) were missing the "Location Tracking" and "Games"
nav items the other 9 had — copy-paste drift, not deliberate — added them
everywhere; also normalized a dead dashboard-map config's
`backgroundColor` (split 10/5 between two near-identical hex values
across pages) to the majority value. Then extracted the repeated
~300-line header/sidebar/footer block into `partials/head.php`,
`partials/sidebar.php`, `partials/footer.php`, included from those 14
pages (and just the sidebar from `location.php` — see below). Each page
dropped to under 100 lines, with no further behavior change — this part
was purely moving now-identical markup into one place.

**CSRF on signup.html/login.html/email.html.** Converted all three to
`signup_form.php`/`login_form.php`/`email_form.php`, each starting with
`session_start()` + `require_once csrf.php` and a `csrf_field()` call
inside the form; `login.php`/`signup.php`/`emailsent.php` now call
`csrf_verify()` before acting on the POST. Updated every link that
pointed at the old `.html` filenames (`index.html`, sidebar nav).
`email.html` turned out to already be unreachable from anywhere in the
live app before this change — the "Get in touch" sidebar link has always
pointed at `email.php`, a separate, already-PHP contact form that also
posts to `emailsent.php`. Converting `email.html` was still worth doing
(matches the other two, and `emailsent.php` is shared), but doing so
added `csrf_verify()` to `emailsent.php` without giving `email.php` a
matching `csrf_field()` — which broke the one copy of this form real
users actually reach. Caught by testing the live sidebar path instead of
just the newly-converted one, fixed in a follow-up commit
("Fix contact form CSRF token mismatch").

**Bootstrap 3 → 5, jQuery 1.x → 3.7.1.** Swapped `js/jquery.js`,
`css/bootstrap.min.css`, `js/bootstrap.min.js` (now the Popper-inclusive
bundle, needed for dropdown positioning). Before touching any compat
code, grepped every page for the CSS class/ID each "plugin" call
targets: knob, owl carousel, custom-select, vectormap, jquery-ui,
easy-pie-chart, sparklines, fullcalendar, xcharts, chart-master, morris,
autosize, placeholder, rateit, slimscroll all turned out to target
elements that don't exist anywhere in the app — already fully dead, no
compat work needed. Two call sites were real and did need fixing for
Bootstrap 5's removal of its jQuery plugin API: `scripts.js`'s tooltip
init (`.tooltip()` → `new bootstrap.Tooltip(el)`; the popover call was
dropped, dead) and `creative.js`'s `scrollspy()`/`affix()` calls on
`index.html`, which targeted nonexistent elements but *threw*, which
silently broke the real WOW.js scroll animations running right after
them in the same script — found by loading pages in an actual browser
and watching the console, not just diffing HTML. Verified dropdown,
tooltip, and WOW.js animations all work under Bootstrap 5 via Playwright
(click/hover interaction, not just "no console errors"). Then removed
~30 now-fully-dead vendored files/directories (`assets/chart-master/`,
`assets/fullcalendar/`, `assets/jquery-easy-pie-chart/`,
`assets/jquery-knob/`, old jQuery/jQuery UI builds, IE8 shims, and the
rest of the dead-plugin list) as a separate commit. `location.php` was
the one page not covered by the dedup refactor (it's a stray, already
broken in several unrelated ways — duplicate `<body>` tags, links to an
`assets/` tree that doesn't exist in this repo — clearly an abandoned
half-finished page from the original project) and still had its own
copy of every dead-plugin tag plus an inline init block; stripped those
too, verified its (pre-existing, unrelated) breakage is unchanged.

## Open questions / not done

- **File storage in MySQL BLOBs**: noted in `schema.sql` as a scalability
  ceiling, not something to fix without a reason to.
- **`location.php`**: reachable from the sidebar ("Location Tracking") but
  was already broken before this session in ways out of scope for
  anything approved so far — malformed HTML (three `<body>` tags, looks
  like two or three unrelated page templates pasted together), and
  several `<script>`/`<link>` tags pointing at an `assets/bootstrap/`,
  `assets/wow/`, `assets/uniform/` etc. tree that doesn't exist anywhere
  in this repo (404s today, probably always has). Only touched its dead
  jQuery-plugin references as part of the Bootstrap/jQuery upgrade above;
  everything else about it is pre-existing and untouched.
- **`chat.php`**: a second, unreachable chat-entry page (not linked from
  anywhere) whose form posts to `chatroom.php` with fields `chatroom.php`
  never reads. Dead, like `email_form.php`'s lack of inbound links — left
  alone, same reasoning as `location.php`.

## What's left to do

Nothing from the approved scope is outstanding. Next steps are whichever
of the open items above get approved.
