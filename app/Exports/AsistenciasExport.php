<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class AsistenciasExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithCustomStartCell
{
    protected $data;
    protected $titulo;

    public function __construct(array $data, $titulo)
    {
        $this->data = $data;
        $this->titulo = $titulo;
    }

    public function collection()
    {
        return collect($this->data);
    }

    // Empezamos en la celda A4 para dejar espacio a los títulos de arriba
    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        if (empty($this->data)) return [];
        
        // Obtenemos las llaves (nombres de columnas) para los títulos de la tabla
        $columnas = array_keys($this->data[0]);
        return [
            array_map(fn($col) => strtoupper(str_replace('_', ' ', $col)), $columnas)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Colocamos el Título Principal 
        $sheet->setCellValue('A1', 'CONCENTRADO DE ASISTENCIA - MENSUAL');
        $sheet->mergeCells('A1:E1');

        // 2. Colocamos la información del Taller 
        $sheet->setCellValue('A2', $this->titulo);
        $sheet->mergeCells('A2:E2');

        // 3. Estilos de los Títulos (A1 y A2)
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        $sheet->getStyle('A2')->getFont()->setBold(false)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        // 4. Estilo de la cabecera de la tabla (Fila 4) 
        $lastColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A4:{$lastColumn}4")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A4:{$lastColumn}4")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('1B5E20'); // Verde UTVT

        // 5. Bordes para los datos 
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A4:{$lastColumn}{$lastRow}")->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [];
    }
}