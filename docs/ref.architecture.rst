.. _ref.architecture:

============
Architecture
============

In the scheme below, you may see how PhpTabs core is structured.

It may be useful for all PhpTabs users. Nevertheless, this is more for
those who plan to contribute.

.. code-block:: console
     

                          Reader  ------------  Writer
     ------------------          |            |          ------------------
    | Guitar Pro, MIDI |         | Internal   |         | Guitar Pro, MIDI |
    | JSON, serialized | ------> |            | ------> | JSON, serialized |    
    | file or string   |         |            |         | XML, YAML file   |
     ------------------          |            |         | or string        |
                                 | Music                 ------------------
                        Importer |            | 
      -----------------          |            | Renderer
     | PHP array       | ------> |            |          ------------------
     ------------------          | Model      | ------> | VexTab           |
                                 |            |          ------------------
                                 |            |          ------------------
                                 |            | ------> | ASCII            |
                                 |            |          ------------------
                                 |            | 
                                 |            | Exporter
                                 |            |          ------------------
                                 |            | ------> | PHP array        |
                                 |            |          ------------------
                                  ------------ 


Component roles
===============

- **Reader** imports data from :ref:`files <parse.files>` and
  :ref:`strings <parse.strings>` into the internal model
- **Writer** exports data to :ref:`files <export.files>` or
  :ref:`strings <export.variables>`
- **Renderer** exports data to a
  :ref:`human-readable representation <render.songs>`,
- **Exporter** exports data as PHP arrays for caching or various usages
- **Importer** imports data from internal exports (array)

With the :ref:`internal music model <api.phptabs>`, you can easily
convert files from one type to another.
