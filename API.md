# REST API SIMANTAP v1

Base URL: `/simantab/api/v1/index.php?route=`. Seluruh respons memakai JSON dan endpoint mutasi membutuhkan header `X-CSRF-Token` dari `auth/login` atau `auth/me`.

## Autentikasi

- `POST auth/login` — `{ "identity": "admin", "password": "..." }`
- `GET auth/me`
- `POST auth/logout`

## Dokumen dan workflow

- `GET documents?q=&status=`
- `POST documents`
- `GET documents/{id}`
- `PATCH documents/{id}`
- `DELETE documents/{id}` (khusus draf)
- `POST documents/{id}/submit`
- `POST approvals/{stepId}/approve`
- `POST approvals/{stepId}/revise` — catatan wajib
- `POST approvals/{stepId}/reject` — catatan wajib

## File dan finalisasi

- `POST documents/{id}/files` — multipart field `file`, maksimum 20 MB
- `POST documents/{id}/pdf`
- `GET files/{id}/download`

## Operasional

- `POST documents/{id}/distribute`
- `POST documents/{id}/dispositions`
- `POST documents/{id}/reports`
- `GET agendas`
- `GET notifications`

Otorisasi selalu diperiksa server-side berdasarkan permission dan lingkup unit pengguna.
