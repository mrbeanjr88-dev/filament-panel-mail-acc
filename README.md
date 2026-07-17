# Email Customs — Laravel 13 + Filament 5 + IMAP

„Vamă pentru email", axat pe **conturi bancare**: trage mailurile din mai multe conturi
IMAP în carantină (HOLD), le **filtrează complex** și clasifică, **extrage date bancare**
(sumă/sold/sens), le verifici manual în dashboard, poți **modifica atașamentele** (PDF etc.),
apoi **aprobi** spre Inbox și/sau **actualizezi soldul contului bancar**.

## Pornire rapidă

Aplicația e deja construită și migrată (SQLite). Pornește:

```bash
php artisan serve
# Dashboard: http://127.0.0.1:8000/admin
# Login:     admin@local  /  EmailCustoms!2026   (schimbă parola!)
```

Sincronizare mailuri:

```bash
php artisan emails:sync           # toate conturile active
php artisan emails:sync 1         # un singur cont
php artisan emails:sync --queue   # pe queue (+ php artisan queue:work)
```

Programat (deja configurat în `routes/console.php`, la 5 min). În producție adaugă în cron:
```
* * * * * cd /cale/proiect && php artisan schedule:run >> /dev/null 2>&1
```

## Stack

- **Laravel 13.8** (cel mai nou, fără advisory-uri de securitate)
- **Filament 5.6** (panou admin, arhitectura nouă `filament/schemas`)
- **webklex/laravel-imap 6.2** — client IMAP în PHP pur (nu necesită `ext-imap`)

## Module în dashboard (grup „Email Customs")

| Secțiune | Ce face |
|---|---|
| **Carantină** | mailurile oprite; filtre complexe (status, categorie, cont email/bancar, regulă, atașamente, interval dată, interval sumă, debit/credit); verifici, gestionezi atașamente, aprobi/respingi/muți, actualizezi contul bancar |
| **Conturi bancare** | CRUD bănci (IBAN, valută, sold); soldul se actualizează din mailuri |
| **Conturi email** | CRUD conturi IMAP + „Testează conexiunea" + „Sync acum" |
| **Reguli de filtrare** | motor de reguli: condiții (expeditor/subiect/corp/regex/atașament/sumă) → acțiuni (asociază cont bancar, categorie, tag, folder, auto-aprobă, auto-respinge); prioritizabile prin drag&drop |
| **Setări** | mod captură (toate/necitite), mutare în Hold, aplicare reguli, extragere date bancare, sanitizare HTML, valute/foldere implicite |

## Flux

1. **Sync** → trage mailurile din INBOX, extrage date bancare, aplică regulile, mută în HOLD.
2. **Dashboard** → verifici în carantină; gestionezi atașamente (download / șterge / înlocuiește PDF / adaugă).
3. **Aprobare** → fără modificări: `move` original în Inbox (păstrează tot); cu modificări: reconstruiește MIME + APPEND, șterge originalul.
4. **Actualizare bancă** → aplică soldul/suma extrasă pe contul bancar asociat.

## Structura codului

```
app/Models/                 EmailAccount, BankAccount, EmailFilterRule, PendingEmail, PendingEmailAttachment, AppSetting
app/Enums/                  PendingEmailStatus, EmailCategory
app/Services/Imap/          ImapClientFactory, EmailSyncService, RuleEngine, BankDataExtractor, EmailApprovalService, MimeBuilder
app/Jobs/                   SyncEmailAccountJob
app/Console/Commands/       SyncPendingEmails (emails:sync)
app/Filament/Resources/     BankAccounts/, EmailAccounts/, EmailFilterRules/, PendingEmails/ (+ RelationManagers, Pages, Schemas, Tables)
app/Filament/Pages/         ManageSettings
app/Filament/Widgets/       CustomsOverview (stats pe dashboard)
config/email-customs.php    disk atașamente, timeout, OAuth
tests/Feature/              PanelSmokeTest (randează toate paginile)
```

## Limitări (onest)

- **IMAP nu oprește mailul ÎNAINTE de Inbox** — îl citește după livrare și-l mută în HOLD.
  Interceptare reală pre-inbox = server-side (Sieve/MTA).
- **Extragerea datelor bancare e euristică** (regex pentru bănci RO + format generic) —
  verificarea umană rămâne necesară. Ajustează în `BankDataExtractor`.
- **Sync-ul și aprobarea ating un server IMAP real** — nu sunt testabile fără un cont
  (testele acoperă tot UI-ul, nu conexiunea IMAP).
- **Gmail/Outlook în producție = OAuth2** (Basic Auth e restricționat). Config + cârlige
  există în `config/email-customs.php`; fluxul token e de implementat.
- Parola IMAP e stocată criptat (`encrypted`); HTML-ul mailurilor e sanitizat la afișare.
