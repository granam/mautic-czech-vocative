#Install

1. Manually copy the `MauticVocativeBundle` directory into your Mautic `plugins` directory.
 - for example `/var/www/mautic/plugins/MauticVocativeBundle`.
2. Clear Mautic cache by `./app/console cache:clear` or just delete the `app/cache` dir.
3. Log in to your Mautic as an admin, open cogwheel menu in the right top corner and choose *Plugins*
4. Click *Install/Upgrade Plugins*
 - if everything goes well, you got new plugin *FOMVocative*.

#Usage
In your Mautic insert into an email template this shortcode around *some name*
`[some name|vocative]`
- for example `[Karel|vocative]`
- or better example `[{leadfield=firstname}|vocative]`

#Known issues
If a name with unknown structure is given to convert, the *e* suffix is added.
`[Who am I?|vocative]` = `Who am I?e`
 - on the other hand, also foreign names are converted to czech form
 `[Cassandra|vocative]` = `Cassandro`

# Credits
The plugin has been created thank to sponsor [svetandroida.cz](https://www.svetandroida.cz/)
and the author of czech vocative library [`bigit/vokativ`](https://bitbucket.org/bigit/vokativ.git) Petr Joachim.
