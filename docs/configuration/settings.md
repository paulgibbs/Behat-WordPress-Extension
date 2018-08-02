description: WordHat configuration settings
# Settings

This documentaton refers to settings provided by the WordHat extension.

Consult the [Behat website](http://behat.org/en/latest/user_guide/configuration.html) for an overview of general settings, or the [Mink website](http://mink.behat.org/en/latest/) for settings relating to controlling a web browser.


## WordHat

These are the options provided by the WordHat extension:

```YAML
PaulGibbs\WordpressBehatExtension:
  default_driver: wpcli
  path: ~

  # User settings.
  users:
    -
      role: administrator
      username: admin
      password: admin
    -
      role: contributor,superhero
      username: clark
      password: kent

  # WordPress settings.
  site_url: ~
  permalinks:
    author_archive: author/%s/
  database:
    restore_after_test: false
    backup_path: ~

  # Driver settings.
  wpcli:
    alias: dev
    binary: wp
```

Option           | Default value | Description
:----------------| :------------ | :----------
`default_driver` | "wpcli"       | _Optional_.<br>The [WordPress driver](/features/overview.md) to use ("wpcli", "wpphp").
`path`           | null          | _Optional_.<br>Path to WordPress files.
`users.*`        | _see example_ | _Optional_.<br>`roles` property can accept multiple values, comma-seperated.
`site_url`       | null          | _Optional_.<br>If your WordPress is installed in a subdirectory, set this to the `site_url()` value. Defaults to [`mink.base_url`](http://behat.org/en/latest/user_guide/configuration.html#extensions).
`permalinks.*`   | _see example_ | _Optional_.<br>Permalink pattern for the specific kind of link.<br>`%s` is replaced with an ID/object name, as appropriate.
`database.restore_after_test` | false | _Optional_.<br>If <code>true</code>, [WordHat will restore your site's database to its initial state between feature tests](/recipes/content.md).
`database.backup_path` | _see example_ | _Optional_.<br>If <code>restore_after_test</code> is true, and the value is a file path, WordHat will use that as the back up to restore the database from.<br>If the path is a directory, then before any tests are run, WordHat will generate a database back up and store it here.<br>If the path has not been set, WordHat will choose its own temporary folder.
`wpcli.alias`    | null          | _Optional_.<br>[WP-CLI alias](https://wp-cli.org/commands/cli/alias/) (preferred over `wpcli.path`).
`wpcli.binary`   | `wp`          | _Optional_.<br>Path and name of WP-CLI binary.<br>Also supports shell commands (e.g. `cd ~/bin && ./wp`).


## Per-environment settings

Some of the settings in `behat.yml` are environment specific. For example, the `base_url` may be `http://test.example.dev` on your local development environment, while on a test server it might be `http://test.example.com`.

If you intend to run your tests on different environments, these sorts of settings should not be added to your `behat.yml`. Instead, they should be exported in an environment variable.

Before running tests, Behat will check the `BEHAT_PARAMS` environment variable and add these settings to the ones that are present in `behat.yml` (settings from this file takes precedence). This variable should contain a JSON object with your settings.

Example JSON object:

```JavaScript
{
  "extensions": {
    "Behat\\MinkExtension": {
      "base_url": "http://development.dev"
    }
  }
}
```

To export this to the ``BEHAT_PARAMS`` environment variable on a Linux or MacOS system, squash the JSON object into a single line and surround with single quotes:

```Shell
export BEHAT_PARAMS='{"extensions":{"Behat\\MinkExtension":{"base_url":"http://development.dev"}}}'
```
