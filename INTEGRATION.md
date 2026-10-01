# Frontend Integration Guide

How to wire the existing TanStack Start frontend's Contact and Quote Request
forms to this backend, replacing their current fake local-only submit
behavior. This guide does not modify the frontend repository — it describes
the change for someone to apply there (in `src/routes/contact.tsx` and the
`QuoteRequest` component in `src/routes/pricing.tsx`, plus their Arabic
counterparts in `src/components/arabic-pages.tsx`).

## 1. Configure the API base URL

Add to the frontend's environment (e.g. `.env`):

```
VITE_API_URL=http://localhost:8000/api/v1
```

(In production: `https://api.wijhan.com/api/v1`.)

## 2. A small typed client

```ts
// src/lib/api.ts
const API_URL = import.meta.env.VITE_API_URL as string;

export type ApiSuccess<T> = { success: true; message: string; data: T };
export type ApiError = { success: false; message: string; errors?: Record<string, string[]> };
export type ApiResponse<T> = ApiSuccess<T> | ApiError;

export async function apiPost<T>(path: string, body: unknown): Promise<ApiResponse<T>> {
  const response = await fetch(`${API_URL}${path}`, {
    method: "POST",
    headers: { "Content-Type": "application/json", Accept: "application/json" },
    body: JSON.stringify(body),
  });

  return (await response.json()) as ApiResponse<T>;
}
```

## 3. Replace the contact form's fake submit

Current code (`src/routes/contact.tsx`):

```tsx
function submit(event: FormEvent<HTMLFormElement>) {
  event.preventDefault();
  setSubmitted(true);
}
```

Replace with a real submission, keeping the same `submitted` UI state plus
loading/error states:

```tsx
import { apiPost } from "@/lib/api";

function ContactPage() {
  const [submitted, setSubmitted] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function submit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setSubmitting(true);
    setError(null);

    const form = new FormData(event.currentTarget);

    const result = await apiPost("/contact", {
      name: form.get("name"),
      company: form.get("company") || undefined,
      email: form.get("email"),
      phone: form.get("phone") || undefined,
      project_type: form.get("projectType") || undefined,
      budget_range: form.get("budget") || undefined,
      message: form.get("description"),
      locale: "en", // "ar" on the Arabic route
    });

    setSubmitting(false);

    if (!result.success) {
      setError(result.message);
      return;
    }

    setSubmitted(true);
  }

  // ...
}
```

Validation handling: on a 422 response, `result.errors` is a
`{ field: string[] }` map — the same shape Laravel always returns. Example
of surfacing field-level errors instead of (or in addition to) the generic
`error` message:

```tsx
if (!result.success) {
  if (result.errors) {
    // e.g. setFieldErrors(result.errors) and render per-field messages
  } else {
    setError(result.message);
  }
  return;
}
```

Loading state: disable the submit button while `submitting` is true —

```tsx
<Button size="lg" type="submit" disabled={submitting}>
  {submitting ? "Sending..." : "Start a Conversation"}
</Button>
```

Update the success panel's copy — it currently says "Form delivery is not
connected yet, so no message was sent"; once wired up, that line should be
removed (or replaced with something like "We'll be in touch shortly").

## 4. Replace the quote request form's fake submit

Identical pattern in the `QuoteRequest` component (`src/routes/pricing.tsx`),
posting to `/quote-requests` instead, with `description` (not `message`) as
the field name and `project_type`/`budget_range` both required:

```tsx
const result = await apiPost("/quote-requests", {
  name: form.get("name"),
  company: form.get("company") || undefined,
  email: form.get("email"),
  phone: form.get("phone") || undefined,
  project_type: form.get("projectType"),
  budget_range: form.get("budget"),
  description: form.get("description"),
  locale: "en", // "ar" on the Arabic route
});
```

## 5. Arabic routes

`src/components/arabic-pages.tsx`'s `ArabicContactPage` and
`ArabicPricingPage` follow the exact same pattern — the only difference is
`locale: "ar"` and that the `<select>` options there already post the
Arabic option strings, which the backend accepts natively (see `API.md`).

## 6. CORS

The backend's `FRONTEND_URL` env var must include whatever origin the
frontend is served from (e.g. `http://localhost:3000` in dev,
`https://wijhan.com` in production) or the browser will block the request.

## 7. What NOT to change

- Do not add a new client-side validation library — the backend now owns
  validation; keep the existing native HTML `required`/`type="email"`
  attributes as a first UX pass, nothing more.
- Do not remove the `id="quote"` anchor or existing layout/markup — only
  the `submit` handler and the success-panel copy change.
