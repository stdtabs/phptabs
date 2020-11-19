.. _api.music.channel:

=======
Channel
=======

Channel's parent is :ref:`PhpTabs <api.phptabs>`.

Read channel informations
=========================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get the first channel
    $channel = $song->getChannel(0);

    echo sprintf("
    Channel
    -------

    id: %s
    name: %s
    balance: %s
    chorus: %s
    bank: %s
    program: %s
    phaser: %s
    reverb: %s
    tremolo: %s
    volume: %s
    is percussion channel: %s
    ",

        $channel->getId(),
        $channel->getName(),
        $channel->getBalance(),
        $channel->getChorus(),
        $channel->getBank(),
        $channel->getProgram(),
        $channel->getPhaser(),
        $channel->getReverb(),
        $channel->getTremolo(),
        $channel->getVolume(),
        // bool
        $channel->isPercussionChannel()
            ? 'true' : 'false'
    );

It will ouput something like:

.. code-block:: console

    Channel
    -------

    id: 1
    name: Clean Guitar 1
    balance: 23
    chorus: 0
    bank: 0
    program: 27
    phaser: 0
    reverb: 0
    tremolo: 0
    volume: 127
    is percussion channel: false


------------------------------------------------------------------------

Write channel informations
==========================

For each getter methods, a setter is available.

.. code-block:: php

    $channel->setId(1);
    $channel->setName('My channel name');
    $channel->setBalance(12);
    $channel->setChorus(0);
    $channel->setBank(0);
    $channel->setProgram(25);
    $channel->setPhaser(0);
    $channel->setReverb(0);
    $channel->setTremolo(0);
    $channel->setVolume(127);


------------------------------------------------------------------------

Channel parameters
==================

You may handle :ref:`channel parameters <api.music.channelparameter>`.

.. code-block:: php

    // Get the number of parameters
    $count = $channel->countParameters();

    // Get an array of parameters
    $parameters = $channel->getParameters();

    // Get a parameter by its index
    $parameter = $channel->getParameter(0);

    // Push a parameter in the stack
    $channel->addParameter($parameter);

    // Replace a parameter at index 0
    $channel->setParameter(0, $parameter);

    // Remove a parameter at index 0
    $channel->removeParameter(0);


------------------------------------------------------------------------

Copy
====

You may copy all attributes from another channel.


.. code-block:: php

    // Copy from another channel
    $newChannel->copyFrom($channel);
