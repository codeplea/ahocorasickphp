
# Aho Corasick in PHP

This is a small library which implements the [Aho-Corasick string
search
algorithm](https://en.wikipedia.org/wiki/Aho%E2%80%93Corasick_algorithm).

It's coded in pure PHP, and self-contained in a single file, `ahocorasick.php`.

It's useful when you want to search for many keywords all at once. It's faster
than simply calling `strpos` many times, and it's much faster than calling
`preg_match_all` with something like `/keyword1|keyword2|...|keywordn/`.

# Usage

It's designed to be really easy to use. You create the search engine, add your
keywords, call `finalize()` to finish setup, and then search your text. It'll
return an array of the keywords found and their position in the search text.

Create, add keywords, and `finalize()`:

```php
require('ahocorasick.php');

$ac = new ahocorasick();

$ac->add_needle('art');
$ac->add_needle('cart');
$ac->add_needle('ted');

$ac->finalize();

```

Call `search()` to preform the actual search. It'll return an array of matches.

```php
$found = $ac->search('a carted mart lot one blue ted');
print_r($found);
```

`$found` will be an array with these elements:

```
[0] => Array
    (
        [0] => cart
        [1] => 2
    )
[1] => Array
    (
        [0] => art
        [1] => 3
    )
[2] => Array
    (
        [0] => ted
        [1] => 5
    )
[3] => Array
    (
        [0] => art
        [1] => 10
    )
[4] => Array
    (
        [0] => ted
        [1] => 27
    )
```

See `example.php` for a complete example.

# Speed

A simple benchmarking program is included which compares various alternatives.


Check for correctness, strpos. Regex is broken, but that's the nature of regex.

```
$ php benchmark.php
Loaded 3000 keywords to search on a text of 19377 characters.

Searching with strpos...
time: 0.38440799713135

Searching with preg_match...
time: 5.6817619800568

Searching with preg_match_all...
time: 5.0735609531403

Searching with aho corasick...
time: 0.054709911346436

```

You should test it on your data to make sure you really get a speedup. The most
dramatic speed ups come from where you're searching for many keywords.

Also keep in mind that building the search tree (the `add_needle()` and
`finalize()` calls) takes time. So you'll get the best speed-up if you're
reusing the same keywords and calling `search()` many times.
