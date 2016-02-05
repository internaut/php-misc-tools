<?php
/**************** FUNCTIONS THAT WORK ON OBJECTS ****************************************/

/**
 * Merge two simple objects $a and $b recursively. Values in $b will override the ones in $a.
 * Values in $b and not in $a will be added to the output object.
 * Values will be copied, not simply referenced.
 * @param object $a object A
 * @param object $b object B (will override A's values wherever the keys match)
 * @return stdClass merged output object
 */
function mergeObjects($a, $b) {
    assert(is_object($a));
    assert(is_object($b));

    $o = new stdClass();

    // add all values from $a to $o and where existing, override by $b's values
    foreach (get_object_vars($a) as $k => $aV) {
        if (isset($b->$k)) {
            if (is_object($aV)) {
                $v = mergeObjects($a->$k, $b->$k);
            } else {
                $v = $b->$k;
            }
        } else {
            if (is_object($aV)) {
                $v = clone $aV;
            } else {
                $v = $aV;
            }
        }

        $o->$k = $v;
    }

    // if there're values in $b that aren't in $a, add them here
    foreach (get_object_vars($b) as $k => $bV) {
        if (!isset($a->$k)) {
            if (is_object($bV)) {
                $v = clone $bV;
            } else {
                $v = $bV;
            }

            $o->$k = $v;
        }
    }

    return $o;
}

/**
 * Delete all keys (in-place) in array $keys in object or array $x.
 *
 * @param $x array or object to delete keys in
 * @param $keys array with keys to delete
 */
function delKeys($x, $keys) {
    assert(is_object($x) || is_array($x));
    $isObj = is_object($x);

    foreach ($keys as $k) {
        if ($isObj) {
            unset($x->$k);
        } else {
            unset($x[$k]);
        }
    }
}

/**
 * Get an attribute $attr from $obj which can be either an assoc. array or an PHP object.
 * @param mixed $obj variable which can be either an assoc. array or an PHP object
 * @param string $attr attribute name
 * @param bool $recursive traverse recursively through $obj when we have an $attr with 'obj->subobj->...' path
 * @return mixed attribute value
 * @throws Exception
 */
function getAttr($obj, $attr, $recursive = false) {
    if ($recursive) {
        $attrPath = explode('->', $attr);
        $attr = $attrPath[0];
        if (count($attrPath) > 1) {
            $attrNextLevel = implode('->', array_slice($attrPath, 1));
        } else {
            $attrNextLevel = null;
        }
    } else {
        $attrNextLevel = null;
    }

    if (is_array($obj)) {
        if (!isset($obj[$attr])) {
            return null;
        }

        if ($recursive && $attrNextLevel && (is_array($obj[$attr]) || is_object($obj[$attr]))) {
            return getAttr($obj[$attr], $attrNextLevel, true);
        } else {
            return $obj[$attr];
        }
    } else if (is_object($obj)) {
        if (!isset($obj->$attr)) {
            return null;
        }

        if ($recursive && $attrNextLevel && (is_array($obj->$attr) || is_object($obj->$attr))) {
            return getAttr($obj->$attr, $attrNextLevel, true);
        } else {
            return $obj->$attr;
        }
    } else {
        throw new Exception('Must be either array or object: ' . $obj);
    }
}

/**
 * Check if an attribute $attr exists in $obj which can be either an assoc. array or an PHP object.
 * @param mixed $obj variable which can be either an assoc. array or an PHP object
 * @param string $attr attribute name
 * @return mixed attribute value
 * @throws Exception
 */
function hasAttr($obj, $attr) {
    if (is_null($obj)) return false;

    if (is_array($obj)) {
        return isset($obj[$attr]);
    } else if (is_object($obj)) {
        return isset($obj->$attr);
    } else {
        throw new Exception('Must be either array or object: ' . $obj);
    }
}

/**************** FUNCTIONS THAT WORK ON ARRAYS *****************************************/

/**
 * Create intersection of all arrays in $arrays using array_intersection() function.
 *
 * @param $arrays array input arrays
 * @return array intersection of all input arrays
 */
function multiArrayIntersection($arrays) {
    assert(is_array($arrays) && count($arrays) > 1);

    $intersect = array_intersect($arrays[0], $arrays[1]);
    $rest = array_slice($arrays, 2);
    foreach ($rest as $a) {
        $intersect = array_intersect($intersect, $a);
    }

    return array_values($intersect);
}

/**************** FUNCTIONS THAT WORK ON STRINGS ****************************************/

/**
 * Encode an URL parameter value (often used for CouchDB queries).
 * If $p is not numeric, wrap quotes around the parameter
 * @param mixed $p parameter value
 * @return string URL encoded parameter value
 * @throws Exception
 */
function encodeURLParamValue($p) {
    if (is_numeric($p)) {
        $q = urlencode($p);
    } else if (is_string($p)) {
        $q = urlencode('"' . $p . '"');
    } else if (is_object($p) && !(array)$p) {
    	$q = "{}";
    } else {
        throw new Exception('URL parameter must be either numeric or a string or an empty object');
    }

    return $q;
}

/**
 * Abbreviate a persons name, e.g. John Wayne -> J. Wayne or Jan-Josef Liefers -> J.J. Liefers
 * @param string $n full person name
 * @return string abbreviated person name
 */
function abbreviatePersonName($n) {
    $nParts = preg_split("/[\s-]+/", $n);
    $numNameParts = count($nParts);

    if ($numNameParts <= 1) {
        return $n;
    }

    $abbrevs = '';
    for ($i = 0; $i < $numNameParts - 1; $i++) {
        $forename = trim($nParts[$i]);
        if (strlen($forename) <= 0) {
            continue;
        }
        $abbrevs .= substr($forename, 0, 1) . '.';
    }

    return $abbrevs . ' ' . $nParts[$numNameParts - 1];
}