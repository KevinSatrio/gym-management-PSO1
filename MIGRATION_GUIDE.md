# FitManager — Migration & Integration Guide

## Overview

This document describes the modernization changes made to the Gym Management System (now branded **FitManager**). The application remains PHP-native and is fully backward-compatible with the existing Azure deployment pipeline.

---

## Pre-requisites

- PHP 8.0+ (uses `match` expression)
- MySQL 8.0+ (uses `IF NOT EXISTS` in `ALTER TABLE`)
- Existing `loginsystem` database with original tables intact

---

## Step 1 — Run Database Migration

Execute the migration script against your `loginsystem` database **before** deploying the updated code:

```bash
mysql -u root -p loginsystem < migration.sql
```

This migration is **additive only** — it does not drop, rename, or modify any existing columns. It:

1. Creates the `MembershipProgram` table (with indexes)
2. Adds the missing `customer_name` column to the `Payment` table
3. Adds a `created_at` timestamp to the `doctorapp` (Members) table
4. Inserts 5 sample membership records for demonstration

---

## Step 2 — Deploy Updated Code

Deploy all files to your web server. The deployment is compatible with your existing Docker/Azure pipeline — no Dockerfile or CI/CD changes are needed.

### New Files Added

| File | Purpose |
|------|---------|
| `db.php` | Centralized database connection with prepared statement helpers |
| `layout.php` | Shared sidebar + navbar layout template |
| `dashboard.php` | Admin dashboard with summary statistics |
| `membership.php` | Membership Programs list (search/filter/sort) |
| `membership_create.php` | Create new membership form |
| `membership_edit.php` | Edit existing membership form |
| `membership_delete.php` | Soft-delete membership handler |
| `migration.sql` | Database migration script |
| `MIGRATION_GUIDE.md` | This document |

### Modified Files

| File | Changes |
|------|---------|
| `style.css` | Complete redesign — modern design system with CSS variables |
| `index.php` | Modernized login page with glassmorphic card |
| `func.php` | Added session management, flash messages, fixed payment bug |
| `admin-panel.php` | Modernized UI, uses new layout template |
| `package.php` | Modernized UI, fixed title typos |
| `payment.php` | Modernized UI, split into table + form |
| `trainer.php` | Modernized UI, fixed title, split layout |
| `trainer_details.php` | Modernized UI, renamed to "Member Directory" |
| `trainer_search.php` | Modernized UI, fixed SQL injection with prepared statements |

### Untouched Files (no changes)

- `Dockerfile`, `docker-compose.yml` — deployment infrastructure
- `.github/workflows/*` — CI/CD pipelines
- `composer.json`, `phpunit.xml`, `.phpcs.xml` — dev tooling
- `includes/*` — secondary auth system (unused)
- `register.php`, `signup.php`, `header.php` — secondary auth pages
- `images/*`, `includes/images/*` — static assets
- `loginsystem.sql` — original schema (kept for reference)
- `tests/*` — test suite

---

## Step 3 — Verify

1. **Login**: Navigate to `index.php`, log in with admin credentials
2. **Dashboard**: Verify you land on the dashboard with correct stats
3. **All Pages**: Click through sidebar links — Members, Memberships, Trainers, Packages, Payments
4. **Membership CRUD**: Create, edit, and delete a membership
5. **Search**: Test member search from the Member Directory
6. **Mobile**: Resize browser to verify sidebar collapse
7. **Logout**: Click "Sign Out" and verify session is cleared

---

## Architecture Notes

### Authentication Flow
- Login form on `index.php` posts to `func.php`
- On success, `$_SESSION['admin_logged_in'] = true` is set
- `layout.php` checks this session variable — redirects to login if missing
- Logout via `index.php?logout=1` destroys the session

### Database Access Patterns
- **New code** (dashboard, membership CRUD, search) uses `db.php` with prepared statements
- **Legacy code** (member/trainer/payment/package data functions) still uses `func.php` with `$con` global — kept for backward compatibility

### Soft Delete
- `MembershipProgram.deleted_at` is `NULL` for active records
- Deleting sets `deleted_at = NOW()`
- All queries filter `WHERE deleted_at IS NULL`
- Records are never physically deleted

---

## Rollback Procedure

If you need to revert:

1. Restore original PHP files from Git (the migration doesn't break old code)
2. The `MembershipProgram` table and new columns can be dropped:
   ```sql
   DROP TABLE IF EXISTS MembershipProgram;
   ALTER TABLE Payment DROP COLUMN IF EXISTS customer_name;
   ALTER TABLE doctorapp DROP COLUMN IF EXISTS created_at;
   ```
3. Redeploy the original code
