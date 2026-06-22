# Deployment Guide — Grafix@Mirror LMS
## Target: https://lms.mirrorageconcepts.com

---

## 1. Server Requirements

- PHP 8.1+ with extensions: PDO, PDO_MySQL, cURL, mbstring, fileinfo, openssl
- MySQL / MariaDB 10.4+
- Apache 2.4+ with mod_rewrite, mod_headers, mod_expires, mod_deflate
- SSL certificate (Let's Encrypt recommended — free)

---

## 2. DNS Setup

In your domain registrar / DNS panel, add:

| Type | Name | Value |
|------|------|-------|
| A    | lms  | YOUR_SERVER_IP |
| CNAME | www.lms | lms.mirrorageconcepts.com |

---

## 3. Upload Files

Upload the entire project to your hosting public directory:

```
/public_html/          ← if lms is the root domain
/public_html/lms/      ← if lms is a subdirectory
```

For `lms.mirrorageconcepts.com` as a subdomain, the document root should point directly to the project folder.

---

## 4. Configure `.env`

Edit `.env` on the server with your production values:

```env
APP_ENV=production
APP_URL=https://lms.mirrorageconcepts.com

DB_HOST=localhost
DB_NAME=your_production_db_name
DB_USER=your_db_user
DB_PASS=your_strong_password
DB_CHARSET=utf8mb4
```

**Never commit `.env` to Git.**

Important:
- Rotate all secrets before go-live if they were ever stored in local files, screenshots, chat logs, or test deployments.
- This includes `OPENAI_API_KEY`, `PAYSTACK_SECRET_KEY`, SMTP credentials, TURN credentials, and any admin bootstrap passwords.
- Do not deploy with test API keys.

---

## 5. Create the Database

```bash
mysql -u root -p
CREATE DATABASE mirror_age_lms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'lms_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON mirror_age_lms.* TO 'lms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Import the schema:
```bash
mysql -u lms_user -p mirror_age_lms < database/mirror_age_lms_schema.sql
```

Run patches in order:
```bash
mysql -u lms_user -p mirror_age_lms < database/migrate_new_tables.sql
mysql -u lms_user -p mirror_age_lms < database/migrate_enrollment_payment_type.sql
mysql -u lms_user -p mirror_age_lms < database/nigeria_geo_patch.sql
mysql -u lms_user -p mirror_age_lms < database/migrate_live_hybrid.sql
```

Then run the PHP data scripts:
```bash
php database/fix_all.php
php database/add_ml_ai.php
php database/add_ml_lessons.php
php database/add_exam_questions_new.php
php database/add_videos_new.php
```

---

## 6. File Permissions

```bash
chmod 755 uploads/
chmod 755 logs/
chmod 644 .htaccess
chmod 600 .env
```

Also confirm these protection files exist after upload:

```bash
ls -la uploads/.htaccess
ls -la logs/.htaccess
```

---

## 7. SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d lms.mirrorageconcepts.com
```

Auto-renewal:
```bash
sudo crontab -e
# Add: 0 3 * * * certbot renew --quiet
```

---

## 8. Apache Virtual Host

```apache
<VirtualHost *:443>
    ServerName lms.mirrorageconcepts.com
    DocumentRoot /var/www/lms

    SSLEngine on
    SSLCertificateFile    /etc/letsencrypt/live/lms.mirrorageconcepts.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/lms.mirrorageconcepts.com/privkey.pem

    <Directory /var/www/lms>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog  /var/log/apache2/lms_error.log
    CustomLog /var/log/apache2/lms_access.log combined
</VirtualHost>

<VirtualHost *:80>
    ServerName lms.mirrorageconcepts.com
    Redirect permanent / https://lms.mirrorageconcepts.com/
</VirtualHost>
```

---

## 9. Paystack Webhook

In your Paystack dashboard:
- Settings → API Keys & Webhooks
- Webhook URL: `https://mirrorageconcepts.com/lms/paystack_webhook.php`
- Callback URL: `https://mirrorageconcepts.com/lms/pay_verify.php`

---

## 10. Post-Deployment Checklist

- [ ] `.env` has production DB credentials
- [ ] `APP_ENV=production` in `.env`
- [ ] All exposed/test secrets have been rotated
- [ ] Paystack keys are live keys, not test keys
- [ ] SSL certificate active (HTTPS works)
- [ ] `uploads/` directory is writable
- [ ] `logs/` directory is writable
- [ ] Database imported and patches run
- [ ] `test-db.php` and `run_patches.php` return `403 Forbidden`
- [ ] Admin account created (`admin_register.php`)
- [ ] Paystack webhook URL set in dashboard
- [ ] Test a payment end-to-end
- [ ] Test certificate download
- [ ] Test AI tutor chat
- [ ] Test live session scheduling
- [ ] Self-hosted STUN/TURN configured for live classroom
- [ ] `display_errors=Off` confirmed in production
- [ ] Session cookies are `HttpOnly`, `SameSite=Lax`, and `Secure` on HTTPS
- [ ] Google Search Console: submit sitemap.xml

---

## 11. Self-Hosted TURN for Native Live Classes

The LMS live classroom is now WebRTC-based and intended to stay inside your platform. For reliable cross-network audio/video and screen sharing, deploy your own TURN server.

Recommended host:
- `turn.mirrorageconcepts.com` pointing to the same VPS or a dedicated media server

DNS:

| Type | Name | Value |
|------|------|-------|
| A    | turn | YOUR_SERVER_IP |

Install Coturn on Ubuntu/Debian:

```bash
sudo apt update
sudo apt install -y coturn
sudo mkdir -p /etc/turnserver
sudo openssl rand -hex 32
```

Create `/etc/turnserver/turnserver.conf`:

```ini
listening-port=3478
tls-listening-port=5349

listening-ip=0.0.0.0
relay-ip=YOUR_SERVER_IP
external-ip=YOUR_SERVER_IP

fingerprint
lt-cred-mech
user=lms-live:replace-with-your-turn-password
realm=turn.mirrorageconcepts.com
total-quota=100
bps-capacity=0
stale-nonce=600
no-cli

cert=/etc/letsencrypt/live/turn.mirrorageconcepts.com/fullchain.pem
pkey=/etc/letsencrypt/live/turn.mirrorageconcepts.com/privkey.pem
```

Allow Coturn to start:

```bash
sudo sed -i "s/#TURNSERVER_ENABLED=0/TURNSERVER_ENABLED=1/" /etc/default/coturn
sudo systemctl enable coturn
sudo systemctl restart coturn
sudo systemctl status coturn
```

Firewall ports:

```bash
sudo ufw allow 3478/tcp
sudo ufw allow 3478/udp
sudo ufw allow 5349/tcp
sudo ufw allow 49152:65535/udp
```

LMS `.env` values:

```env
LIVECLASS_STUN_URLS=stun:turn.mirrorageconcepts.com:3478
LIVECLASS_TURN_URLS=turn:turn.mirrorageconcepts.com:3478?transport=udp,turns:turn.mirrorageconcepts.com:5349?transport=tcp
LIVECLASS_TURN_USERNAME=lms-live
LIVECLASS_TURN_CREDENTIAL=replace-with-your-turn-password
LIVECLASS_FORCE_RELAY=false
```

Notes:
- `LIVECLASS_FORCE_RELAY=true` is useful for testing or for restrictive networks.
- Without TURN, users on different mobile/home/office networks may fail to connect.
- The LMS no longer uses a public Google STUN fallback, so set these values before production classroom use.
- A reusable sample file is included at `config/turnserver.conf.example`.

---

## 12. cPanel Hosting (Alternative)

If using cPanel shared hosting:

1. Create subdomain `lms` pointing to `/public_html/lms/`
2. Upload files via File Manager or FTP
3. Create MySQL database in cPanel → MySQL Databases
4. Import SQL via phpMyAdmin
5. Edit `.env` with cPanel DB credentials
6. Enable SSL via cPanel → SSL/TLS → Let's Encrypt
