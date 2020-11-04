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


Contribution and Support
========================

If you have any questions, please `open an issue<{{ site.github_repository_url }}/issues>`_.

You want to write another parser, to fix a bug? Please open `a pull request<{{ site.github_repository_url }}>`_.

To discuss new features, make feedback or simply to share ideas, you can
contact me on Mastodon at `https://cybre.space/@landrok <https://cybre.space/@landrok>`_


Running the test suite
======================

.. code-block:: console

    git clone https://github.com/stdtabs/phptabs.git
    cd phptabs
    composer require phpunit/phpunit
    vendor/bin/phpunit


License
=======

PhpTabs is licensed under `LGPL2.1+<{{ site.github_repository_url }}/blob/master/LICENSE>`_.
