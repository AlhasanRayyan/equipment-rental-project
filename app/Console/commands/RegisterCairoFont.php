<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RegisterCairoFont extends Command
{
    protected $signature = 'font:register-cairo';
    protected $description = 'Register Cairo font for dompdf';

    public function handle()
    {
        $fontDir = public_path('fonts');
        $fontPath = $fontDir . '/Cairo.ttf';

        if (!file_exists($fontPath)) {
            $this->error('Cairo.ttf not found in public/fonts');
            return;
        }

        $options = new \Dompdf\Options();
        $options->setFontDir($fontDir);
        $options->setFontCache($fontDir);

        $dompdf = new \Dompdf\Dompdf($options);
        $canvas = $dompdf->getCanvas();
        $fontMetrics = new \Dompdf\FontMetrics($canvas, $options);

        $result = $fontMetrics->registerFont(
            ['family' => 'Cairo', 'style' => 'normal', 'weight' => 'normal'],
            $fontPath
        );

        if ($result) {
            $this->info('Cairo font registered successfully!');
        } else {
            $this->error('Failed to register Cairo font.');
        }
    }
}