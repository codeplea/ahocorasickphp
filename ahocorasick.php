<?php

/*
 * ahocorasick - fast string searching in php
 *
 * Copyright (c) 2017-2018 Lewis Van Winkle
 *
 * http://CodePlea.com
 *
 * This software is provided 'as-is', without any express or implied
 * warranty. In no event will the authors be held liable for any damages
 * arising from the use of this software.
 *
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely, subject to the following restrictions:
 *
 * 1. The origin of this software must not be misrepresented; you must not
 *    claim that you wrote the original software. If you use this software
 *    in a product, an acknowledgement in the product documentation would be
 *    appreciated but is not required.
 * 2. Altered source versions must be plainly marked as such, and must not be
 *    misrepresented as being the original software.
 * 3. This notice may not be removed or altered from any source distribution.
 *
 */



class ahocorasick {

  private $nodes = array(array());

  private $final = 0;


  public function add_needle($needle) {
    if ($this->final) throw new Exception("Cannot add word to finalized ahocorasick.");

    $nodes = &$this->nodes;
    $n = 0;

    for ($i = 0; $i < strlen($needle); ++$i) {
      $c = $needle[$i];

      if (!isset($nodes[$n][$c])) {
        $nodes[$n][$c] = count($nodes);
        $nodes[] = array();
      }
      $n = $nodes[$n][$c];
    }

    $nodes[$n][0][] = $needle;
  }


  public function finalize() {
    $nodes = &$this->nodes;
    $queue = array();

    foreach($nodes[0] as $j => $_) {
      $nodes[$nodes[0][$j]][1] = 0;
      $queue[] = $nodes[0][$j];
    }

    while (count($queue)) {
      $r = $queue[0];
      $queue = array_slice($queue, 1);

      foreach($nodes[$r] as $j => $_) {
        if ($j === 0 || $j === 1) continue;
        $v = $nodes[$r][1];
        $u = $nodes[$r][$j];
        while ($v > 0 && !isset($nodes[$v][$j])) $v = $nodes[$v][1];
        $nodes[$u][1] = isset($nodes[$v][$j]) ? $nodes[$v][$j] : $v;
        if (isset($nodes[$nodes[$u][1]][0])) {
          if (!isset($nodes[$u][0])) $nodes[$u][0] = array();
          $nodes[$u][0] = array_merge($nodes[$u][0], $nodes[$nodes[$u][1]][0]);
        }
        $queue[] = $u;
      }
    }

    $this->final = 1;
  }


  public function search($haystack) {
    if (!$this->final) throw new Exception("Must call finalize() before search.");

    $nodes = &$this->nodes;
    $found = array();
    $n = 0;

    for ($i = 0; $i < strlen($haystack); ++$i) {
      $c = $haystack[$i];

      while(!isset($nodes[$n][$c]) && $n) {
        $n = $nodes[$n][1];
        if ($n === null) die();
      }

      if (isset($nodes[$n][$c]))
        $n = $nodes[$n][$c];

      if (isset($nodes[$n][0])) {
        $z = $nodes[$n][0];
        foreach($z as $w) {
          $found[] = array($w, $i - strlen($w) + 1);
        }
      }
    }

    return $found;
  }

};
