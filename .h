# 1. Cegah akses melihat daftar file dalam folder (Directory Browsing)
Options -Indexes

# 2. Proteksi dari SQL Injection dasar & Karakter Berbahaya
RewriteEngine On

# Blokir karakter yang sering digunakan untuk SQL Injection di URL
RewriteCond %{QUERY_STRING} [concat|union|select|insert|update|delete|drop|truncate] [NC,OR]
RewriteCond %{QUERY_STRING} (.*|'|"|;|<|>|--) [NC]
RewriteRule ^(.*)$ - [F,L]

# 3. Proteksi akses langsung ke file konfigurasi
<Files "config/database.php">
    Order Allow,Deny
    Deny from all
</Files>

# 4. Batasi ukuran upload (Opsional, sesuaikan dengan PHP.ini)
LimitRequestBody 2097152