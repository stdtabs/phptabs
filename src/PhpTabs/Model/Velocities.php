<?php

namespace PhpTabs\Model;

abstract class Velocities
{
  const MIN_VELOCITY = 15;
  const VELOCITY_INCREMENT = 16;
  const PIANO_PIANISSIMO = 15;
  const PIANISSIMO = 31;
  const PIANO = 47;
  const MEZZO_PIANO = 63;
  const MEZZO_FORTE = 79;
  const FORTE = 95;
  const FORTISSIMO = 111;
  const FORTE_FORTISSIMO = 127;
  const _DEFAULT = 95; // FORTE
}
