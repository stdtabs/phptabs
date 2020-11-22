# Changelog


## 1.0.0 - 2020-11-22 ##################################################

A new manual at https://phptabs.readthedocs.io/

This release contains some new features, new shortcuts to ease usages,
fixes in the core and removals.

### New features

#### Slice tracks and measures

- `PhpTabs::onlyTrack()` and `PhpTabs::sliceTracks()`
- `PhpTabs::onlyMeasure()` and `PhpTabs::sliceMeasures()`

See manuals for
[slice methods](https://phptabs.readthedocs.io/en/latest/slice-tracks-measures.html)
and
[only methods](https://phptabs.readthedocs.io/en/latest/target-track-measure.html)


#### Parse from strings

You may load file contents from database or a stream.
This can be done with `IOFactory::fromString()` and
`PhpTabs::fromString()`

See manual for
[fromString](https://phptabs.readthedocs.io/en/latest/parse.strings.html)

#### Parse from arrays

You may need to load contents from a previously parsed song.
This can be done with `IOFactory::fromArray()` and
`PhpTabs::fromArray()`

See manual for
[loading from arrays](https://phptabs.readthedocs.io/en/latest/export.variables.html#php-array)

#### Parse from files now supports JSON and PHP serialized file formats

You can make `IOFactory::fromFile()`or `new PhpTabs()` with more file
formats.

See manual for
[parsing from files](https://phptabs.readthedocs.io/en/latest/parse.files.html)
for all available formats.

#### Saving to files supports new file formats

- `Phptabs::save('filename.json')` will implicitly convert to JSON
- `PhpTabs::save('filename.ser')` will implicitly convert to PHP serialized
- `PhpTabs::save('filename.txt')` will implicitly convert to TEXT
- `PhpTabs::save('filename.xml')` will implicitly convert to XML
- `PhpTabs::save('filename.yml')` will implicitly convert to YAML

See manual for
[saving to files](https://phptabs.readthedocs.io/en/latest/export.files.html)
for all available formats.


#### PhpTabs::save() accepts an optional 2nd argument to force conversion

`Phptabs::save('filename.dat', 'json')` will convert to JSON

#### PhpTabs::convert() accepts new formats

Explicitly converting to YAML, XML, JSON and PHP serialized is now
available.

- `Phptabs::convert('json')`
- `Phptabs::convert('ser')`
- `Phptabs::convert('txt')`
- `Phptabs::convert('text')`
- `Phptabs::convert('xml')`
- `Phptabs::convert('yaml')`
- `Phptabs::convert('yml')`

See manual for
[all available formats](https://phptabs.readthedocs.io/en/latest/export.variables.html#phptabs-convert-format)


#### Rendering shortcuts

- `PhpTabs::toAscii($options)`
- `PhpTabs::toVextab($options)`

See manuals for
[rendering to ASCII](https://phptabs.readthedocs.io/en/latest/renderer.ascii.html)
and
[rendering to VEXTAB](https://phptabs.readthedocs.io/en/latest/renderer.vextab.html)


#### Exporting shortcuts

- `PhpTabs::toJson($flags)`
- `PhpTabs::toSerialized()`
- `PhpTabs::toText()`
- `PhpTabs::toXml()`
- `PhpTabs::toYaml()`
- `PhpTabs::toGuitarPro3()`
- `PhpTabs::toGuitarPro4()`
- `PhpTabs::toGuitarPro5()`
- `PhpTabs::toMidi()`

See manual for
[all available shortcuts](https://phptabs.readthedocs.io/en/latest/export.variables.html#shortcuts)


### Changed

- `PhpTabs::save()`. This method now requires a pathname. Previously,
calling it with no pathname was the same as `PhpTabs::convert()` with
no parameter.

### Removed

- `PhpTabs::import()` and `IOFactory::import()` have been removed and
replaced by `fromArray()`, more explicit about what it expects.

- `PhpTabs::exportTrack()` has been removed. It was not really
exporting a track. Indeed, it was exporting a song with a targeted track
as an array. See [PhpTabs::onlyTrack()](https://phptabs.readthedocs.io/en/latest/target-track-measure.html#onlytrack) instead.

- `PhpTabs::export()`. It had a mixed signature `array|string` which may
be confusing. Furthermore, it was redundant with `convert()` method. So
if you want an array see `PhpTabs::toArray()`. For other formats,
shortcuts have been created.
For instance, exporting to JSON can be made with `PhpTabs::toJson()` and
`PhpTabs::convert('json')`

- `PhpTabs::save('php://output')` is not yet supported. It was the same
thing as doing an `echo PhpTabs::convert()` (with no parameter)

- `getError()`, `hasError()`, `setError()` methods for PhpTabs and
Tablature instances.


### Core changes

Trying to write a MIDI file with an empty song now throws an exception.

A new `PhpTabs\Component\InputStream` class has been introduced to
handle content streams

File errors or parsing failures always throw \Exception.

### Documentation

- A new website at https://phptabs.readthedocs.io/
- More examples and manual for each part of the internal
[music model](https://phptabs.readthedocs.io/en/latest/ref.music-model.html)

### QA

Enforce test suite, code coverage > 80%

________________________________________________________________________

## 0.6.4 - 2020-10-26 ##################################################

## Fixes ##

- Fix from scratch scenarios to Guitar Pro 3, 4 and 5 that gives
  `TypeError` exceptions for meta informations #12

________________________________________________________________________

## 0.6.3 - 2020-10-25 ##################################################

## Features ##

- Type declarations have been specified
- Core : rewrite a large part of MIDI writer classes

## Fixes ##

- Fix MidiWriter corrupted files #12 #13 #6 
- Fix some MIDI timing problems. There are still some work to fix some
  complex timings
- Fix some PHP8 deprecated messages
- Fix some MIDI reader types

________________________________________________________________________

## 0.6.2 - 2020-10-12 ##################################################

## Features ##

- Add support for PHP 8.0RC
- Add auto assignement of the track number when it's stacked in a Song
- Enforce string types when massive assignment

## Fixes ##

- Support for PHP 7.4 : ASCII renderer was not working

________________________________________________________________________

## 0.6.1 - 2019-02-22 ##################################################

### Fixes

- Automatically attach a Song dependency to a Track when it's added 
- `Track::getMeasure($index)` now throws an Exception when index is not
  defined

### Deprecated

- Remove PHP5 and HHVM support

### QA

- PHPUnit 7 is now the minimal supported version for testing

________________________________________________________________________

## 0.6.0 - 2018-12-09 ##################################################

### Features

- [Render as an ASCII tablature](https://stdtabs.github.io/render-as-an-ascii-tab.html)
- [IOFactory](https://stdtabs.github.io/iofactory.html) tool to simplify instanciations
- [getVersion()](https://stdtabs.github.io/getting-started.html) method
- [getTime()](https://stdtabs.github.io/calculate-measure-and-beat-durations-in-seconds.html)
  method to simplify reading of beat durations in seconds
- Support for PHP 7.3

### Note

MidiReader is considered as stable whereas MidiWriter still has some
bugs. It sometimes generates some corrupted MIDI files.

### Issues

- #5 Special thanks to @motniemtin who asked for duration calculations
- A lot of bug fixes

________________________________________________________________________

## 0.5.0 - 2017-12-23 ##################################################

### Features

- [Render a tablature](https://stdtabs.github.io/basics.html#render)
- [export()](https://stdtabs.github.io/basics.html#exportformat) method
- [exportTrack()](https://stdtabs.github.io/basics.html#exporttracktrackindex-format) method
- [import()](https://stdtabs.github.io/basics.html#import-data) method
- [fromJson()](https://stdtabs.github.io/basics.html#fromjsonfilename-method) method
- [fromSerialized()](https://stdtabs.github.io/basics.html#fromserializedfilename-method) method
- Support for PHP 7.2
- Parser are now 20% faster than previous release, even more with PHP7 :)

### API changes

- dump() method has been removed and replaced by export(). It has the
  same properties but it's a more coherent name (import() method)

### Note

Reader/Writer are made for external formats (Guitar Pro, MIDI) whereas
Importer/Exporter are dedicated to internal formats (array, JSON, serialized, etc...).

### Issues

- #1 and #2 Special thanks to @comarius who asked for a vextab renderer


________________________________________________________________________

## 0.4.0 - 2017-08-03 ##################################################

### Features

- [save()](https://github.com/stdtabs/phptabs#savefilename) method
- [convert()](https://github.com/stdtabs/phptabs#converttype) method
- [dump()](https://github.com/stdtabs/phptabs#dumpformat) method (XML, JSON, YAML, Text, PHP array, PHP serialized)
- Add support for PHP 7.1

### QA

- Add support for code coverage (>70%)
- Code optimizations
- Remove a lot of dead code

### BugFixes

- f9a703a Fix measure start must be an integer
- ee57ecc Fix tempo type

### Documentation

- Fresh [documentation website](https://stdtabs.github.io/)

________________________________________________________________________

## 0.3.0 - 2016-05-17 ##################################################

### New Features

- Support of Guitar Pro 5 file format
- Support of MIDI file format

### Code Quality

- Improved performance for all parsers
- More code comments

________________________________________________________________________

## 0.2.0 - 2016-05-08 ##################################################

### Features

- Support of Guitar Pro 4 file format
- Access to instruments
    - countInstruments()
    - getInstruments()
    - getInstrument($index)

### Quality

- Fixes for Guitar Pro 3 file format

________________________________________________________________________

## 0.1.0 - 2016-04-30 ##################################################

- Reader and writer for Guitar Pro 3 format
