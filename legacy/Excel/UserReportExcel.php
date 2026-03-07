<?php

use Carbon\Carbon;

require_once __DIR__ . '/PEAR.php';
require_once __DIR__ . '/writer.php';

/**
 * Clase para la generacion de reportes
 */
class UserReportExcel extends Spreadsheet_Excel_Writer
{

  /**
   * Almacena la cabecera de las paginas
   */
  private $_rx_titulo = "";

  /**
   * Almacena las opciones de los campos
   */
  private $_rx_fields = array();

  /**
   * Contenido de la fila actual del reporte
   */
  private $_rx_row = array();

  /**
   * Almacena los diferentes agrupamientos
   */
  private $_rx_groups = array();

  /**
   * Numero de fila actual del reporte
   */
  public $_rx_num_row;

  /**
   * Numero de columna actual del reporte para Cell()
   */
  private $_rx_col_cell = 0;

  /**
   * Hojas de trabajo
   */
  private $_rx_worksheets = array();

  /**
   * Actual hoja de trabajo
   */
  private $_rx_current_worksheet = 0;

  /**
   * Formato de diversas celdas
   */
  public $_rx_formats = array();

  /**
   * Ancho de las columnas
   */
  private $_rx_widthCol = array();

  /**
   * Constructor para los reportes
   */

  /**
   * Numero de columna actual del reporte
   */
  private $_rx_acum_ancho = 0;

  /**
   * Suma Campos del reporte
   */
  public $_sum = array();

  public function __construct($titulo, $fields)
  {
    $this->_rx_titulo = $titulo;
    $this->_rx_fields = is_array($fields) ? $fields : array();
    foreach ($this->_rx_fields as $key => $value) {
      $this->_rx_widthCol[$key] = strlen($value["header"]) * 1.50;
    }
    parent::__construct('');
  }

  /**
   * Inicializa el reporte, previamente configurado
   */
  public function startReport(?string $title = null, ?array $paramsHeader = null)
  {
    $this->_rx_titulo = $title ?? $this->_rx_titulo;
    $this->_rx_worksheets[] = &$this->addWorksheet("Hoja 1");
    $this->_SetFormats();
    $this->_SetHeader($paramsHeader);
  }

  /**
   * Metodo para escribir celdas
   */
  public function writeCell($row, $col, $text, $format = null, $type = "S")
  {
    if ($type == "S") {
      $this->_rx_worksheets[$this->_rx_current_worksheet]->writeString($row, $col, ($text), $format);
    } else {
      $this->_rx_worksheets[$this->_rx_current_worksheet]->write($row, $col, $text, $format);
    }
  }

  /**
   * Cabecera del reporte
   */
  private function _SetHeader($paramsHeader)
  {
    $row = 1;
    $ws = $this->_rx_current_worksheet;
    $this->writeCell($row++, 0, date('Y-m-d') . "-" . date('H:i:s'));
    $this->writeCell($row++, 0, $paramsHeader['razsoc'] ?? 'CAJA DE COMPENSACIÓN FAMILIAR DEL CAQUETÁ');
    $this->writeCell($row++, 0, 'NIT. ' . ($paramsHeader['nit'] ?? '891.190.047-2'));
    $row++;
    if (is_array($this->_rx_titulo)) {
      foreach ($this->_rx_titulo as $titulo) {
        $this->writeCell($row++, 0, $titulo, $this->_rx_formats["title"]);
      }
    } else {
      $this->writeCell($row++, 0, $this->_rx_titulo, $this->_rx_formats["title"]);
    }
    $date = Carbon::now();
    $str = "Elaborado el " . $date->day . " de " . $date->month . " de " . $date->year;
    $this->writeCell($row++, 0, $str, $this->_rx_formats["subtitle"]);
    $row++;
    if (!count($this->_rx_groups)) {
      $col = 0;
      foreach ($this->_rx_fields as $field) {
        $this->writeCell($row, $col++, $field["header"], $this->_rx_formats["header"]);
      }
    }
    for ($i = 1; $i < $row - 1; $i++) {
      $this->_rx_worksheets[$ws]->setMerge($i, 0, $i, count($this->_rx_fields) - 1);
    }
    if (!count($this->_rx_groups)) $row += 2;
    $this->_rx_num_row = $row - 1;
  }


  /**
   * Crea los diferentes formatos para diversos usos
   */
  private function _SetFormats()
  {
    $this->setCustomColor(10, 29, 97, 19);
    $this->_rx_formats["title"] = &$this->addFormat(
      array(
        "fontfamily" => "Arial",
        "size" => 15,
        "align" => "center",
        "bold" => 1,
        "color" => 10,
        "italic" => 1
      )
    );

    $this->_rx_formats["subtitle"] = &$this->addFormat(
      array(
        "fontfamily" => "Arial",
        "size" => 12,
        "align" => "left",
        "bold" => 1,
        "color" => 10,
        "italic" => 1
      )
    );

    $this->_rx_formats["group"] = &$this->addFormat(
      array("fontfamily" => "Arial", "size" => 12, "align" => "left", "bold" => 1)
    );

    $this->setCustomColor(11, 230, 230, 230);
    $this->_rx_formats["header"] = &$this->addFormat(
      array(
        "fontfamily" => "Arial",
        "size" => 12,
        "align" => "center",
        "bold" => 1,
        "border" => 1,
        "fgcolor" => 11,
        "bordercolor" => "black"
      )
    );

    $this->_rx_formats["content"] = &$this->addFormat(
      array("fontfamily" => "Arial", "size" => 10, "border" => 1, "bordercolor" => "black", "align='left'")
    );

    $this->_rx_formats["currency"] = &$this->addFormat(
      array(
        "fontfamily" => "Arial",
        "size" => 10,
        "border" => 1,
        "bordercolor" => "black",
        "numFormat" => "#,##0.00;[RED]#,##0.00"
      )
    );

    // Formato para subtotales con fondo de color
    $this->setCustomColor(12, 255, 255, 200); // Color amarillo claro
    $this->_rx_formats["bg:amarillo"] = &$this->addFormat(
      array(
        "fontfamily" => "Arial",
        "size" => 12,
        "align" => "right",
        "bold" => 1,
        "border" => 1,
        "fgcolor" => 12,
        "bordercolor" => "black"
      )
    );
  }

  /**
   * Retorna el formato especificado
   */
  public function &getFormat($format)
  {
    return $this->_rx_formats[$format];
  }


  public function Put($field, $value, $align = '', $format = '')
  {
    if ($align != '') {
      $this->_rx_fields[$field]['align'] = $align;
    }

    // ✅ PHP 8 safe
    $_w = strlen((string)$value) * 1.25;

    if ($_w > $this->_rx_widthCol[$field]) {
      $this->_rx_widthCol[$field] = $_w;
    }

    // Guardar formato temporal si se especificó
    if ($format != '') {
      $this->_rx_row[$field] = array(
        'value' => $value,
        '_format' => $format
      );
    } else {
      $this->_rx_row[$field] = $value;
    }

    if (isset($this->_rx_fields[$field]['sum']) && $this->_rx_fields[$field]['sum'] == 'true') {
      if (!isset($this->_sum[$field])) {
        $this->_sum[$field] = 0;
      }
      $this->_sum[$field] += $value;
    }
  }


  /**
   * Envia una fila al reporte
   */
  public function OutputToReport($line = 1, $size = 9)
  {
    $col = 0;
    if ($size == 9) {
      $format = $this->_rx_formats["content"];
    } else {
      $format = $this->_rx_formats["subtitle"];
    }
    foreach (array_keys($this->_rx_fields) as $field) {
      if (count($this->_rx_row) > 0) {
        $fieldValue = $this->_rx_row[$field];

        // Extraer valor real si es array con formato
        if (is_array($fieldValue)) {
          $value = $fieldValue['value'] ?? '';
        } else {
          $value = $fieldValue;
        }

        $align = $this->_rx_fields[$field]["align"];
        if ($align == "R" && $value != '') {
          $type = "N";
          if (preg_match("/^(\-|)\d(\d|.\d)+(\,\d*)?$/", $value)) {
            $value = str_replace(array(".", ","), array("", "."), $value);
            $format = $this->_rx_formats["currency"];
          }
          if (preg_match("/^(\-|)\d+(\.\d*)$/", $value)) {
            if (isset($this->_rx_fields[$field]['numberFormat'])) {
              //$value = number_format($value,2,',','.');
            }
            $format = $this->_rx_formats["currency"];
          }
        } else {
          $type = "S";
        }
        switch ($align) {
          case 'L':
            $align = '1';
            break;
          case 'C':
            $align = '2';
            break;
          case 'R':
            $align = '3';
            break;
        }

        // Verificar si se especificó un formato personalizado para este campo en esta fila
        if (is_array($fieldValue) && isset($fieldValue['_format']) && $fieldValue['_format'] != '') {
          $format = $this->_rx_formats[$fieldValue['_format']];
        }

        $format->_text_h_align = $align;
        $this->writeCell($this->_rx_num_row, $col++, $value, $format, $type);
      }
    }
    $this->_rx_num_row++;
  }

  /**
   * Retorna el valor de la fila actual
   */
  public function getCurrentRow()
  {
    return $this->_rx_num_row;
  }

  /**
   * Selecciona un campo, para agrupar, y asi dividir el reporte por resultados
   */
  public function Group($field, $group)
  {
    $this->_rx_groups[$field] = array_merge($group, array("currentGroup" => "?", "currentMsg" => ""));
  }

  /**
   * Indica el grupo actual, sobre el cual se trabaja
   */
  public function SetGroup($group, $value)
  {
    if ($this->_rx_groups[$group]["currentGroup"] != $value) {
      if ($this->_rx_groups[$group]["currentGroup"] != "?") {
        if (isset($this->_rx_groups[$group]["afterGroup"])) {
          $function = array($this->_rx_groups[$group]["afterGroup"]["class"], $this->_rx_groups[$group]["afterGroup"]["callback"]);
          $params = array($this, $value);
          call_user_func_array($function, $params);
        }
        $this->Sum();
      }
      if (isset($this->_rx_groups[$group]["beforeGroup"])) {
        $this->_rx_num_row += 1;
        $_w = $this->_rx_num_row;
        $function = array($this->_rx_groups[$group]["beforeGroup"]["class"], $this->_rx_groups[$group]["beforeGroup"]["callback"]);
        $params = array($this, $value);
        $this->_rx_groups[$group]["currentMsg"] = call_user_func_array($function, $params);
        $this->_rx_num_row++;
        for ($i = $_w; $i < $this->_rx_num_row; $i++) {
          $this->_rx_worksheets[$this->_rx_current_worksheet]->setMerge($i, 0, $i, count($this->_rx_fields) - 1);
        }
        $col = 0;
        foreach ($this->_rx_fields as $field) {
          $this->writeCell($this->_rx_num_row, $col++, $field["header"], $this->_rx_formats["header"]);
        }
        $this->_rx_num_row++;
        $this->SumRe();
      }
      $this->_rx_groups[$group]["currentGroup"] = $value;
    }
  }

  /**
    funcion para reiniciar los totales al final del reporte
   * */
  public function SumRe()
  {
    if (isset($this->_sum) && count($this->_sum) > 0) {
      foreach (array_keys($this->_rx_fields) as $field) {
        if (array_key_exists($field, $this->_sum)) {
          $this->_sum[$field] = 0;
        }
      }
    }
  }

  /**
    funcion para colocar los totales al final del reporte
   * */
  public function Sum()
  {
    $col = 0;
    if (isset($this->_sum) && count($this->_sum) > 0) {
      $this->_rx_num_row++;
      foreach (array_keys($this->_rx_fields) as $field) {
        if (array_key_exists($field, $this->_sum)) {
          $type = 'N';
          $format = false;
          if (isset($this->_rx_fields[$field]["numberFormat"])) {
            $format = $this->_rx_fields[$field]["numberFormat"];
          }
          $value = 0;
          if (preg_match("/^(\-|)\d(\d|.\d)+(\,\d*)?$/", $this->_sum[$field])) {
            $value = str_replace(array(".", ","), array("", "."), $this->_sum[$field]);
          }
          if (preg_match("/^(\-|)\d+(\.\d*)$/", $this->_sum[$field])) {
            $value = $this->_sum[$field];
            if (isset($this->_rx_fields[$field]['numberFormat'])) {
              $value = number_format($value, 2, ',', '.');
            }
          }
          if ($format) {
            $this->writeCell($this->_rx_num_row, $col++, $value, $this->_rx_formats["currency"], $type);
          } else {
            $this->writeCell($this->_rx_num_row, $col++, $value, $this->_rx_formats["currency"], $type);
          }
        } else {
          if (isset($total)) {
            $this->writeCell($this->_rx_num_row, $col++, "", $this->_rx_formats["header"]);
          } else {
            if (count($this->_sum) > 1) {
              $total = "Totales: ";
            } else {
              $total = "Total: ";
            }
            $format = '';
            if (isset($this->_rx_fields[$field]["numberFormat"])) {
              $format = $this->_rx_fields[$field]["numberFormat"];
            }
            if ($format == 'true') {
              $this->writeCell($this->_rx_num_row, $col++, $total, $this->_rx_formats["header"]);
            } else {
              $this->writeCell($this->_rx_num_row, $col++, $total, $this->_rx_formats["header"]);
            }
          }
        }
      }
      $this->Ln();
    }
  }



  /**
   * Termina el reporte
   */
  public function FinishReport($file, $option = "D")
  {
    $this->Sum();
    // Verificar si hay un buffer activo antes de limpiarlo
    if (ob_get_level() > 0) {
      ob_end_clean();
    }
    $col = 0;
    foreach ($this->_rx_widthCol as $width) {
      $this->_rx_worksheets[$this->_rx_current_worksheet]->setColumn($col, $col++, $width);
    }
    if ($option == "D") {
      $this->send($file . ".xls");
    } else {
      $this->_filename = $file;
    }
    $this->close();
  }


  public function Cell($ancho = '', $alto = '', $text = '', $borde = '', $salto = 0, $align = 'L')
  {
    if ($text == '') {
      $this->_rx_acum_ancho += $ancho;
      return;
    }

    if ($this->_rx_acum_ancho != 0) {
      $acum = 0;
      foreach ($this->_rx_fields as $field) {
        $acum += $field['size'];
        $this->_rx_col_cell++;
        if ($acum >= $this->_rx_acum_ancho) {
          $this->writeCell(
            $this->_rx_num_row,
            $this->_rx_col_cell,
            '',
            $this->_rx_formats["group"]
          );
          break;
        }
      }
      $this->_rx_acum_ancho = 0;
    }

    $this->writeCell(
      $this->_rx_num_row,
      $this->_rx_col_cell,
      $text,
      $this->_rx_formats["header"]
    );

    if ($salto == 0) {
      $this->_rx_col_cell++;
    } else {
      $this->_rx_col_cell = 0;
      $this->_rx_num_row++;
    }
  }


  /**
   * Metodo que simula el metodo Ln() de FPDF
   */
  public function ln($rows = 1)
  {
    $this->_rx_col_cell = 0;
    $this->_rx_num_row += $rows;
  }

  public function afterColumn($fields, $width = "")
  {
    foreach ($fields as $field) {
      if ($width == "") {
        //$this->setX(($this->_tWidth)/2);
      }
      $r = $this->getCurrentRow();
      $f = $this->_rx_formats["group"];
      $this->writeCell($r, 0, $field['value'], $f, "S");
      $this->ln();
    }
  }
}
