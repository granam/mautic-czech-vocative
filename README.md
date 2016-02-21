#Install

1. Manually copy the `MauticVocativeBundle` directory into your Mautic `plugins` directory.
- for example `/var/www/mautic/plugins/MauticVocativeBundle`.
2. Clear Mautic cache by `./app/console cache:clear` or just delete the `app/cache` dir.
3. Login into your Mautic as an admin, open cogwheel menu in right top corner and choose *Plugins*
4. Click *Install/Upgrade Plugins*
- if everything goes well, you got new plugin *FirstName*.

#Usage
In your Mautic insert into an email template this shortcode around *some name*
`[some name|vocative]`
- for example `[Karel|vocative]`
- or better example `[{leadfield=firstname}|vocative]`
