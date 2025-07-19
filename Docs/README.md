
## Lokale hosts-Datei anpassen:
Starte Notepad (Editor) als Administrator.
Öffne die Datei C:\Windows\System32\drivers\etc\hosts. (Wähle "Alle Dateien" im Öffnen-Dialog).
Füge die folgenden Zeilen am Ende der Datei hinzu:

127.0.0.1   ldap.example.org
127.0.0.1   idp.example.org
127.0.0.1   sp.example.org
127.0.0.1   idp.example.com

Speichere die Datei.

## Docker Images bauen und starten:
docker-compose build
docker-compose up

## Login:
- Öffne ein neues Inkognito-Fenster im Browser.
- Access the link "https://sp.example.org:2443" to launch the Shibboleth SAML SP demo
- Click Login
- You are redirected to Shibboleth SAML IdP
- Input Username/Password credential (e.g., winstonhong/winston-passwd) and then Click Login
- You are redirected back to reach Shibboleth SAML SP-protected web page