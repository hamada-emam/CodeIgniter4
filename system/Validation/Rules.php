<?php

/**
 * This file is part of the CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CodeIgniter\Validation;

use Config\Database;
use InvalidArgumentException;

/**
 * Validation Rules.
 */
class Rules
{
    //--------------------------------------------------------------------

    /**
     * The value does not match another field in $data.
     *
     * @param string $str
     * @param string $field
     * @param array  $data  Other field/value pairs
     *
     * @return boolean
     */
    public function differs(string $str = null, string $field, array $data): bool
    {
        if (strpos($field, '.') !== false)
        {
            return $str !== dot_array_search($field, $data);
        }

        return array_key_exists($field, $data) && $str !== $data[$field];
    }

    //--------------------------------------------------------------------

    /**
     * Equals the static value provided.
     *
     * @param string $str
     * @param string $val
     *
     * @return boolean
     */
    public function equals(string $str = null, string $val): bool
    {
        return $str === $val;
    }

    //--------------------------------------------------------------------

    /**
     * Returns true if $str is $val characters long.
     * $val = "5" (one) | "5,8,12" (multiple values)
     *
     * @param string $str
     * @param string $val
     *
     * @return boolean
     */
    public function exact_length(string $str = null, string $val): bool
    {
        $val = explode(',', $val);

        foreach ($val as $tmp)
        {
            if (is_numeric($tmp) && (int) $tmp === mb_strlen($str))
            {
                return true;
            }
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Greater than
     *
     * @param string $str
     * @param string $min
     *
     * @return boolean
     */
    public function greater_than(string $str = null, string $min): bool
    {
        return is_numeric($str) && $str > $min;
    }

    //--------------------------------------------------------------------

    /**
     * Equal to or Greater than
     *
     * @param string $str
     * @param string $min
     *
     * @return boolean
     */
    public function greater_than_equal_to(string $str = null, string $min): bool
    {
        return is_numeric($str) && $str >= $min;
    }

    //--------------------------------------------------------------------

    /**
     * Checks the database to see if the given value exist.
     * Can ignore records by field/value to filter (currently
     * accept only one filter).
     *
     * Example:
     *    is_not_unique[table.field,where_field,where_value]
     *    is_not_unique[menu.id,active,1]
     *
     * @param string $str
     * @param string $field
     * @param array  $data
     *
     * @return boolean
     */
    public function is_not_unique(string $str = null, string $field, array $data): bool
    {
        // Grab any data for exclusion of a single row.
        [$field, $whereField, $whereValue] = array_pad(explode(',', $field), 3, null);

        // Break the table and field apart
        sscanf($field, '%[^.].%[^.]', $table, $field);

        $db = Database::connect($data['DBGroup'] ?? null);

        $row = $db->table($table)
                  ->select('1')
                  ->where($field, $str)
                  ->limit(1);

        if (! empty($whereField) && ! empty($whereValue) && ! preg_match('/^\{(\w+)\}$/', $whereValue))
        {
            $row = $row->where($whereField, $whereValue);
        }

        return (bool) ($row->get()->getRow() !== null);
    }

    //--------------------------------------------------------------------

    /**
     * Value should be within an array of values
     *
     * @param string $value
     * @param string $list
     *
     * @return boolean
     */
    public function in_list(string $value = null, string $list): bool
    {
        $list = array_map('trim', explode(',', $list));

        return in_array($value, $list, true);
    }

    //--------------------------------------------------------------------

    /**
     * Checks the database to see if the given value is unique. Can
     * ignore a single record by field/value to make it useful during
     * record updates.
     *
     * Example:
     *    is_unique[table.field,ignore_field,ignore_value]
     *    is_unique[users.email,id,5]
     *
     * @param string $str
     * @param string $field
     * @param array  $data
     *
     * @return boolean
     */
    public function is_unique(string $str = null, string $field, array $data): bool
    {
        // Grab any data for exclusion of a single row.
        [$field, $ignoreField, $ignoreValue] = array_pad(explode(',', $field), 3, null);

        // Break the table and field apart
        sscanf($field, '%[^.].%[^.]', $table, $field);

        $db = Database::connect($data['DBGroup'] ?? null);

        $row = $db->table($table)
                  ->select('1')
                  ->where($field, $str)
                  ->limit(1);

        if (! empty($ignoreField) && ! empty($ignoreValue) && ! preg_match('/^\{(\w+)\}$/', $ignoreValue))
        {
            $row = $row->where("{$ignoreField} !=", $ignoreValue);
        }

        return (bool) ($row->get()->getRow() === null);
    }

    //--------------------------------------------------------------------

    /**
     * Less than
     *
     * @param string $str
     * @param string $max
     *
     * @return boolean
     */
    public function less_than(string $str = null, string $max): bool
    {
        return is_numeric($str) && $str < $max;
    }

    //--------------------------------------------------------------------

    /**
     * Equal to or Less than
     *
     * @param string $str
     * @param string $max
     *
     * @return boolean
     */
    public function less_than_equal_to(string $str = null, string $max): bool
    {
        return is_numeric($str) && $str <= $max;
    }

    //--------------------------------------------------------------------

    /**
     * Matches the value of another field in $data.
     *
     * @param string $str
     * @param string $field
     * @param array  $data  Other field/value pairs
     *
     * @return boolean
     */
    public function matches(string $str = null, string $field, array $data): bool
    {
        if (strpos($field, '.') !== false)
        {
            return $str === dot_array_search($field, $data);
        }

        return array_key_exists($field, $data) && $str === $data[$field];
    }

    //--------------------------------------------------------------------

    /**
     * Returns true if $str is $val or fewer characters in length.
     *
     * @param string $str
     * @param string $val
     *
     * @return boolean
     */
    public function max_length(string $str = null, string $val): bool
    {
        return (is_numeric($val) && $val >= mb_strlen($str));
    }

    //--------------------------------------------------------------------

    /**
     * Returns true if $str is at least $val length.
     *
     * @param string $str
     * @param string $val
     *
     * @return boolean
     */
    public function min_length(string $str = null, string $val): bool
    {
        return (is_numeric($val) && $val <= mb_strlen($str));
    }

    //--------------------------------------------------------------------

    /**
     * Does not equal the static value provided.
     *
     * @param string $str
     * @param string $val
     *
     * @return boolean
     */
    public function not_equals(string $str = null, string $val): bool
    {
        return $str !== $val;
    }

    //--------------------------------------------------------------------

    /**
     * Value should not be within an array of values.
     *
     * @param string $value
     * @param string $list
     *
     * @return boolean
     */
    public function not_in_list(string $value = null, string $list): bool
    {
        return ! $this->in_list($value, $list);
    }

    //--------------------------------------------------------------------

    /**
     * Required
     *
     * @param mixed $str Value
     *
     * @return boolean          True if valid, false if not
     */
    public function required($str = null): bool
    {
        if (is_object($str))
        {
            return true;
        }

        return is_array($str) ? ! empty($str) : (trim($str) !== '');
    }

    //--------------------------------------------------------------------

    /**
     * The field is required when any of the other required fields are present
     * in the data.
     *
     * Example (field is required when the password field is present):
     *
     *     required_with[password]
     *
     * @param string|null $str
     * @param string|null $fields List of fields that we should check if present
     * @param array       $data   Complete list of fields from the form
     *
     * @return boolean
     */
    public function required_with($str = null, string $fields = null, array $data = []): bool
    {
        if (is_null($fields) || empty($data))
        {
            throw new InvalidArgumentException('You must supply the parameters: fields, data.');
        }

        $fields = explode(',', $fields);

        // If the field is present we can safely assume that
        // the field is here, no matter whether the corresponding
        // search field is present or not.
        $present = $this->required($str ?? '');

        if ($present)
        {
            return true;
        }

        // Still here? Then we fail this test if
        // any of the fields are present in $data
        // as $fields is the lis
        $requiredFields = [];

        foreach ($fields as $field)
        {
            if ((array_key_exists($field, $data) && ! empty($data[$field])) ||
                (strpos($field, '.') !== false && ! empty(dot_array_search($field, $data)))                )
            {
                $requiredFields[] = $field;
            }
        }

        return empty($requiredFields);
    }

    //--------------------------------------------------------------------

    /**
     * The field is required when all of the other fields are present
     * in the data but not required.
     *
     * Example (field is required when the id or email field is missing):
     *
     *     required_without[id,email]
     *
     * @param string|null $str
     * @param string|null $fields
     * @param array       $data
     *
     * @return boolean
     */
    public function required_without($str = null, string $fields = null, array $data = []): bool
    {
        if (is_null($fields) || empty($data))
        {
            throw new InvalidArgumentException('You must supply the parameters: fields, data.');
        }

        $fields = explode(',', $fields);

        // If the field is present we can safely assume that
        // the field is here, no matter whether the corresponding
        // search field is present or not.
        $present = $this->required($str ?? '');

        if ($present)
        {
            return true;
        }

        // Still here? Then we fail this test if
        // any of the fields are not present in $data
        foreach ($fields as $field)
        {
            if ((strpos($field, '.') === false && (! array_key_exists($field, $data) || empty($data[$field]))) ||
                (strpos($field, '.') !== false && empty(dot_array_search($field, $data)))
            )
            {
                return false;
            }
        }

        return true;
    }

    //--------------------------------------------------------------------
}
