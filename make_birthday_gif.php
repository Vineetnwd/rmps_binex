<?php
/**
 * make_birthday_gif.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Pure-PHP animated GIF generator for birthday cards.
 * Requires: GD extension (php-gd) — no ImageMagick, no Python, no FFmpeg.
 *
 * Included by birthday_card.php when ?download_gif=1 is requested,
 * OR called directly from CLI:
 *   php make_birthday_gif.php --name "Sarah Johnson" --photo path/to/photo.jpg --out card.gif
 *
 * How it works
 * ────────────
 *  1. Renders N_FRAMES individual true-colour GD frames (GIF palette, 256 colours each).
 *  2. Stitches them together with the tiny built-in AnimatedGifEncoder class
 *     (pure PHP — no Imagick needed) to produce a looping GIF.
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ── Config ────────────────────────────────────────────────────────────────────
define('GIF_W',      1080);
define('GIF_H',      1080);
define('GIF_FPS',    12);          // frames/sec  (lower = smaller file)
define('GIF_SECS',   5);           // loop length in seconds
define('N_FRAMES',   GIF_FPS * GIF_SECS);
define('DELAY_CS',   intval(100 / GIF_FPS)); // centiseconds per frame

// ── Colour palette (RGB arrays) ───────────────────────────────────────────────
define('C_DEEP',   [13,  6,  32]);
define('C_DEEP2',  [31, 13,  58]);
define('C_GOLD',   [232,201,126]);
define('C_ROSE',   [242,167,179]);
define('C_PURPLE', [176,132,245]);
define('C_CREAM',  [253,246,238]);

// ── Helpers ───────────────────────────────────────────────────────────────────
function lerpC(array $a, array $b, float $t): array {
    return [
        (int)($a[0] + ($b[0]-$a[0])*$t),
        (int)($a[1] + ($b[1]-$a[1])*$t),
        (int)($a[2] + ($b[2]-$a[2])*$t),
    ];
}
function easeOut(float $t): float { return 1-(1-$t)**3; }
function easeInOut(float $t): float { return $t*$t*(3-2*$t); }

function alloc(GdImage $im, array $rgb): int {
    return imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
}
function allocAlpha(GdImage $im, array $rgb, int $a127): int {
    return imagecolorallocatealpha($im, $rgb[0], $rgb[1], $rgb[2], $a127);
}

// ── Star data (generated once, reused every frame) ────────────────────────────
function make_stars(int $n = 110, int $seed = 42): array {
    mt_srand($seed);
    $stars = [];
    for ($i = 0; $i < $n; $i++) {
        $stars[] = [
            mt_rand(0, GIF_W-1),   // x
            mt_rand(0, GIF_H-1),   // y
            mt_rand(5,25)/10,       // radius
            mt_rand(0,100)/100,     // phase
        ];
    }
    return $stars;
}

// ── Confetti data ─────────────────────────────────────────────────────────────
function make_confetti(int $n = 50, int $seed = 7): array {
    mt_srand($seed);
    $colors = [C_GOLD, C_ROSE, C_PURPLE, [126,212,232], [138,212,154], C_CREAM];
    $pieces = [];
    for ($i = 0; $i < $n; $i++) {
        $pieces[] = [
            mt_rand(0, GIF_W-1),          // x
            mt_rand(0, GIF_H-1),          // y start
            mt_rand(6,18)/100,             // speed (fraction of height per loop)
            $colors[array_rand($colors)],  // colour
            mt_rand(6,13),                 // width
            mt_rand(6,13),                 // height
            mt_rand(0,100)/100,            // phase
        ];
    }
    return $pieces;
}

// ── Draw a filled, anti-aliased circle ───────────────────────────────────────
// GD's imagefilledellipse is already decent; we wrap it.
function draw_circle(GdImage $im, int $cx, int $cy, int $r, array $rgb): void {
    $c = alloc($im, $rgb);
    imagefilledellipse($im, $cx, $cy, $r*2, $r*2, $c);
}

// ── Gradient background ───────────────────────────────────────────────────────
function draw_bg(GdImage $im): void {
    $w = GIF_W; $h = GIF_H;
    // Flat deep fill
    $bg = alloc($im, C_DEEP);
    imagefilledrectangle($im, 0, 0, $w-1, $h-1, $bg);
    // Radial gradient from centre
    $cx = $w/2; $cy = $h/2;
    $maxR = (int)($w * 0.72);
    for ($r = $maxR; $r > 0; $r -= 3) {
        $t = $r / $maxR;
        $c = lerpC(C_DEEP2, C_DEEP, easeInOut($t));
        $col = alloc($im, $c);
        imagefilledellipse($im, (int)$cx, (int)$cy, $r*2, $r*2, $col);
    }
}

// ── Soft glow blob ────────────────────────────────────────────────────────────
function draw_glow(GdImage $im, int $cx, int $cy, int $r, array $rgb, float $strength): void {
    for ($i = $r; $i > 0; $i -= 4) {
        $t = 1 - $i/$r;
        $a127 = (int)(120 * $strength * ($t**1.5));  // 0=opaque,127=transparent in GD
        $a127 = min(127, max(0, 127 - $a127));
        $col = allocAlpha($im, $rgb, $a127);
        imagefilledellipse($im, $cx, $cy, $i*2, $i*2, $col);
    }
}

// ── Stars ─────────────────────────────────────────────────────────────────────
function draw_stars(GdImage $im, array $stars, float $t): void {
    foreach ($stars as [$x,$y,$r,$phase]) {
        $bright = 0.25 + 0.75 * abs(sin(($t + $phase) * M_PI));
        $v = (int)(255 * $bright);
        $col = alloc($im, [$v,$v,$v]);
        $ri = max(1, (int)$r);
        imagefilledellipse($im, (int)$x, (int)$y, $ri*2, $ri*2, $col);
    }
}

// ── Rounded rectangle (GD 8.0 has imageroundedrectangle, older needs manual) ──
function filled_rounded_rect(GdImage $im, int $x1, int $y1, int $x2, int $y2,
                              int $rx, array $rgb): void {
    if (function_exists('imagefilledroundedrectangle')) {
        /** @phpstan-ignore-next-line */
        imagefilledroundedrectangle($im, $x1, $y1, $x2, $y2, $rx, alloc($im,$rgb));
        return;
    }
    $c = alloc($im, $rgb);
    imagefilledrectangle($im, $x1+$rx, $y1,    $x2-$rx, $y2,    $c);
    imagefilledrectangle($im, $x1,    $y1+$rx, $x2,    $y2-$rx, $c);
    imagefilledellipse($im, $x1+$rx, $y1+$rx, $rx*2, $rx*2, $c);
    imagefilledellipse($im, $x2-$rx, $y1+$rx, $rx*2, $rx*2, $c);
    imagefilledellipse($im, $x1+$rx, $y2-$rx, $rx*2, $rx*2, $c);
    imagefilledellipse($im, $x2-$rx, $y2-$rx, $rx*2, $rx*2, $c);
}

// ── Card panel ────────────────────────────────────────────────────────────────
function draw_card_panel(GdImage $im): void {
    $m = 70;
    // dark translucent panel — layered rectangles (GD can't do real alpha blend
    // in palette mode, so we paint the panel colour directly)
    $panel = lerpC(C_DEEP2, C_DEEP, 0.4);
    filled_rounded_rect($im, $m, $m, GIF_W-$m, GIF_H-$m, 38, $panel);
    // gold border
    $border = C_GOLD;
    $bc = alloc($im, $border);
    // top/bottom/left/right lines (thin)
    imagerectangle($im, $m, $m, GIF_W-$m, GIF_H-$m, $bc);
}

// ── Spinning photo ring ────────────────────────────────────────────────────────
function draw_photo_ring(GdImage $im, GdImage|null $photo,
                         int $cx, int $cy, float $angle, string $name): void {
    $ring_r = 165;   // outer radius
    $border  = 10;   // ring thickness
    $photo_r = $ring_r - $border - 4;

    // Conic gradient ring (N segments)
    $n_seg = 120;
    for ($i = 0; $i < $n_seg; $i++) {
        $frac = $i / $n_seg;
        $a_s  = $angle + $frac * 360;
        $a_e  = $angle + ($frac + 1/$n_seg) * 360;
        if ($frac < 0.33)
            $seg_c = lerpC(C_GOLD,   C_ROSE,   $frac/0.33);
        elseif ($frac < 0.66)
            $seg_c = lerpC(C_ROSE,   C_PURPLE, ($frac-0.33)/0.33);
        else
            $seg_c = lerpC(C_PURPLE, C_GOLD,   ($frac-0.66)/0.34);
        $col = alloc($im, $seg_c);
        imagefilledarc($im, $cx, $cy, $ring_r*2, $ring_r*2,
                       (int)$a_s, (int)$a_e, $col, IMG_ARC_PIE);
    }

    // Dark "gap" circle (background colour)
    $inner = $ring_r - $border;
    $bg = alloc($im, C_DEEP);
    // use panel colour so it matches
    $panel = lerpC(C_DEEP2, C_DEEP, 0.4);
    $bgc = alloc($im, $panel);
    imagefilledellipse($im, $cx, $cy, $inner*2, $inner*2, $bgc);

    // Photo or initials
    $pd = ($photo_r - 3) * 2;   // photo diameter
    if ($photo !== null) {
        // Resize photo to circle diameter
        $pw = imagesx($photo); $ph = imagesy($photo);
        $small = imagecreatetruecolor($pd, $pd);
        imagecopyresampled($small, $photo, 0, 0, 0, 0, $pd, $pd, $pw, $ph);
        // Circular clip — draw row by row
        for ($py = 0; $py < $pd; $py++) {
            for ($px = 0; $px < $pd; $px++) {
                $dx = $px - $pd/2; $dy = $py - $pd/2;
                if ($dx*$dx + $dy*$dy <= ($pd/2)*($pd/2)) {
                    $rgb = imagecolorat($small, $px, $py);
                    $r = ($rgb>>16)&0xFF; $g=($rgb>>8)&0xFF; $b=$rgb&0xFF;
                    $col = alloc($im, [$r,$g,$b]);
                    imagesetpixel($im, $cx-$pd/2+$px, $cy-$pd/2+$py, $col);
                }
            }
        }
        imagedestroy($small);
    } else {
        // Initials circle
        $bcol = alloc($im, C_DEEP2);
        imagefilledellipse($im, $cx, $cy, $pd, $pd, $bcol);
        $gcol = alloc($im, C_GOLD);
        $font = 5; // largest built-in GD font
        $initials = implode('', array_map(fn($w)=>strtoupper($w[0]),
                    array_filter(explode(' ', $name))));
        $initials = substr($initials, 0, 2);
        $fw = imagefontwidth($font)*strlen($initials);
        $fh = imagefontheight($font);
        imagestring($im, $font, $cx-$fw/2, $cy-$fh/2, $initials, $gcol);
    }
}

// ── Candles ───────────────────────────────────────────────────────────────────
function draw_candles(GdImage $im, float $t, int $base_y): void {
    $n       = 5;
    $spacing = 54;
    $cw      = 14; $ch = 40;
    $x0      = GIF_W/2 - (($n-1)*$spacing)/2;
    $bodies  = [C_ROSE,[126,180,212],[138,212,154],[212,184,138],C_PURPLE];
    for ($i=0; $i<$n; $i++) {
        $cx  = (int)($x0 + $i*$spacing);
        $top = $base_y - $ch;
        // Body
        $bc = alloc($im, $bodies[$i]);
        imagefilledrectangle($im, $cx-$cw/2, $top, $cx+$cw/2, $base_y, $bc);
        // Highlight
        $hc = alloc($im, [255,255,255]);
        imagefilledrectangle($im, $cx-$cw/2+2, $top+4, $cx-$cw/2+4, $base_y-6, $hc);
        // Wick
        $wc = alloc($im, [80,60,40]);
        imageline($im, $cx, $top-8, $cx, $top, $wc);
        // Flame
        $phase  = $i * 0.4;
        $hflame = 22 + 5*sin($t*2*M_PI*1.8 + $phase);
        $wobble = (int)(4*sin($t*2*M_PI*2.3 + $phase + 1));
        $fy     = $top - 8;
        // Outer flame polygon
        $flame_pts = [
            $cx,             $fy-(int)$hflame,
            $cx+7+$wobble,   $fy-(int)($hflame*0.4),
            $cx+7,           $fy,
            $cx-7,           $fy,
            $cx-7-$wobble,   $fy-(int)($hflame*0.4),
        ];
        $fc = alloc($im, [255,200,60]);
        imagefilledpolygon($im, $flame_pts, $fc);
        // Inner flame
        $inner_pts = [
            $cx,             $fy-(int)($hflame*0.55),
            $cx+4,           $fy-(int)($hflame*0.22),
            $cx+4,           $fy,
            $cx-4,           $fy,
            $cx-4,           $fy-(int)($hflame*0.22),
        ];
        $ic = alloc($im, [255,240,140]);
        imagefilledpolygon($im, $inner_pts, $ic);
    }
}

// ── Centered text (GD built-in fonts, scale=2 for readability at 1080p) ───────
function draw_text_c(GdImage $im, string $text, int $y,
                     int $font_size, array $rgb, float $alpha_frac=1.0): void {
    if ($alpha_frac <= 0) return;
    // GD built-in fonts: 1-5. We scale by drawing on a bigger canvas then
    // downsampling — impractical. Instead use imagettftext with bundled font.
    // Fallback to imagestring with scale factor emulation.
    // We use imagettftext if a font file is available.
    static $fonts = null;
    if ($fonts === null) {
        $candidates = [
            '/usr/share/fonts/truetype/dejavu/DejaVuSerif-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSerif-Bold.ttf',
            '/usr/share/fonts/truetype/freefont/FreeSerifBold.ttf',
        ];
        $fonts = ['bold'=>null,'body'=>null];
        foreach ($candidates as $f) {
            if (file_exists($f)) { $fonts['bold'] = $f; break; }
        }
        $body_candidates = [
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        ];
        foreach ($body_candidates as $f) {
            if (file_exists($f)) { $fonts['body'] = $f; break; }
        }
    }
    $fpath = ($font_size >= 52) ? ($fonts['bold'] ?? $fonts['body'])
                                 : ($fonts['body'] ?? $fonts['bold']);
    $col   = alloc($im, $rgb);
    if ($fpath && function_exists('imagettftext')) {
        $bbox = imagettfbbox($font_size, 0, $fpath, $text);
        $tw   = abs($bbox[4]-$bbox[0]);
        $x    = (GIF_W - $tw) / 2;
        // shadow
        $sc = alloc($im, [0,0,0]);
        imagettftext($im, $font_size, 0, (int)$x+2, $y+2, $sc, $fpath, $text);
        imagettftext($im, $font_size, 0, (int)$x,   $y,   $col, $fpath, $text);
    } else {
        // fallback GD built-in font
        $gf = max(1, min(5, (int)($font_size/14)));
        $tw = imagefontwidth($gf)*strlen($text);
        $x  = (GIF_W - $tw)/2;
        imagestring($im, $gf, (int)$x, $y, $text, $col);
    }
}

// ── Confetti ──────────────────────────────────────────────────────────────────
function draw_confetti(GdImage $im, array $pieces, float $t): void {
    foreach ($pieces as [$px,$py_start,$speed,$rgb,$pw,$ph,$phase]) {
        $y = (int)(($py_start + ($t+$phase)*$speed*GIF_H) % GIF_H);
        $x = (int)$px;
        $c = alloc($im, $rgb);
        imagefilledrectangle($im, $x-$pw/2, $y-$ph/2, $x+$pw/2, $y+$ph/2, $c);
    }
}

// ── Render one frame ──────────────────────────────────────────────────────────
function render_frame(int $fi, array $stars, array $confetti,
                      GdImage|null $photo, string $name): string {
    $t       = $fi / N_FRAMES;
    $reveal  = min(1.0, $fi / (GIF_FPS * 1.2));
    $W = GIF_W; $H = GIF_H;

    $im = imagecreate($W, $H);
    imagealphablending($im, true);

    // ── Background ────────────────────────────────────────────────────────────
    draw_bg($im);

    // ── Glows ─────────────────────────────────────────────────────────────────
    $pulse = 0.85 + 0.15*sin($t*2*M_PI);
    draw_glow($im, (int)(-50),    (int)(-50),    (int)(240*$pulse), C_ROSE,   0.5);
    draw_glow($im, (int)($W+50),  (int)($H+50),  (int)(200*$pulse), C_PURPLE, 0.4);
    draw_glow($im, (int)($W/2),   (int)($H-60),  (int)(160*$pulse), C_GOLD,   0.35);

    // ── Stars ─────────────────────────────────────────────────────────────────
    draw_stars($im, $stars, $t);

    // ── Card panel ────────────────────────────────────────────────────────────
    draw_card_panel($im);

    // ── Badge ─────────────────────────────────────────────────────────────────
    $badge_t = easeOut(min(1.0, $reveal * 3));
    if ($badge_t > 0.05) {
        draw_text_c($im, '✦  SPECIAL DAY  ✦', 110, 20, C_GOLD, $badge_t);
    }

    // ── Spinning photo ring ───────────────────────────────────────────────────
    $angle = $t * 360;
    $bob   = (int)(6*sin($t*2*M_PI));
    $ring_t = easeOut(min(1.0, $reveal * 2));
    if ($ring_t > 0.05) {
        draw_photo_ring($im, $photo, (int)($W/2), 330 + $bob, $angle, $name);
    }

    // ── "Happy Birthday" ─────────────────────────────────────────────────────
    $hb_t = easeOut(min(1.0, ($reveal-0.30)*2.5));
    if ($hb_t > 0.05) {
        draw_text_c($im, 'Happy Birthday', 510 + $bob/2, 42, C_GOLD, $hb_t);
    }

    // ── Name ─────────────────────────────────────────────────────────────────
    $name_t = easeOut(min(1.0, ($reveal-0.45)*3));
    if ($name_t > 0.05) {
        draw_text_c($im, $name, 568 + $bob/2, 62, C_CREAM, $name_t);
    }

    // ── Gold divider ─────────────────────────────────────────────────────────
    $div_t = easeOut(min(1.0, ($reveal-0.60)*4));
    if ($div_t > 0.05) {
        $div_y = 648;
        $div_w = (int)(260 * $div_t);
        $gc    = alloc($im, C_GOLD);
        imageline($im, (int)($W/2-$div_w/2), $div_y, (int)($W/2+$div_w/2), $div_y, $gc);
        imagefilledellipse($im, (int)($W/2), $div_y, 10, 10, $gc);
    }

    // ── Message ───────────────────────────────────────────────────────────────
    $msg_t = easeOut(min(1.0, ($reveal-0.70)*5));
    if ($msg_t > 0.05) {
        $lines = [
            'May this birthday bring you boundless joy,',
            'beautiful memories & every happiness',
            'your heart desires.',
        ];
        $my = 668;
        foreach ($lines as $line) {
            draw_text_c($im, $line, $my, 26, [200,190,220], $msg_t);
            $my += 38;
        }
    }

    // ── Candles ───────────────────────────────────────────────────────────────
    $candle_t = easeOut(min(1.0, ($reveal-0.80)*6));
    if ($candle_t > 0.05) {
        draw_candles($im, $t, $H - 115);
    }

    // ── Confetti ─────────────────────────────────────────────────────────────
    draw_confetti($im, $confetti, $t);

    // ── Capture frame as raw GIF bytes ────────────────────────────────────────
    ob_start();
    imagegif($im);
    $data = ob_get_clean();
    imagedestroy($im);
    return $data;
}

// ════════════════════════════════════════════════════════════════════════════════
// Minimal pure-PHP Animated GIF encoder
// Based on the public-domain GIFEncoder by László Zsidi
// (rewritten & simplified — no external dependencies)
// ════════════════════════════════════════════════════════════════════════════════
class AnimatedGif {
    private string $buffer = '';
    private bool   $first  = true;
    private int    $loop;
    private int    $delay;   // centiseconds

    public function __construct(int $loop = 0, int $delay_cs = 8) {
        $this->loop  = $loop;
        $this->delay = $delay_cs;
    }

    public function addFrame(string $gifData): void {
        // Strip GIF header (6 bytes) + Logical Screen Descriptor (7 bytes)
        // For the first frame keep the header; for subsequent frames strip it.
        if ($this->first) {
            // Write GIF89a header
            $this->buffer .= substr($gifData, 0, 6);   // GIF89a
            // Logical Screen Descriptor
            $this->buffer .= substr($gifData, 6, 7);
            // Netscape 2.0 loop extension
            $this->buffer .= "\x21\xFF\x0B" . 'NETSCAPE2.0' . "\x03\x01"
                           . pack('v', $this->loop) . "\x00";
            $this->first = false;
            $rest = substr($gifData, 13);
        } else {
            $rest = substr($gifData, 13);
        }

        // Find the image descriptor (0x2C) — skip any extensions in $rest
        // Prepend Graphic Control Extension with our delay
        $gce = "\x21\xF9\x04\x00" . pack('v', $this->delay) . "\x00\x00";

        // Find image start
        $pos = 0;
        while ($pos < strlen($rest)) {
            $byte = $rest[$pos];
            if ($byte === "\x2C") break;          // Image Descriptor
            if ($byte === "\x21") {               // Extension
                $pos++;
                $pos++;                            // extension label
                while (true) {
                    $size = ord($rest[$pos++]);
                    if ($size === 0) break;
                    $pos += $size;
                }
            } elseif ($byte === "\x3B") {         // Trailer
                return;
            } else {
                $pos++;
            }
        }

        $this->buffer .= $gce . substr($rest, $pos);

        // Remove trailing 0x3B (GIF trailer) from all but the last frame
        if (str_ends_with($this->buffer, "\x3B")) {
            $this->buffer = substr($this->buffer, 0, -1);
        }
    }

    public function get(): string {
        return $this->buffer . "\x3B";  // final trailer
    }

    public function save(string $path): bool {
        return (bool)file_put_contents($path, $this->get());
    }
}

// ════════════════════════════════════════════════════════════════════════════════
// Main entry — render all frames and stitch into GIF
// ════════════════════════════════════════════════════════════════════════════════
function generate_birthday_gif(string $name, ?string $photo_path, string $out): bool {
    if (!extension_loaded('gd')) {
        error_log('make_birthday_gif.php: GD extension not loaded');
        return false;
    }

    $stars    = make_stars();
    $confetti = make_confetti();

    // Load & crop photo to square
    $photo = null;
    if ($photo_path && file_exists($photo_path)) {
        $info = @getimagesize($photo_path);
        if ($info) {
            $photo = match($info[2]) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($photo_path),
                IMAGETYPE_PNG  => @imagecreatefrompng($photo_path),
                IMAGETYPE_GIF  => @imagecreatefromgif($photo_path),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp')
                                  ? @imagecreatefromwebp($photo_path) : false,
                default        => false,
            } ?: null;
            if ($photo) {
                // Center-crop to square
                $pw = imagesx($photo); $ph = imagesy($photo);
                $side = min($pw, $ph);
                $ox = (int)(($pw-$side)/2); $oy = (int)(($ph-$side)/2);
                $sq = imagecreatetruecolor($side, $side);
                imagecopy($sq, $photo, 0, 0, $ox, $oy, $side, $side);
                imagedestroy($photo);
                $photo = $sq;
            }
        }
    }

    $gif = new AnimatedGif(0, DELAY_CS);

    for ($fi = 0; $fi < N_FRAMES; $fi++) {
        $frame = render_frame($fi, $stars, $confetti, $photo, $name);
        $gif->addFrame($frame);
        if (PHP_SAPI === 'cli' && $fi % GIF_FPS === 0) {
            echo "  frame " . ($fi+1) . "/" . N_FRAMES . "\n";
        }
    }

    if ($photo) imagedestroy($photo);
    return $gif->save($out);
}

// ── CLI usage ─────────────────────────────────────────────────────────────────
if (PHP_SAPI === 'cli') {
    $opts = getopt('', ['name:', 'photo:', 'out:']);
    $name = $opts['name']  ?? 'Happy Person';
    $photo= $opts['photo'] ?? null;
    $out  = $opts['out']   ?? 'birthday_card.gif';
    echo "Generating {$name} → {$out}\n";
    $ok = generate_birthday_gif($name, $photo, $out);
    echo $ok ? "Done! " . round(filesize($out)/1024) . " KB\n" : "Failed.\n";
}