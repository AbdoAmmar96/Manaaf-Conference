<?php

namespace App\Support;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class Qr
{
    public static function png(string $data, int $size = 480): string
    {
        /*
         * مستوى Medium لا High عمدًا: الحمولة ٦٦ حرفًا، ومع High يصير الرمز
         * ٤٩×٤٩ وحدة — أي ٤.٩ بكسل للوحدة فقط عند عرضه ٢٤٠px على الشاشة،
         * وهذا تحت الحد العملي لمسحه بكاميرا جوال آخر. Medium يخفضه إلى
         * ٣٧×٣٧ (٦.٥ بكسل/وحدة) ويظل احتياطي التصحيح ١٥٪ وهو أكثر من كافٍ
         * لرمز يُعرض على شاشة أو يُطبع على بطاقة نظيفة.
         */
        $builder = new Builder(
            writer: new PngWriter(),
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $size,
            margin: 16,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        return $builder->build()->getString();
    }
}
