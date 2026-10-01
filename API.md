# Wijhan Backend API

REST API for the Wijhan marketing site (contact/quote lead capture, public
content, and an admin API for managing leads).

## Base URL

```
http://localhost:8000/api/v1   (local)
https://api.wijhan.com/api/v1  (production, once deployed)
```

## Response envelope

Every response follows the same shape.

**Success**

```json
{
  "success": true,
  "message": "Your request has been received.",
  "data": { "id": 1, "status": "new" }
}
```

**Validation error** (HTTP 422)

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

**Other errors**

```json
{ "success": false, "message": "Something went wrong." }
```

No response ever includes a stack trace, an internal exception message, a
server file path, or database credentials — `bootstrap/app.php` renders
every `/api/*` exception through this same envelope, and generic 500s are
always reduced to `"Something went wrong."` regardless of `APP_DEBUG`.

## HTTP status codes used

| Status | Meaning |
|---|---|
| 200 | OK |
| 201 | Created (Contact / Quote Request submitted) |
| 401 | Unauthenticated (missing/invalid admin token) |
| 403 | Unauthorized (inactive admin, or a manager attempting an admin-only action) |
| 404 | Resource not found |
| 422 | Validation failed |
| 429 | Too many requests (rate limited) |
| 500 | Unexpected server error |

## Authentication

Admin endpoints use **Laravel Sanctum** bearer tokens (not cookies — the
frontend and API are expected to live on different domains/subdomains).

1. `POST /admin/login` → returns a `token`.
2. Send it on every subsequent admin request: `Authorization: Bearer <token>`.
3. `POST /admin/logout` revokes the token in use.

Public endpoints (`/contact`, `/quote-requests`, `/services`, `/work`,
`/pricing`) require no authentication.

## CORS

Controlled by `FRONTEND_URL` in `.env` (comma-separated list of allowed
origins) — see `config/cors.php`. No origin is hard-coded.

## Locale

Public content endpoints (`/services`, `/pricing`, `/work`) accept an
optional `?locale=en|ar` query parameter (default `en`) and return that
language's copy. `POST /contact` and `POST /quote-requests` accept an
optional `locale` field (`en` or `ar`, default `en`) — it's stored as
metadata on the record and picks which language the client's confirmation
email is sent in. Backend logic is not duplicated per language; the
`locale` value simply selects which pre-translated column/view is used.

---

## Public endpoints

### `POST /contact`

Rate limited: 5 requests/minute per IP (`lead-capture` limiter).

Fields mirror the existing frontend's contact form exactly
(`src/routes/contact.tsx`): the same Project Type / Budget Range options as
the quote form are accepted here too, since the current form submits them.

| Field | Rules |
|---|---|
| `name` | required, string, max 150 |
| `company` | optional, string, max 150 |
| `email` | required, valid email, max 255 |
| `phone` | optional, string, max 30 |
| `subject` | optional, string, max 150 |
| `message` | required, string, max 5000 |
| `project_type` | optional, one of the values below |
| `budget_range` | optional, one of the values below |
| `locale` | optional, `en` or `ar`, default `en` |

**Request**

```json
{
  "name": "John Doe",
  "company": "Example Company",
  "email": "john@example.com",
  "phone": "+201000000000",
  "message": "We need a business platform.",
  "locale": "en"
}
```

**Response** (201)

```json
{
  "success": true,
  "message": "Your request has been received.",
  "data": { "id": 1, "status": "new" }
}
```

### `POST /quote-requests`

Rate limited: 5 requests/minute per IP (`lead-capture` limiter).

| Field | Rules |
|---|---|
| `name` | required, string, max 150 |
| `company` | optional, string, max 150 |
| `email` | required, valid email, max 255 |
| `phone` | optional, string, max 30 |
| `project_type` | required, one of the values below |
| `budget_range` | required, one of the values below |
| `description` | required, string, max 5000 |
| `locale` | optional, `en` or `ar`, default `en` |

**`project_type` accepted values** (exactly as the frontend's `<select>` submits
them — English pages post the English string, Arabic pages post the Arabic
string; both are accepted without duplicating validation logic):

- English: `New digital product`, `Existing product improvement`, `ERP solution`, `Product discovery`, `Design`, `Engineering`, `Other`
- Arabic: `منتج رقمي جديد`, `تحسين منتج قائم`, `حل ERP`, `اكتشاف المنتج`, `التصميم`, `الهندسة`, `أخرى`

**`budget_range` accepted values**:

- English: `Not decided yet`, `Under $10,000`, `$10,000–$25,000`, `$25,000–$50,000`, `$50,000+`
- Arabic: `لم نحددها بعد`, `أقل من 10,000 دولار`, `10,000–25,000 دولار`, `25,000–50,000 دولار`, `أكثر من 50,000 دولار`
- Contact form only (EGP bands): `Under EGP 100,000`, `EGP 100,000 – 250,000`, `EGP 250,000 – 1,000,000`, `EGP 1,000,000+`, `Not sure — I need advice`, and their Arabic equivalents (`أقل من 100,000 جنيه`, `100,000 – 250,000 جنيه`, `250,000 – 1,000,000 جنيه`, `أكثر من 1,000,000 جنيه`, `غير متأكد — أحتاج استشارة`)

**Request**

```json
{
  "name": "John Doe",
  "company": "Example Company",
  "email": "john@example.com",
  "phone": "+201000000000",
  "project_type": "New digital product",
  "budget_range": "Under $10,000",
  "description": "We need a business platform.",
  "locale": "en"
}
```

**Response** (201)

```json
{
  "success": true,
  "message": "Your request has been received.",
  "data": { "id": 1, "status": "new" }
}
```

**Validation error example** (422)

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "project_type": ["The selected project type is invalid."]
  }
}
```

### `GET /services`

Query: `?locale=en|ar` (default `en`).

```json
{
  "success": true,
  "message": "OK",
  "data": [
    {
      "id": 1,
      "number": "01",
      "slug": "custom-software",
      "icon": "code",
      "title": "Custom Software",
      "summary": "Enterprise applications & platforms",
      "capabilities": []
    }
  ]
}
```

### `GET /pricing`

Query: `?locale=en|ar` (default `en`). Returns the three engagement models
(Discovery Sprint, Product Build, Embedded Partnership).

```json
{
  "success": true,
  "message": "OK",
  "data": [
    {
      "id": 1,
      "number": "01",
      "title": "Discovery Sprint",
      "tag": "Fixed scope, fixed price",
      "price": "$1,000",
      "price_note": "fixed price per sprint",
      "summary": "...",
      "includes": ["..."],
      "best_for": "Founders and teams who need clarity before committing to a build."
    }
  ]
}
```

### `GET /work`

Query: `?locale=en|ar` (default `en`). Returns the project categories the
frontend already lists, plus any **published** case studies — currently
none, matching the frontend's own "Selected work is being prepared"
placeholder. No example projects are fabricated.

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "categories": [{ "id": 1, "name": "ERP" }],
    "case_studies": []
  }
}
```

---

## Admin endpoints

All require `Authorization: Bearer <token>` and an **active** account
(`is_active = true`), checked on every request — not just at login.

### `POST /admin/login`

Rate limited: 5 requests/minute per IP+email pair (`admin-login` limiter).

**Request**

```json
{ "email": "admin@wijhan.com", "password": "ChangeMe123!" }
```

**Response** (200)

```json
{
  "success": true,
  "message": "Logged in successfully.",
  "data": {
    "token": "1|abcdef...",
    "user": { "id": 1, "name": "Wijhan Admin", "email": "admin@wijhan.com", "role": "admin", "is_active": true }
  }
}
```

Invalid credentials → 401. Deactivated account → 403.

### `POST /admin/logout`

Revokes the token used on the request. → `{ "success": true, "message": "Logged out successfully." }`

### `GET /admin/me`

Returns the authenticated admin's profile.

### `GET /admin/dashboard`

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "new_contacts": 3,
    "new_quote_requests": 2,
    "open_leads": 5,
    "qualified_leads": 1,
    "won_requests": 0,
    "lost_requests": 0,
    "recent_submissions": [
      { "type": "quote_request", "id": 4, "name": "Jane Roe", "email": "jane@example.com", "status": "new", "submitted_at": "2026-09-17T19:56:00+00:00" }
    ]
  }
}
```

### Contacts

- `GET /admin/contacts` — optional `?status=` and `?locale=` filters, paginated (`?per_page=`, default 15).
- `GET /admin/contacts/{id}`
- `PATCH /admin/contacts/{id}` — body: `{ "status": "contacted" }`. Valid statuses: `new`, `contacted`, `qualified`, `closed`, `spam`.
- `DELETE /admin/contacts/{id}` — **admin role only**; a manager gets 403.

### Quote requests

- `GET /admin/quote-requests` — optional `?status=` and `?locale=` filters, paginated.
- `GET /admin/quote-requests/{id}`
- `PATCH /admin/quote-requests/{id}` — body: `{ "status": "proposal_sent" }`. Valid statuses: `new`, `reviewing`, `contacted`, `qualified`, `proposal_sent`, `won`, `lost`, `spam`.
- `DELETE /admin/quote-requests/{id}` — **admin role only**; a manager gets 403.

---

## Environment configuration

See `.env.example` for the full list. The variables that must be set
before this goes anywhere near production are called out there with
comments — in short: `DB_*`, `MAIL_*` (switched off `log`), `FRONTEND_URL`,
`WIJHAN_TEAM_EMAIL`, and the `*_SEED_*` credentials.

## Rate limiting & anti-spam

- Public `/contact` and `/quote-requests`: 5 requests/minute per IP.
- Admin `/admin/login`: 5 requests/minute per IP+email pair.
- Both are named limiters (`lead-capture`, `admin-login`) registered in
  `AppServiceProvider`, applied as ordinary route middleware — a
  CAPTCHA/Turnstile check can be added to the same middleware group later
  without restructuring anything.
