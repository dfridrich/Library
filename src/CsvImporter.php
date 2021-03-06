<?php

/*
 * This file is part of Library package.
 *
 * (c) Dennis Fridrich <fridrich.dennis@gmail.com>
 *
 * For the full copyright and license information,
 * please view the contract or license.
 */

namespace Defr;

/**
 * Class CsvImporter.
 *
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class CsvImporter
{
    const DELIMITER_SEMICOLON = ';';
    const DELIMITER_TAB = '\t';
    const DELIMITER_COMMA = ',';

    private $fp;
    private $parse_header;
    private $header;
    private $delimiter;
    private $length;

    /**
     * @param $file_name
     * @param bool   $parse_header
     * @param string $delimiter
     * @param int    $length
     */
    public function __construct($file_name, $parse_header = false, $delimiter = "\t", $length = 8000)
    {
        $this->fp = fopen($file_name, 'r');
        $this->parse_header = $parse_header;
        $this->delimiter = $delimiter;
        $this->length = $length;
        //$this->lines = $lines;

        if ($this->parse_header) {
            $this->header = fgetcsv($this->fp, $this->length, $this->delimiter);
        }
    }

    public function __destruct()
    {
        if ($this->fp) {
            fclose($this->fp);
        }
    }

    /**
     * @param int $max_lines
     *
     * @return array
     */
    public function get($max_lines = 0)
    {
        //if $max_lines is set to 0, then get all the data

        $data = [];

        if ($max_lines > 0) {
            $line_count = 0;
        } else {
            $line_count = -1;
        } // so loop limit is ignored

        while ($line_count < $max_lines && false !== ($row = fgetcsv($this->fp, $this->length, $this->delimiter))) {
            if ($this->parse_header) {
                $row_new = null;
                foreach ($this->header as $i => $heading_i) {
                    $row_new[$heading_i] = $row[$i];
                }
                $data[] = $row_new;
            } else {
                $data[] = $row;
            }

            if ($max_lines > 0) {
                ++$line_count;
            }
        }

        if (1 === $max_lines && isset($data[0])) {
            return $data[0];
        }

        return $data;
    }
}
