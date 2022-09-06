# Usage

In your Mautic insert into an email template this shortcode around *some name*
`[some name|vocative]`

- for example `[Karel|vocative]`
- or better example `[{leadfield=firstname}|vocative]`  
  hint: use `CTRL+SHIFT+V` to insert copied text without formatting, also check source code of your email template by
  ![Mautic source code icon](https://raw.githubusercontent.com/mautic/mautic/4.3.1/app/bundles/CoreBundle/Assets/js/libraries/ckeditor/plugins/sourcedialog/icons/sourcedialog.png)
  button for unwanted formatting
- also foreign and non-human names are converted to czech form `[Cassandra|vocative]` = `Cassandro`
  , `[android|vocative]` = `Androide`
- you can use it even in Subject of your email (unlike other shortcodes).
- **always test your email before sending it to real people**

### Aliases

You can also set aliases to be used (and vocalized) instead of the name.

- `[{leadfield=firstname}|vocative(sirius,andromeda,fill your name plase!)]` leading into
    - if `firstname` is male, let's say Roman, the result is `Siriusi`
    - if `firstname` is female, for example Gloria, the result is `Andromedo`
    - if `firstname` is empty, or from white characters only respectively, the result is `Fill your name please!`
- if you omit one of gender-dependent alias, the original name is used
    - `[richard|vocative(,For gentlemen only!)]` = `Richarde`
    - `[monika|vocative(,For gentlemen only!)]` = `For gentlemen only!` (because of the trailing non-character the
      string is untouched)
    - `[  |vocative(Karel,Monika)]` = ``
    - `[  |vocative(Karel,Monika,Batman)]` = `Batmane`

### Dynamic Web Content support

Thanks to [Zdeno Kuzmany](https://github.com/kuzmany/)
the [Dynamic Web Content](https://mautic.org/docs/en/dwc/index.html) is also supported and processed by vocative.

# Install

1. Let it install by `composer require granam/mautic-czech-vocative-bundle`
2. Clear Mautic cache by `./app/console cache:clear` or just delete the `app/cache` dir.
    - note: In some cases, not yet fully understood, the cache is not rebuilt fully automatically.
      In case of fatal error because of expected but missing file in the cache, rebuilt it manually:
        - `./app/console cache:warmup --no-optional-warmers`
3. Log in to your Mautic as an admin, open cogwheel menu in the right top corner and choose *Plugins*
4. Click *Install/Upgrade Plugins*

If everything goes well, you got new plugin *GranamVocative*.

## Compatibility

### Mautic v4.*

- virtually tested with Mautic 4.* up to 4.4

_Unknown, but possible compatibility with lower versions._

## Troubleshooting

If any error happens, first of all, have you **cleared the cache**?

Otherwise, check the logs for what happened:

1. they are placed in app/logs dir in your Mautic, like `/var/www/mautic/app/logs/mautic_prod-2016-02-19.php`
2. or, if they are more fatal or just Mautic does not catch them (error 500), see your web-server logs,
   like `/var/log/apache2/error.log`

# Credits

The plugin has been created thanks to sponsor [svetandroida.cz](https://www.svetandroida.cz/)
and thanks to the author of free czech vocative library [`bigit/vokativ`](https://bitbucket.org/bigit/vokativ.git) Petr
Joachim.

Additional thanks to [vietnamisa.cz](http://www.vietnamisa.cz/) for their help with bug-fixes and improvements.

# Hint for mautic Twig plugin

If you are going to create a Mautic plugin for [Twig](https://twig.symfony.com/doc/2.x/), a good start can
be [mautic-twig-plugin-skeleton](https://github.com/dongilbert/mautic-twig-plugin-skeleton).
