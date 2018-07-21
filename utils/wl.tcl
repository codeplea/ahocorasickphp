
#this script generates random keywords and text from a dictionary

set in [open words.txt r]
set words [read $in]
close $in

set words [split $words "\n"]

puts $words
puts [llength $words]


set keep {}
foreach w $words {
    if {rand() < .05} {
        lappend keep $w
    }
}

set keep [lrange $keep 0 2999]
puts [llength $keep]



set text {}
for {set i 0} {$i < 2000} {incr i} {
    lappend text [lindex $words [expr {int([llength $words] * rand())}]]
}

puts [llength $text]

set out [open keywords.php w]
puts -nonewline $out "<?php\n\$needles = array('";
puts -nonewline $out [join $keep {', '}]
puts -nonewline $out "');\n"

puts -nonewline $out "\$haystack = '[join $text { }]';\n"

close $out
