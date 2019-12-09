<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_DB_active_record extends CI_DB_active_record {
    // --------------------------------------------------------------------

    /**
     * Join
     *
     * Generates the JOIN portion of the query
     *
     * @param	string
     * @param	string	the join condition
     * @param	string	the type of join
     * @return	object
     */
    public function join($table, $cond, $type = '') {
        if ($type != '') {
            $type = strtoupper(trim($type));

            if (!in_array($type, array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER'))) {
                $type = '';
            } else {
                $type .= ' ';
            }
        }

        // Extract any aliases that might exist.  We use this information
        // in the _protect_identifiers to know whether to add a table prefix
        $this->_track_aliases($table);

        // Strip apart the condition and protect the identifiers
        $cond_array = preg_split("/and/i", $cond);

        if (count($cond_array) > 1) {
            $conds_array = array();
            foreach ($cond_array as $conds) {
                if (preg_match('/([\w\.]+)([\W\s]+)(.+)/', $conds, $match)) {

                    $match[1] = $this->_protect_identifiers($match[1]);
                    $match[3] = $this->_protect_identifiers($match[3]);

                    $conds_array[] = $match[1] . $match[2] . $match[3];
                }
            }
            $cond = implode(' AND ', $conds_array);
        } else {

            if (preg_match('/([\w\.]+)([\W\s]+)(.+)/', $cond, $match)) {

                $match[1] = $this->_protect_identifiers($match[1]);
                $match[3] = $this->_protect_identifiers($match[3]);

                $cond = $match[1] . $match[2] . $match[3];
            }
        }
    }
}