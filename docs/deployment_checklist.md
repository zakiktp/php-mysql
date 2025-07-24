\# 📜 Changelog — PHP-MySQL Appointment System



All notable changes to this project will be documented here.



---



\## \[v1.0] — 2025-07-23

🎉 Initial public release



\### Added

\- Patient Appointment Form

\- Auto DOB ↔ Age calculator

\- Prefix-based Gender selection

\- 10-digit Mobile validation

\- City and Address dropdown support

\- Patient search and selection modal

\- Export (Excel/PDF) and live clock



\### Email \& Backup

\- PHPMailer integration for confirmation

\- Google Sheet record sync (partially integrated)

\- Daily Google Drive backup logic (to be completed)



---



> Next: v1.1 — OPD Integration + Patient Linking

# ✅ Deployment Checklist

## 🧪 Pre-Deployment
- [ ] Code committed and pushed to `main`
- [ ] All environment variables configured in `.env`
- [ ] `.gitignore` updated to exclude sensitive files
- [ ] Database is backed up
- [ ] Application tested locally

## 🚀 Deployment Steps
- [ ] Pull latest from GitHub
- [ ] Configure Apache/NGINX
- [ ] Set file permissions
- [ ] Start/restart XAMPP services
- [ ] Open browser to verify functionality

## 🔁 Post-Deployment
- [ ] Test critical forms (Appointment, OPD, etc.)
- [ ] Confirm email confirmation works
- [ ] Check logs for errors
- [ ] Push tag for new version


