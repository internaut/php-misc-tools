# Miscellaneous PHP functions for working with objects, arrays and strings

*misc_tools.php* contains a set of well-tested and well-documented miscellaneous PHP
functions for working with objects, arrays and strings.

## Functions

* for working with objects and arrays:
 * `mergeObjects($a, $b)` -- Merge two simple objects *$a* and *$b* recursively. Values in *$b* will override the ones in *$a*. Values in *$b* and not in *$a* will be added to the output object
 * `getAttr($obj, $attr, $recursive = false)` -- Get an attribute *$attr* from *$obj* which can be either an assoc. array or an PHP object. Traverse recursively through *$obj* when we have an *$attr* with *'obj->subobj->...'* path and *$recursive* is set to *true*
 * `delKeys($x, $keys)` -- Delete all keys (in-place) listed in array *$keys* from object or array *$x*
 * `multiArrayIntersection($arrays)` -- Create intersection of all arrays in *$arrays* using *array_intersection()* function repeatedly
* for working with strings:
 * `encodeURLParamValue($p)` -- Encode an URL parameter value (often used for CouchDB queries). If *$p* is not numeric, wrap quotes around the parameter
 * `abbreviatePersonName($n)` -- Abbreviate a persons name *$n*, e.g. John Wayne -> J. Wayne or Jan-Josef Liefers -> J.J. Liefers