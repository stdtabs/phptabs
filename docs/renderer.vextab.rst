.. _renderer.vextab:

=======================
Render a song as Vextab
=======================

VexTab format is provided by vexflow.com. If you want to know more about
VexTab format, there is a
`good tutorial <http://www.vexflow.com/vextab/tutorial.html>`_.

PhpTabs (>= 0.5.0) can render a track as a VexTab string.


Quick Usage
===========

The following code prints all tabstaves of the first track.

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('mytab.gp4');

    // Render track 0
    echo $song->toVextab();

This example will ouput something like:

.. code-block:: console

    options scale=1 space=16 width=520 tempo=66

    tabstave notation=true time=4/4

    notes | :8 0/5 9/3 7/4 7/3 T7/3 7/4 0/5 5/3


With a bit of VexFlow JS, that renders:

.. figure:: /_static/images/vextab-renderer-ex01.png
    :width: 100%
    :alt: Vextab rendering with PhpTabs
    :align: center

    Ex1: Rendering with defaults

________________________________________________________________________

Customize VexTab options
========================

Some options can be passed to override default VexTab options.

If the value is the same as VexTab defaults, it won’t be printed.

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('mytab.gp4');

    // Available options
    $options = [
      // Renderer options
      'measures_per_stave'  => 1,

      // Global options
      'space'               => 16,        # An integer
      'scale'               => 0.8,       # A float or an integer
      'stave-distance'      => 20,        # An integer
      'width'               => 500,       # An integer, in pixel

      'font-size'           => 12,        # An integer
      'font-face'           => 'times',   # A string
      'font-style'          => 'italic',  # A string

      'tab-stems'           => true,      # A boolean, default: false
      'tab-stem-direction'  => 'down',    # A string up|down, default: up
      'player'              => false,     # A boolean, default: false

      // Tabstaves options
      'notation'            => true,       # A boolean, default: false
      'tablature'           => true,       # A boolean, default: true
    ];

    // Rendering
    echo $song->toAscii($options);


Will ouput something like:

.. code-block:: console

    options scale=0.8 space=16 width=500 tab-stems=true tab-stem-direction=down stave-distance=20 font-size=12 font-face=times font-style=italic tempo=66

    tabstave notation=true time=4/4

    notes | :8 0/5 9/3 7/4 7/3 T7/3 7/4 0/5 5/3


That renders:

.. figure:: /_static/images/vextab-renderer-ex02.png
    :width: 100%
    :alt: Vextab rendering with PhpTabs
    :align: center

    Ex2: Rendering with custom options

Other options (tempo, clef, key, etc…) will be set by the tab object.

________________________________________________________________________

Supported VexTab features
=========================

Global features
---------------

All options rendered as ``options …``.

========================= ============================== ===============
Feature                   Example                        Supported
========================= ============================== ===============
tempo                     tempo=192                      OK        
player                    player=true                    OK        
tab-stems                 tab-stems=true                 OK        
tab-stem-direction        tab-stem-direction=up          OK        
width 	                  width=1024                     OK        
scale                     scale=0.8                      OK        
space 	                  space=16                       OK        
stave-distance	          stave-distance=16              OK        
font-face	              font-face=times                OK        
font-style	              font-style=italic              OK        
font-size 	              font-size=12                   OK        
========================= ============================== ===============

Stave features
--------------

All options rendered as ``tabstave ...``.

========================= ============================== ===============
Feature                   Example                        Supported
========================= ============================== ===============
notation                  notation=true                  OK        
tablature                 tablature=true                 OK        
clef                      clef=treble                    OK        
key                       key=Ab                         @todo     
time 	                  time=4/4                       OK        
tuning                    tuning=eb                      @todo     
========================= ============================== ===============

Measure and beat features
-------------------------

All options rendered as ``notes ...``.

**Bars**

========================= ============================== ===============
Feature                   Notation                        Supported
========================= ============================== ===============
Bar                       |                              OK        
Double Bar                ||                             @todo     
Repeat Begin              =|:                            OK        
Repeat End                =:|                            OK        
Double Repeat             =:\:                            @todo     
End Bar                   =|=                            @todo     
========================= ============================== ===============


**Beats and notes**

========================= ============================== ===============
Feature                   Notation                        Supported
========================= ============================== ===============
Rest Beat                 ##                             OK        
Bend                      b                              OK        
Dead Note                 X                              OK        
Vibrato                   v                              OK        
Harsh Vibrato             V                              @todo     
Hammer-on                 h                              OK        
Pull-off                  p                              OK        
Taps                      t                              OK        
Slide                     s                              OK        
Tied Note                 T                              OK        
Upstroke                  u                              OK        
Downstroke                d                              OK        
Chord Beat                (0/6.2/5.2/4)                  OK        
Tuplets                   ^n^                            OK        
Durations                 w h q 8 16 32 64               OK        
Annotations               $.$                            @todo     
Staccato                  $a./bottom.$                   @todo     
Staccatissimo             $av/bottom.$                   @todo     
Accent                    $a>/bottom.$                   @todo     
Tenuto                    $a-/bottom.$                   @todo     
marcato                   $a^/bottom.$                   @todo     
LH pizzicato              $a+/bottom.$                   @todo     
snap pizzicato            $ao/bottom.$                   @todo     
open note                 $ah/bottom.$                   @todo     
up fermata                $a\@a/bottom.$                  @todo     
down fermata              $a\@u/bottom.$                  @todo     
bow up                    $a|/bottom.$                   @todo     
bow down                  $am/bottom.$                   @todo     
========================= ============================== ===============


**Lyrics**

Lyrics integration still has to be done.


Musical symbols
---------------

========================= ============================== ===============
Feature                   Notation                        Supported
========================= ============================== ===============
Trills                    #tr                            @todo     
Codas                     #coda                          @todo     
Segnos                    #segno                         @todo     
Forte                     #f                             @todo     
========================= ============================== ===============
