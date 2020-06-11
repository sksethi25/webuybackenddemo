# Webuy Backend Demo

Webuy Assignment Backend Demo: Login/Signup Api in PHP Custom Framework.

Installation & Running:

1. Modify Config/.env.php file and change backend url and Frontend Host url.

2. Modify Config/.env.php file to change database name(Create if not exists) and use db.sql to create required tables.

3. Point your server to Public/index.php.You need to have rewrite url configuration for pretty urls.

For Valet:

No Configuration Required.

For Apache:

Options +FollowSymLinks -Indexes
RewriteEngine On

RewriteCond %{HTTP:Authorization} .
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]


For Nginx:

location / {
    try_files $uri $uri/ /index.php?$query_string;
}
