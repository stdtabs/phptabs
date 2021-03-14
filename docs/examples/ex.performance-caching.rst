.. _ex.performance-caching:

=====================
Performance & caching
=====================

There are some cases where it's useful to increase performance.

It largely depends on the context but it may be good to know that
PhpTabs provides some tools for this purpose.

It is often possible to summarize performance issues in 2 types:

- IO struggling
- Software issues

To fix IO issues, we'll try to put in cache (memory) some data.

But, first, let's look at what we will cache.

Context
=======

For this example, we'll take a real-life Guitar Pro file.

Characteristics:

- 6 tracks
- 849 measures (Oh!)
- A little bit less than 1MB

Let's parse it !

.. code-block:: php

    $filename = 'big-file.gp5';

    // Start
    $start = microtime(true);

    // Parse
    $song = new PhpTabs($filename);

    // Stop
    $stop = microtime(true);

    // Display parsing time
    echo round($stop - $start, 2) . 's';

And the result is:

.. code-block:: console

    5.78s

Woh! I don't know what the subject of your app is but we can tell it's
going to be slow.

As usual for performance issues, you have to make choices.  

We have ``6 tracks * 849 measures = 5094 measures``. Do you want to
display all of these in a webpage ? In a mobile app ?

Let's say that we only want to display one track.

PhpTabs provides features
:ref:`to target a single track <target-track-measure>` and to generate
a new file.

Slicing tracks
==============

In this example, starting from the whole file, we'll create 6 files with
only one track in each.

.. code-block:: php

    $filename = 'big-file.gp5';

    // Parse
    $song = new PhpTabs($filename);

    // Generate one file per track
    for ($i = 0; $i < $song->countTracks(); $i++) {
        $song->onlyTrack($i)->save("track-{$i}-{$filename}");
    }


Now, we're going to test parsing for one of these files.


.. code-block:: php

    $filename = 'track-0-big-file.gp5';

    // Start
    $start = microtime(true);

    // Parse
    $song = new PhpTabs($filename);

    // Stop
    $stop = microtime(true);

    // Display parsing time
    echo "\nParsing a track file: " . round($stop - $start, 2) . 's';
    echo "\n" . $song->getName();


.. code-block:: console

    Parsing a track file: 0.52s
    My song title

Ok, that's better. At the end of this script, you may have seen that
we've printed out the song title. Indeed,
:ref:`slicing <slice-tracks-measures>` or
:ref:`targetting <target-track-measure>` a track
does not loose global song informations.

Exporting to JSON
=================

Is it possible to make it faster ?

We're going to make the same thing than before but instead of saving
the track into in a Guitar Pro file, we're going to save it in JSON.

.. code-block:: php

    $filename = 'big-file.gp5';

    // Parse
    $song = new PhpTabs($filename);

    // Generate one JSON file per track
    for ($i = 0; $i < $song->countTracks(); $i++) {
        $song->onlyTrack($i)->save("track-{$i}-{$filename}.json");
    }

Now, we're going to test parsing for one of these files.


.. code-block:: php

    $filename = 'track-0-big-file.gp5.json';

    // Start
    $start = microtime(true);

    // Parse
    $song = new PhpTabs($filename);

    // Stop
    $stop = microtime(true);

    // Display parsing time
    echo "\nParsing a JSON file: " . round($stop - $start, 2) . 's';
    echo "\n" . $song->getName();


.. code-block:: console

    Parsing a JSON file: 0.21s
    My song title

It's good for the moment.

JSON file is bigger than Guitar Pro file. Under the hood, it makes a
``PhpTabs::toArray()`` call, then it converts
it to JSON.

As data is stored in a native
Phptabs export, it makes it faster.

The idea here was to parse the whole song only once and split it into
several files with sliced tracks. 

The new problem is that we have 6 files for tracks.

What about pushing ``toArray()`` results into a cache system ?

------------------------------------------------------------------------

Caching
=======

We're going to take all the work done before in order to keep only the
best parts.

Best parts are:

- Parsing only once the whole song
- Splitting tracks into smaller units for later use

What we're introducing here is:

- Exporting tracks to arrays
- Saving them into cache
- Importing an array into PhpTabs

Importing from an array is blazingly fast. There is no parsing time,
it's like re-importing a part already analyzed previously.

You may have to install Memcache server and client before. Of course,
you may use another caching system.

.. code-block:: php

    use PhpTabs\IOFactory;

    $memcache = new Memcache;
    $memcache->connect('localhost', 11211)
            or die ("Connection failed");

    $filename = 'track-0-big-file.gp5';

    // Parse
    $song = IOFactory::create($filename);

    // Generate one array for this track
    $array = $song->toArray();

    // Put in cache
    $memcache->set($filename, $array);


And now, we may load this track from cache.


.. code-block:: php

    use PhpTabs\IOFactory;

    $memcache = new Memcache;
    $memcache->connect('localhost', 11211)
            or die ("Connection failed");

    $filename = 'track-0-big-file.gp5';

    // Start
    $start = microtime(true);

    // Get from cache
    $song = IOFactory::fromArray(
        $memcache->get($filename)
    );

    $stop = microtime(true);

    // Display loading time
    echo "\nLoading time : " . round($stop - $start, 2) . 's';


.. code-block:: console

    Loading time : 0.13s


It's a quick example on how to tackle some performance issues. You may
not use these scripts without adapting them to your own context.

However, with that in mind, you have an idea of how to successfully
meet production constraints.

If you have any questions or some feedbacks, feel free to open issues
or contribute to this manual.
