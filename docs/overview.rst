========
Overview
========


Requirements
============

Support for PHP 7.2+ and 8.0


Install
=======

.. code-block:: console

    composer require stdtabs/phptabs


Supported file formats
======================

PhpTabs currently supports the following file formats:

- GuitarPro 3 (.gp3)
- GuitarPro 4 (.gp4)
- GuitarPro 5 (.gp5)
- MIDI files (.mid, .midi)
- JSON (.json)
- XML (.xml)


Contribution and support
========================

The source code is hosted on github at 
`https://github.com/stdtabs/phptabs <https://github.com/stdtabs/phptabs>`_.

If you have any questions, please 
`open an issue <https://github.com/stdtabs/phptabs/issues>`_.

You want to write another parser, to fix a bug? Please open 
`a pull request <https://github.com/stdtabs/phptabs>`_.

To discuss new features, make feedback or simply to share ideas, you can
contact me on Mastodon at 
`https://cybre.space/@landrok <https://cybre.space/@landrok>`_


Running the test suite
======================

.. code-block:: console

    git clone https://github.com/stdtabs/phptabs.git
    cd phptabs
    composer require phpunit/phpunit
    vendor/bin/phpunit


License
=======

PhpTabs is licensed under 
`LGPL2.1+ <https://github.com/stdtabs/phptabs/blob/master/LICENSE>`_.
