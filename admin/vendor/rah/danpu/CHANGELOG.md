Changelog
=========

Version 2.6.0 - 2013/12/16
----

* Added: Rah\Danpu\BaseInterface.
* Added: Support for filtering tables by prefix.
* Added: Option to get configuration values from the Rah\Danpu\Dump class.
* Added: Generated dump file now ends to linefeed.
* Added: Option to disable auto-commit, foreign and unique key checks. These can be used to generated SQL dumps that import faster to InnoDB tables.
* Changed: Writes the DSN to the dump header instead of the old database property and host.
* Changed: Dump setter and Config values inheritance. Workers now require instance of Dump, but Dump can be fed a different Config instance. This makes sure the methods implemented in the setter, Dump, are available in the consumer class. Extending Config class still works as previously, just pass your Config through Dump to the consumer.
* Changed: Rewritten tests.
* Changed: Improved PHPdoc blocks.
* Changed: Adopted full [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) and [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) standards compliance.

Version 2.5.0 - 2013/05/22
----

* Fixed: Database table ignoring.
* Fixed: Importer's decompressor.
* Fixed: Table locking doesn't generate errors if the database doesn't have tables.
* Added: Support for triggers and views.
* Added: Importer supports delimiter keyword.
* Changed: Any SQL error is thrown as exception.

Version 2.4.0 - 2013/05/16
----

* Fixed: Makes sure creating the temporary file was successful.
* Added: Adds completed on line to the dump file.
* Added: Option to only dump table structures and no rows.

Version 2.3.3 - 2013/05/15
----

* Suggest Zlib, but do not require. Zlib isn't required if the file isn't compressed.

Version 2.3.2 - 2013/05/14
----

* Fixed: Define Rah\Danpu\Base::$pdo property as protected.

Version 2.3.1 - 2013/05/14
----

* Fixed: Can pass instance of Rah\Danpu\Config to a worker. Rah\Base now correctly hints Rah\Danpu\Config instead of Rah\Danpu\Dump.

Version 2.3.0 - 2013/05/13
----

* Fixed: Errors in the given examples. The Rah\Danpu\Dump::temp() should be Rah\Danpu\Dump::tmp().
* Added: Rah\Danpu\Config class. Allows creating dump configs by extending.
* Added: Rah\Danpu\Dump now validates the given configuration option names.
* Added: [PHPunit](http://phpunit.de) tests and [Travis](https://travis-ci.org/gocom/danpu).

Version 2.2.0 - 2013/05/13
----

* Added: Database connection details are set using a DSN string. Deprecates Rah\Danpu\Dump::$db and Rah\Danpu\Dump::$host.

Version 2.1.1 - 2013/05/13
----

* Fixed: Catch PDOExcepiton.

Version 2.1.0 - 2013/05/13
----

* Added: Option to ignore database tables.

Version 2.0.1 - 2013/05/11
----

* Fixed: Error in the composer.json.

Version 2.0.0 - 2013/05/11
----

* Initial release.
