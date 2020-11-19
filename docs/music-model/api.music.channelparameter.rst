.. _api.music.channelparameter:

================
ChannelParameter
================

ChannelParameter's parent is :ref:`Channel <api.music.channel>`.

Read channel parameter informations
===================================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get the first channel parameter
    $parameter = $song->getChannel(0)->getParameter(0);

    echo sprintf("
    ChannelParameter
    ----------------

    key: %s
    value: %s
    ",

        $parameter->getKey(),
        $parameter->getValue()
    );

It will ouput something like:

.. code-block:: console

    ChannelParameter
    ----------------

    key: channel-1
    value: 0



------------------------------------------------------------------------

Write channel parameter informations
====================================

For each getter methods, a setter is available.

.. code-block:: php

    $parameter->setKey('channel-10'),
    $parameter->setValue('My value')


------------------------------------------------------------------------

Copy
====

You may copy all attributes from another channel parameter.


.. code-block:: php

    // Copy from another parameter
    $newParameter->copyFrom($parameter);
