<?php
namespace Quak\ShopsAdminBundle\Report;

/**
 * Class for generating xml with multiple spreadsheets
 *
 * @author  Marin Crnković <unknown@unknown.com>
 * @author  Wojciech Chojnacki <quak@tlen.pl>
 * @version 0.9
 * @update_date 21.01.2009
 *
 * Updated createRows method to handle individual cell styling and more styling options
 *
 * @update_date 27.11.2012
 *
 */
class XMLBuilder
{
    private $nl;
    private $tab;
    private $rows;
    private $worksheets;
    private $counters;
    private $xml;
    private $columnWidth;
    private $styles;
    private $debug;
    private $rowArray;

    const HAIRLINE = 0;
    const THIN = 1;
    const MEDIUM = 2;
    const THICK = 3;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->resetState();
    }

    /**
     * Reset state of builder
     */
    public function resetState()
    {
        $this->columnWidth = 60;
        $this->debug = false;
        $this->rowArray = array();
        $this->rows = array();
        $this->worksheets = array();
        $this->counters = array();
        $this->nl = "\n";
        $this->tab = "\t";
        $this->styles = array();
        $this->xml = '';
    }

    /**
     * Set debug
     */
    public function debug()
    {
        $this->debug = true;
    }

    /**
     * Generate xml
     *
     * @return string
     */
    public function generate()
    {
        // Create header
        $xml = $this->createHeader() . $this->nl;
        // Put all worksheets
        $xml .= join($this->nl, $this->worksheets) . $this->nl;
        // Finish with a footer
        $xml .= $this->createFooter();
        $this->xml = $xml;

        return $this->xml;
    }

    /**
     * Create worksheet
     * Uppon creating a worksheet, delete counters
     *
     * @param string $worksheetName name of the worksheet
     */
    public function createWorksheet($worksheetName)
    {
        $worksheet = '<Worksheet ss:Name="' . $worksheetName . '">';
        $worksheet .= $this->createTable();
        $worksheet .= '<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
        <ProtectObjects>False</ProtectObjects>
        <ProtectScenarios>False</ProtectScenarios>
        </WorksheetOptions>
        </Worksheet>';

        // Unset the counters and rows so you can generate another worksheet table
        $this->counters = array();
        $this->rowArray = array();
        $this->rows = array();

        // Add generated worksheet to the worksheets array
        $this->worksheets[] = $worksheet;
    }

    /**
     * Create table
     *
     * @return string
     */
    protected function createTable()
    {
        // Create rows with the method that automaticaly sets counters for number of columns and rows
        $rows = $this->createRows();

        // Table header
        $table = '<Table ss:ExpandedColumnCount="' . $this->counters['cols'] . '" ss:ExpandedRowCount="' . $this->counters['rows'] . '" x:FullColumns="1" x:FullRows="1">' . $this->nl;

        // Columns data (width mainly)
        for ($i = 1; $i <= $this->counters['cols']; $i++) {
            $table .= '<Column ss:Index="' . $i . '" ss:Width="' . $this->columnWidth . '" />' . $this->nl;
        }
        // Insert all rows
        $table .= join('', $rows);

        // End table
        $table .= '</Table>' . $this->nl;

        return $table;
    }

    /**
     * Add another row into the array
     *
     * @param mixed $array array with row cells
     * @param mixed $style default null, if set, adds style to the array
     */
    public function addRow($array, $style = null)
    {
        if (!is_array($array)) {
            // Asume the delimiter is , or ;
            $array = str_replace(',', ';', $array);
            $array = explode(';', $array);
        }
        if (!is_null($style)) {
            $styleArray = array('attach_style' => $style);
            $array = array_merge($array, $styleArray);
        }

        $this->rowArray[] = $array;
    }

    /**
     * Create rows
     *
     * @return array
     */
    protected function createRows()
    {
        $rowArray = $this->rowArray;
        if (!$rowArray || !is_array($rowArray)) {

            return;
        }

        $cnt = 0;
        $rowCell = array();
        foreach ($rowArray as $rowData) {
            $cnt++;

            // See if there are styles attached
            $style = null;
            if (!empty($rowData['attach_style'])) {
                $style = $rowData['attach_style'];
                unset($rowData['attach_style']);
            }

            // Store the counter of rows
            $this->counters['rows'] = $cnt;

            $cells = '';
            $cellCount = 0;
            foreach ($rowData as $key => $cellData) {
                $cellCount++;

                if (is_array($cellData)) {
                    $style = $cellData['attach_style'];
                    $cellData = $cellData['data'];
                }

                $cells .= $this->nl . $this->prepareCell($cellData, $style);
            }

            // Store the number of cells in row
            $rowCell[$cnt][] = $cellCount;

            $this->rows[] = '<Row>' . $cells . $this->nl . '</Row>' . $this->nl;
        }

        // Find out max cells in all rows
        $maxCells = max($rowCell);
        $this->counters['cols'] = $maxCells[0];

        return $this->rows;
    }

    /**
     * Prepare cell
     *
     * @param string $cellData string for a row cell
     * @param string $style    cell style
     *
     * @return string
     */
    protected function prepareCell($cellData, $style = null)
    {
        $merge = '';
        $str = strip_tags($cellData);
        $str = str_replace("\t", " ", $str); // replace tabs with spaces
        $str = str_replace("&nbsp;", " ", $str); // replace entities with spaces
        $str = str_replace("\r\n", "\n", $str); // replace windows-like new-lines with unix-like
        $str = str_replace('"', '""', $str); // escape quotes so we support multiline cells now
        preg_match('#\"\"#', $str) ? $str = '"' . $str . '"' : $str; // If there are double doublequotes, encapsulate str in doublequotes

        // Formating: bold
        if (!is_null($style)) {
            $style = ' ss:StyleID="' . $style . '"';
        } elseif (preg_match('/^\*([^\*]+)\*$/', $str, $out)) {
            $style = ' ss:StyleID="bold"';
            $str = $out[1];
        }

        if (preg_match('/\|([\d]+)$/', $str, $out)) {
            $merge = ' ss:MergeAcross="' . $out[1] . '"';
            $str = str_replace($out[0], '', $str);
        }
        // Get type
        $type = preg_match('/^([\d]+)$/', $str) ? 'Number' : 'String';

        return '<Cell' . $style . $merge . '><Data ss:Type="' . $type . '">' . $str . '</Data></Cell>';
    }

    /**
     * Create header
     *
     * @return string
     */
    protected function createHeader()
    {
        if (is_array($this->styles)) {
            $styles = join('', $this->styles);
        }
        $header = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<Workbook
	xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet"
	xmlns:html="http://www.w3.org/TR/REC-html40"
	xmlns:o="urn:schemas-microsoft-com:office:office"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xmlns="urn:schemas-microsoft-com:office:spreadsheet"
	xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml"
	xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
	xmlns:x="urn:schemas-microsoft-com:office:excel">
	<OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
	</OfficeDocumentSettings>
	<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
	    <WindowHeight>9000</WindowHeight>
	    <WindowWidth>13860</WindowWidth>
	    <WindowTopX>240</WindowTopX>
	    <WindowTopY>75</WindowTopY>
	    <ProtectStructure>False</ProtectStructure>
	    <ProtectWindows>False</ProtectWindows>
	</ExcelWorkbook>
	<Styles>
	    <Style ss:ID="Default" ss:Name="Default"/>
        $styles
	</Styles>
EOF;

        return $header;
    }

    /**
     * Add style to the header
     *
     * @param string $styleId    id of the style the cells will reference to
     * @param array  $parameters array with parameters
     */
    public function addStyle($styleId, $parameters)
    {
        $interior = '';
        $align = '';
        $font = '';
        $border = '';
        foreach ($parameters as $param => $data) {
            switch ($param) {
                case 'size':
                    $font['ss:Size'] = $data;
                    break;

                case 'font':
                    $font['ss:FontName'] = $data;
                    break;

                case 'color':
                case 'colour':
                    $font['ss:Color'] = $data;
                    break;

                case 'bgcolor':
                    $interior['ss:Color'] = $data;
                    break;

                case 'bold':
                    $font['ss:Bold'] = $data;
                    break;

                case 'italic':
                    $font['ss:Italic'] = $data;
                    break;

                case 'strike':
                    $font['ss:StrikeThrough'] = $data;
                    break;

                case 'align':
                    $align['ss:Horizontal'] = $data;
                    break;

                case 'border':
                    $border['ss:Weight'] = $data;
                    break;
            }
        }
        if (!empty($interior)) {
            $interiors = '';
            foreach ($interior as $param => $value) {
                $interiors .= ' ' . $param . '="' . $value . '"';
            }
            $interior = '<Interior ss:Pattern="Solid"' . $interiors . ' />' . $this->nl;
        }
        if (!empty($align)) {
            $alignment = '';
            foreach ($align as $param => $value) {
                $alignment .= ' ' . $param . '="' . $value . '"';
            }
            $align = '<Alignment ' . $alignment . ' />' . $this->nl;
        }
        if (!empty($font)) {
            $fonts = '';
            foreach ($font as $param => $value) {
                $fonts .= ' ' . $param . '="' . $value . '"';
            }
            $font = '<Font' . $fonts . ' />' . $this->nl;
        }
        if (!empty($border)) {
            $borders = '';
            foreach ($border as $param => $value) {
                $borders .= ' ' . $param . '="' . $value . '"';
            }
            $border = '<Borders>'
                . '<Border ss:Position="Left" ss:LineStyle="Continuous" ' . $borders . ' />'
                . '<Border ss:Position="Right" ss:LineStyle="Continuous" ' . $borders . ' />'
                . '<Border ss:Position="Top" ss:LineStyle="Continuous" ' . $borders . ' />'
                . '<Border ss:Position="Bottom" ss:LineStyle="Continuous" ' . $borders . ' />'
                . '</Borders>' . $this->nl;
        }
        $this->styles[] = '
        <Style ss:ID="' . $styleId . '">
            ' . $interior . $font . $align . $border . '
        </Style>';
    }

    /**
     * Create footer
     *
     * @return string
     */
    protected function createFooter()
    {
        return '</Workbook>';
    }
}