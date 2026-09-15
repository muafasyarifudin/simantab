<?php
declare(strict_types=1);

final class LetterService {
    public static function content(array $d): array {
        $q = Database::connection()->prepare('SELECT content_json FROM document_versions WHERE document_id=? ORDER BY version_no DESC LIMIT 1');
        $q->execute([$d['id']]);
        $data = json_decode($q->fetchColumn() ?: '{}', true) ?: [];

        $q = Database::connection()->prepare('SELECT name,address,phone,email,website FROM institutions WHERE id=?');
        $q->execute([$d['institution_id']]);
        $i = $q->fetch() ?: [];

        $defaults = [
            'institution' => $i['name'] ?? 'UNIVERSITAS MUHAMMADIYAH PONOROGO',
            'unit' => $d['unit_name'] ?? 'Lembaga Pengembangan Sistem Informasi',
            'address' => $i['address'] ?? 'Jl. Budi Utomo No. 10, Ronowijayan, Kec. Siman, Kab. Ponorogo, Jawa Timur 63471',
            'contact' => implode(' | ', array_filter([$i['phone'] ?? 'Telp. (0352) 481124', $i['email'] ?? 'info@umpo.ac.id', $i['website'] ?? 'www.umpo.ac.id'])),
            'heading' => strtoupper($d['type_name'] ?? 'SURAT RESMI'),
            'heading_size' => '14pt',
            'kop_align' => 'center',
            'kop_line_style' => 'double',
            'kop_line_weight' => 'normal',
            'kop_spacing' => 'normal',
            'kop_inst1_size' => '14pt',
            'kop_inst2_size' => '12pt',
            'kop_address_size' => '9pt',
            'kop_contact_size' => '8.5pt',
            'logo_align' => 'left',
            'logo_size' => 'medium',
            'font_family' => 'serif',
            'paper_margin' => 'normal',
            'custom_number' => $d['number'] ?? '',
            'confidentiality' => $d['confidentiality'] ?? 'Biasa',
            'attachment' => '-',
            'title' => $d['title'] ?? '',
            'meta_font_size' => '11pt',
            'date_size' => '11pt',
            'recipient' => "Yth. Pimpinan / Mitra Terkait\ndi Tempat",
            'recipient_size' => '11pt',
            'greeting_opening' => 'Dengan hormat,',
            'greeting_opening_size' => '11.5pt',
            'opening' => 'Sehubungan dengan agenda operasional dan koordinasi kelembagaan, bersama surat ini kami sampaikan hal-hal sebagai berikut:',
            'opening_size' => '11.5pt',
            'body' => $d['summary'] ?? "1. Pelaksanaan kegiatan koordinasi dan pemantauan sistem.\n2. Dimohon kehadiran dan kesiapan pihak terkait sesuai jadwal yang telah ditentukan.",
            'body_size' => '11.5pt',
            'body_line_height' => '1.6',
            'paragraph_spacing' => 'normal',
            'closing' => 'Demikian surat ini kami sampaikan, atas perhatian dan kerja sama yang baik kami ucapkan terima kasih.',
            'closing_size' => '11.5pt',
            'greeting_closing' => 'Hormat kami,',
            'greeting_closing_size' => '11.5pt',
            'city' => 'Ponorogo',
            'date' => $d['document_date'] ?? date('Y-m-d'),
            'signer_position' => 'Kepala LPSI',
            'signer_position_size' => '11.5pt',
            'signer_name' => 'Ahmad Fauzi, M.Kom.',
            'signer_name_size' => '12pt',
            'signer_number' => 'NIDN. 0712058801',
            'signer_number_size' => '10pt',
            'signature_type' => 'image',
            'signature_size' => 'medium',
            'signature_space_height' => '80px',
            'stamp_position' => 'left',
            'stamp_size' => 'medium',
            'stamp_opacity' => '85',
            'copies' => "1. Rektor (sebagai laporan)\n2. Wakil Rektor Terkait\n3. Arsip",
            'copies_size' => '10pt',
            'footer_note' => 'Dokumen resmi diterbitkan melalui SIMANTAP · Universitas Muhammadiyah Ponorogo',
            'footer_size' => '8.5pt',
            'logo' => '',
            'logo_right' => '',
            'signature' => '',
            'stamp' => ''
        ];

        return array_replace($defaults, $data['letter'] ?? []);
    }

    public static function saveBase64Image(string $value, string $prefix = 'img'): string {
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        if (str_starts_with($value, 'assets/')) {
            return $value;
        }
        if (preg_match('#^data:image/([a-zA-Z0-9\+\-\.]+);base64,(.+)$#s', $value, $m)) {
            $ext = strtolower($m[1]);
            if ($ext === 'jpeg') $ext = 'jpg';
            if ($ext === 'svg+xml') $ext = 'svg';
            if (!in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg'], true)) {
                $ext = 'png';
            }
            $binary = base64_decode(preg_replace('/\s+/', '', $m[2]));
            if ($binary === false || strlen($binary) === 0) {
                return '';
            }
            $dir = dirname(__DIR__, 2) . '/assets/uploads/letters';
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
            $filename = $prefix . '_' . bin2hex(random_bytes(10)) . '.' . $ext;
            $filepath = $dir . '/' . $filename;
            if (@file_put_contents($filepath, $binary) !== false) {
                return 'assets/uploads/letters/' . $filename;
            }
        }
        return '';
    }

    public static function image(string $value, bool $pdf): string {
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        if (str_starts_with($value, 'assets/')) {
            if ($pdf) {
                $fullPath = dirname(__DIR__, 2) . '/' . $value;
                if (is_file($fullPath)) {
                    return '@' . base64_encode(file_get_contents($fullPath));
                }
            }
            return $value;
        }
        if (preg_match('#^data:image/([a-zA-Z0-9\+\-\.]+);base64,(.+)$#s', $value, $m)) {
            $rawB64 = preg_replace('/\s+/', '', $m[2]);
            return $pdf ? '@' . $rawB64 : ('data:image/' . $m[1] . ';base64,' . $rawB64);
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        return '';
    }

    public static function html(array $d, bool $pdf = false): string {
        $v = self::content($d);
        $e = static fn($x) => htmlspecialchars((string)$x, ENT_QUOTES, 'UTF-8');
        $lines = static fn($x) => nl2br($e($x));

        $logo = self::image($v['logo'] ?? '', $pdf);
        $logoRight = self::image($v['logo_right'] ?? '', $pdf);
        $signature = self::image($v['signature'] ?? '', $pdf);
        $stamp = self::image($v['stamp'] ?? '', $pdf);

        $fontStyle = ($v['font_family'] ?? 'serif') === 'sans' 
            ? 'font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;' 
            : 'font-family: "Times New Roman", Times, Georgia, serif;';

        $logoPx = match($v['logo_size'] ?? 'medium') {
            'small' => 56,
            'large' => 88,
            'xlarge' => 104,
            default => 72
        };

        $lineWeight = match($v['kop_line_weight'] ?? 'normal') {
            'thin' => '1.5px',
            'thick' => '4.5px',
            default => '3px'
        };

        $kopSpacing = match($v['kop_spacing'] ?? 'normal') {
            'compact' => '2px 0 10px',
            'spacious' => '10px 0 22px',
            default => '6px 0 16px'
        };

        $lineHtml = match($v['kop_line_style'] ?? 'double') {
            'single_thick' => '<div style="border-top:' . $lineWeight . ' solid #000;margin:' . $kopSpacing . ';"></div>',
            'single_thin' => '<div style="border-top:1px solid #000;margin:' . $kopSpacing . ';"></div>',
            'dashed' => '<div style="border-top:1.5px dashed #000;margin:' . $kopSpacing . ';"></div>',
            'none' => '<div style="margin:12px 0;"></div>',
            default => '<div style="border-top:' . $lineWeight . ' solid #000;border-bottom:1px solid #000;height:2px;margin:' . $kopSpacing . ';"></div>'
        };

        $align = ($v['kop_align'] ?? 'center') === 'left' ? 'left' : 'center';

        $paperPadding = match($v['paper_margin'] ?? 'normal') {
            'compact' => 'padding: 24px 30px;',
            'spacious' => 'padding: 52px 58px;',
            default => 'padding: 42px 48px;'
        };

        // KOP Header Table
        $html = '<div style="' . $fontStyle . $paperPadding . 'line-height:1.45;color:#111;font-size:12pt;background:#fff;box-sizing:border-box;">';
        $html .= '<table width="100%" cellpadding="2" style="border-collapse:collapse;margin-bottom:4px;"><tr>';
        
        if ($logo && ($v['logo_align'] ?? 'left') !== 'none') {
            $html .= '<td width="' . ($logoPx + 16) . 'px" align="center" valign="middle">';
            $html .= '<img src="' . $logo . '" width="' . $logoPx . '" style="max-height:' . $logoPx . 'px;object-fit:contain;">';
            $html .= '</td>';
        }

        $inst1Size = $e($v['kop_inst1_size'] ?? '14pt');
        $inst2Size = $e($v['kop_inst2_size'] ?? '12pt');
        $addrSize = $e($v['kop_address_size'] ?? '9pt');
        $contactSize = $e($v['kop_contact_size'] ?? '8.5pt');

        $html .= '<td align="' . $align . '" valign="middle" style="padding:0 8px;">';
        $html .= '<div style="font-size:' . $inst1Size . ';font-weight:bold;text-transform:uppercase;line-height:1.2;">' . $e($v['institution']) . '</div>';
        if (!empty($v['unit'])) {
            $html .= '<div style="font-size:' . $inst2Size . ';font-weight:bold;text-transform:uppercase;line-height:1.25;margin-top:2px;">' . $e($v['unit']) . '</div>';
        }
        $html .= '<div style="font-size:' . $addrSize . ';margin-top:3px;line-height:1.3;">' . $lines($v['address']) . '</div>';
        if (!empty($v['contact'])) {
            $html .= '<div style="font-size:' . $contactSize . ';margin-top:2px;">' . $e($v['contact']) . '</div>';
        }
        $html .= '</td>';

        if ($logoRight && ($v['logo_align'] ?? '') === 'both') {
            $html .= '<td width="' . ($logoPx + 16) . 'px" align="center" valign="middle">';
            $html .= '<img src="' . $logoRight . '" width="' . $logoPx . '" style="max-height:' . $logoPx . 'px;object-fit:contain;">';
            $html .= '</td>';
        }

        $html .= '</tr></table>';
        $html .= $lineHtml;

        // Surat Metadata (Nomor, Sifat, Lampiran, Hal) & Tanggal
        $docNumber = !empty($v['custom_number']) ? $v['custom_number'] : ($d['number'] ?: 'DRAF — belum bernomor');
        $dateStr = ($v['city'] ? $e($v['city']) . ', ' : '') . $e($v['date']);

        $metaSize = $e($v['meta_font_size'] ?? '11pt');
        $dateSize = $e($v['date_size'] ?? '11pt');
        $recipientSize = $e($v['recipient_size'] ?? '11pt');

        $html .= '<table width="100%" cellpadding="2" style="border-collapse:collapse;margin-bottom:14px;font-size:' . $metaSize . ';"><tr>';
        $html .= '<td width="60%" valign="top">';
        $html .= '<table cellpadding="1" style="border-collapse:collapse;width:100%;">';
        $html .= '<tr><td width="70px">Nomor</td><td width="12px">:</td><td>' . $e($docNumber) . '</td></tr>';
        $html .= '<tr><td>Sifat</td><td>:</td><td>' . $e($v['confidentiality'] ?? 'Biasa') . '</td></tr>';
        $html .= '<tr><td>Lampiran</td><td>:</td><td>' . $e($v['attachment'] ?? '-') . '</td></tr>';
        $html .= '<tr><td>Perihal</td><td>:</td><td><strong>' . $e($v['title'] ?: ($d['title'] ?? '')) . '</strong></td></tr>';
        $html .= '</table>';
        $html .= '</td>';
        
        $html .= '<td width="40%" align="right" valign="top">';
        $html .= '<div style="font-size:' . $dateSize . ';">' . $dateStr . '</div><br>';
        $html .= '<div style="text-align:left;display:inline-block;font-size:' . $recipientSize . ';">Kepada Yth.<br>' . $lines($v['recipient']) . '</div>';
        $html .= '</td>';
        $html .= '</tr></table>';

        $greetingOpenSize = $e($v['greeting_opening_size'] ?? '11.5pt');
        $openSize = $e($v['opening_size'] ?? '11.5pt');
        $bodySize = $e($v['body_size'] ?? '11.5pt');
        $bodyLineHeight = $e($v['body_line_height'] ?? '1.6');
        $pSpacing = match($v['paragraph_spacing'] ?? 'normal') {
            'compact' => '6px',
            'relaxed' => '16px',
            default => '10px'
        };
        $closingSize = $e($v['closing_size'] ?? '11.5pt');
        $greetingCloseSize = $e($v['greeting_closing_size'] ?? '11.5pt');

        // Salam Pembuka & Paragraf Pembuka
        if (!empty($v['greeting_opening'])) {
            $html .= '<p style="margin:12px 0 6px 0;font-size:' . $greetingOpenSize . ';">' . $e($v['greeting_opening']) . '</p>';
        }
        if (!empty($v['opening'])) {
            $html .= '<p style="text-align:justify;line-height:' . $bodyLineHeight . ';margin:0 0 ' . $pSpacing . ' 0;font-size:' . $openSize . ';">' . $lines($v['opening']) . '</p>';
        }

        // Isi Dokumen
        if (!empty($v['body'])) {
            $html .= '<div style="text-align:justify;line-height:' . $bodyLineHeight . ';margin:0 0 ' . $pSpacing . ' 0;font-size:' . $bodySize . ';">' . $lines($v['body']) . '</div>';
        }

        // Paragraf Penutup & Salam Penutup
        if (!empty($v['closing'])) {
            $html .= '<p style="text-align:justify;line-height:' . $bodyLineHeight . ';margin:0 0 ' . $pSpacing . ' 0;font-size:' . $closingSize . ';">' . $lines($v['closing']) . '</p>';
        }
        if (!empty($v['greeting_closing'])) {
            $html .= '<p style="margin:8px 0 16px 0;font-size:' . $greetingCloseSize . ';">' . $e($v['greeting_closing']) . '</p>';
        }

        // Tanda Tangan & Cap Stempel
        $signPosSize = $e($v['signer_position_size'] ?? '11.5pt');
        $signNameSize = $e($v['signer_name_size'] ?? '12pt');
        $signNipSize = $e($v['signer_number_size'] ?? '10pt');
        $signHeight = match($v['signature_space_height'] ?? '80px') {
            '45px' => '45px',
            '65px' => '65px',
            '100px' => '100px',
            '120px' => '120px',
            default => '80px'
        };
        $signImgHeight = match($v['signature_size'] ?? 'medium') {
            'small' => '50px',
            'large' => '90px',
            'xlarge' => '110px',
            default => '70px'
        };
        $stampImgDim = match($v['stamp_size'] ?? 'medium') {
            'small' => '65px',
            'large' => '105px',
            'xlarge' => '125px',
            default => '85px'
        };

        $html .= '<table width="100%" cellpadding="2" style="border-collapse:collapse;margin-top:20px;"><tr>';
        $html .= '<td width="55%"></td>';
        $html .= '<td width="45%" align="center" style="font-size:' . $signPosSize . ';">';
        $html .= '<div>' . $e($v['signer_position']) . '</div>';
        
        $html .= '<div style="position:relative;min-height:' . $signHeight . ';margin:6px 0;display:flex;align-items:center;justify-content:center;">';
        if ($signature) {
            $html .= '<img src="' . $signature . '" style="max-height:' . $signImgHeight . ';max-width:160px;object-fit:contain;vertical-align:middle;">';
        } else {
            $html .= '<div style="height:' . $signHeight . ';"></div>';
        }

        if ($stamp) {
            $stampPosStyle = match($v['stamp_position'] ?? 'left') {
                'right' => 'right:0px;',
                'center' => 'left:50%;transform:translateX(-50%);',
                default => 'left:0px;'
            };
            $opacity = ((int)($v['stamp_opacity'] ?? 85)) / 100;
            $html .= ' <img src="' . $stamp . '" style="position:absolute;' . $stampPosStyle . 'max-width:' . $stampImgDim . ';max-height:' . $stampImgDim . ';opacity:' . $opacity . ';vertical-align:middle;">';
        }
        $html .= '</div>';

        $html .= '<div style="font-weight:bold;text-decoration:underline;margin-top:4px;font-size:' . $signNameSize . ';">' . $e($v['signer_name']) . '</div>';
        if (!empty($v['signer_number'])) {
            $html .= '<div style="font-size:' . $signNipSize . ';">' . $e($v['signer_number']) . '</div>';
        }
        $html .= '</td></tr></table>';

        // Tembusan
        if (!empty($v['copies'])) {
            $copiesSize = $e($v['copies_size'] ?? '10pt');
            $html .= '<div style="margin-top:24px;font-size:' . $copiesSize . ';line-height:1.4;">';
            $html .= '<strong><u>Tembusan:</u></strong><br>' . $lines($v['copies']);
            $html .= '</div>';
        }

        // Footer Note
        if (!empty($v['footer_note'])) {
            $footerSize = $e($v['footer_size'] ?? '8.5pt');
            $html .= '<div style="margin-top:30px;padding-top:6px;border-top:0.5px solid #ccc;font-size:' . $footerSize . ';color:#666;font-style:italic;">';
            $html .= $e($v['footer_note']);
            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }

    public static function upload(array $file, string $prefix = 'img'): string {
        if (($file['error'] ?? 4) !== 0 || $file['size'] > 5 * 1024 * 1024) {
            throw new InvalidArgumentException('Gambar harus PNG/JPG dan maksimum 5 MB.');
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new InvalidArgumentException('Upload tidak valid.');
        }
        $info = getimagesize($file['tmp_name']);
        if (!$info || !in_array($info['mime'], ['image/png', 'image/jpeg', 'image/webp'], true) || $info[0] > 4000 || $info[1] > 4000) {
            throw new InvalidArgumentException('Gambar harus berupa PNG/JPG dengan dimensi maksimum 4000 piksel.');
        }
        $ext = match($info['mime']) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg'
        };
        $dir = dirname(__DIR__, 2) . '/assets/uploads/letters';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $filename = $prefix . '_' . bin2hex(random_bytes(10)) . '.' . $ext;
        $filepath = $dir . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return 'assets/uploads/letters/' . $filename;
        }
        return '';
    }
}
