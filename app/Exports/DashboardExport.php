<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DashboardExport implements WithEvents, WithTitle
{
    protected $dados;

    public function __construct($dados)
    {
        $this->dados = $dados;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                /** @var Worksheet $sheet */
                $sheet = $event->sheet->getDelegate();

                // Logo (ajuste o caminho correto)
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo PsiGestor');
                $drawing->setPath(public_path('images/logo-psigestor.png'));
                $drawing->setHeight(55);
                $drawing->setCoordinates('A1');
                $drawing->setWorksheet($sheet);

                // Título
                $sheet->mergeCells('B1:D1');
                $sheet->setCellValue('B1', 'Relatório de Sessões - PsiGestor');
                $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('1F4E79');
                $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);

                // Cabeçalho Resumo
                $row = 4;
                $sheet->setCellValue("A{$row}", 'Período:');
                $sheet->setCellValue("B{$row}", $this->dados['dataInicial']->format('d/m/Y') . ' a ' . $this->dados['dataFinal']->format('d/m/Y'));

                $row++;
                $sheet->setCellValue("A{$row}", 'Total de Sessões:');
                $sheet->setCellValue("B{$row}", $this->dados['totais']['sessoes'] ?? 0);

                $row++;
                $sheet->setCellValue("A{$row}", 'Valor Recebido:');
                $sheet->setCellValue("B{$row}", 'R$ ' . number_format($this->dados['valores']['total'] ?? 0, 2, ',', '.'));

                $row++;
                $sheet->setCellValue("A{$row}", 'Sessões Hoje:');
                $sheet->setCellValue("B{$row}", $this->dados['sessoesHoje'] ?? 0);

                $row++;
                $sheet->setCellValue("A{$row}", 'Pendências (Não Pagos):');
                $sheet->setCellValue("B{$row}", $this->dados['pendencias'] ?? 0);

                // Estilo para labels (coluna A)
                $sheet->getStyle("A4:A{$row}")->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('333333');

                // Estilo para valores (coluna B)
                $sheet->getStyle("B4:B{$row}")->getFont()->setSize(12)->getColor()->setRGB('333333');

                // Alinhamento vertical central nas células de dados
                $sheet->getStyle("A4:B{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Bordas leves em volta dos dados
                $sheet->getStyle("A4:B{$row}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'DDDDDD'],
                        ],
                    ],
                ]);

                // Fundo branco levemente acinzentado para o bloco principal
                $sheet->getStyle("A4:B{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F9F9F9');

                // Ajuste de colunas
                foreach (['A', 'B'] as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }

    public function title(): string
    {
        return 'Relatório';
    }
}
