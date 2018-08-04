
# Aho Corasick in PHP

This is a small library which implements the [Aho-Corasick string
search
algorithm](https://en.wikipedia.org/wiki/Aho%E2%80%93Corasick_algorithm).

It's coded in pure PHP and self-contained in a single file, `ahocorasick.php`.

It's useful when you want to search for many keywords all at once. It's faster
than simply calling `strpos` many times, and it's much faster than calling
`preg_match_all` with something like `/keyword1|keyword2|...|keywordn/`.

I originally wrote this to use with [F5Bot](https://f5bot.com), since it's
searching for the same set of a few thousand keywords over and over again.

# Usage

It's designed to be really easy to use. You create the `ahocorasick` object,
add your keywords, call `finalize()` to finish setup, and then search your
text. It'll return an array of the keywords found and their position in the
search text.

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

Note: the regex solutions are actually slightly broken. They won't work if you
have a keyword that is a prefix or suffix of another. But hey, who really uses
regex when it's not slightly broken?

Also keep in mind that building the search tree (the `add_needle()` and
`finalize()` calls) takes time. So you'll get the best speed-up if you're
reusing the same keywords and calling `search()` many times.
