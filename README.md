emmetog/config
==============

Handles configuration files and settings in a neat package.

This package is the heart of many other packages.  The config object is a
dependency injection container which is passed to other objects on their
contruction.  Then that object will be able to get all its settings from the
config object.  This makes it possible to give two different instances of the
same object different config objects and have them behave differently!  Cool huh!

This package contains two different classes:

*   Config *(The real config object)*
*   ConfigForMocking *(A 'fake' config object, useful in unit tests when we want to give errors when unmocked configs are used)*

Installation
------------

This package is easy to install using [Composer](http://getcomposer.org/), to get
started just add it to your composer.json file.

For example:

    "require": {
        "emmetog/config":    "1.0.*",
        "emmetog/cache":     "1.0.*",
    }

This package requires the Emmetog\Cache package, that's why it's also required above.

Usage
-----

Just create the object you want and use it, for example:

    $config = new Emmetog\Config\Config('/project/path/config/', new NullCache());
    
    // Get the 'text_color' config variable from the 'theme.config.php' config file.
    $textColor = $config->getConfiguration('theme', 'text_color');

In the example above the file 'theme.config.php' should have something like this
inside:
    
    <?php
    # File /project/path/config/theme.config.php
    $config['text_color'] = 'red';
    ?>

Miscellaneous
-------------

This package follows the [semantic versioning](http://semver.org/) guidelines.