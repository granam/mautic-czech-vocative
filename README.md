# Usage
In your Mautic insert into an email template this shortcode around *some name*
`[some name|vocative]`
- for example `[Karel|vocative]`
- or better example `[{leadfield=firstname}|vocative]`  
hint: use `CTRL+SHIFT+V` to insert copied text without formatting, also check source code of your email template by
![Mautic source code icon](https://github.com/jaroslavtyc/mautic-bundle-skeleton/blob/master/mautic/app/bundles/CoreBundle/Assets/js/libraries/ckeditor/plugins/sourcedialog/icons/sourcedialog.png)
button for unwanted formatting
- also foreign and non-human names are converted to czech form `[Cassandra|vocative]` = `Cassandro`, `[android|vocative]` = `Androide`

### Aliases
You can also set aliases to be used (and vocalized) instead of the name.
- `[{leadfield=firstname}|vocative(sirius,andromeda)]` leading into
    - if `firstname` is male, let's say Roman, the result is `Siriusi`
    - if `firstname` is female, for example Gloria, the result is `Andromedo`
- if you omit one of gender-dependent alias, the original name is used
    - `[richard|vocative(,For gentlemen only!)]` = `Richarde`
    - `[monika|vocative(,For gentlemen only!)]` = `For gentlemen only!` (because of trailing non-character the string is left untouched)

# Install

1. Manually copy the `MauticVocativeBundle` directory into your Mautic `plugins` directory.
 - for example `/var/www/mautic/plugins/MauticVocativeBundle`.
2. Clear Mautic cache by `./app/console cache:clear` or just delete the `app/cache` dir.
3. Log in to your Mautic as an admin, open cogwheel menu in the right top corner and choose *Plugins*
4. Click *Install/Upgrade Plugins*
 - if everything goes well, you got new plugin *FOMVocative*.

## Troubleshooting
 If any error happens, first of all, have you **cleared the cache**?
 
 Otherwise check the logs for what happened:
 
 1. they are placed in app/logs dir in your Mautic, like `/var/www/mautic/app/logs/mautic_prod-2016-02-19.php`
 2. or, if they are more fatal or just Mautic does not catch them (error 500), see your web-server logs, like `/var/log/apache2/error.log`

# Credits
The plugin has been created thanks to sponsor [svetandroida.cz](https://www.svetandroida.cz/)
and thanks to the author of free czech vocative library [`bigit/vokativ`](https://bitbucket.org/bigit/vokativ.git) Petr Joachim.
