# WopiPHPHost

WopiPHPHost is a PHP scripts for work with Office Online Server (Office Web Apps Server).

##Install

1. Place scripts in /wopi/ dir of your web server;
2. Place expample file (test.docx) in your root web;
3. Add to **.htaccess**:

    ```
    <IfModule mod_rewrite.c>
        Options +FollowSymLinks
        RewriteEngine On

        #RewriteCond  %{HTTP_USER_AGENT}  ^MSWAC.* #this string get acceess only OOS server;

        RewriteCond %{REQUEST_URI} ^/wopi/files/(.*)$ [OR]
        RewriteCond %{REQUEST_URI} ^/WOPI/FILES/(.*)$
        RewriteRule ^(.*)$ /wopi/index.php [L]
    </IfModule>
    ```
3. Run http://yourWebServer/wopi/index.php to generate test link.
    First link - test caml to OOS,
    second link - test return file,
    third link - request to server
