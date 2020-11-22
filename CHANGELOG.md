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


#### PhpTabs::save() accepts an optional second parameter to force
conversion

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

### QA

Enforce test suite, code coverage > 80%
