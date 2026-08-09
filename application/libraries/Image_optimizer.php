<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Normalizes uploaded images into web-sized WebP/JPEG assets.
 * WebP is preferred, while JPEG remains available as a safe fallback.
 */
class Image_optimizer
{
    private $ci;

    public function __construct()
    {
        // The optimizer is intentionally self-contained; no CI service is required.
    }

    public function process($source_path, $directory, $options = [])
    {
        if (!is_file($source_path) || !is_readable($source_path)) {
            return ['status' => FALSE, 'error' => 'File gambar tidak dapat dibaca.'];
        }

        $info = @getimagesize($source_path);
        if (!$info || empty($info['mime'])) {
            return ['status' => FALSE, 'error' => 'File yang diunggah bukan gambar yang valid.'];
        }
        if (empty($info[0]) || empty($info[1]) || $info[0] > 8000 || $info[1] > 8000 || ($info[0] * $info[1]) > 12000000) {
            return ['status' => FALSE, 'error' => 'Dimensi gambar terlalu besar. Maksimal 12 megapiksel.'];
        }

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array(strtolower($info['mime']), $allowed, TRUE)) {
            return ['status' => FALSE, 'error' => 'Format gambar harus JPG, PNG, GIF, atau WebP.'];
        }

        $max_dimension = isset($options['max_dimension']) ? (int) $options['max_dimension'] : 1600;
        $quality = isset($options['quality']) ? (int) $options['quality'] : 84;
        $thumbnail = !empty($options['thumbnail']);
        $thumbnail_dimension = isset($options['thumbnail_dimension']) ? (int) $options['thumbnail_dimension'] : 480;

        if (!is_dir($directory) && !@mkdir($directory, 0755, TRUE)) {
            return ['status' => FALSE, 'error' => 'Folder penyimpanan gambar tidak dapat dibuat.'];
        }
        if (!is_writable($directory)) {
            return ['status' => FALSE, 'error' => 'Folder penyimpanan gambar tidak writable oleh PHP.'];
        }

        $source = $this->create_source($source_path, $info['mime']);
        if (!$source) {
            return ['status' => FALSE, 'error' => 'Format gambar tidak dapat diproses oleh GD.'];
        }

        $source = $this->correct_orientation($source, $source_path, $info['mime']);
        $width = imagesx($source);
        $height = imagesy($source);
        $canvas = $this->resize($source, $width, $height, $max_dimension, !empty($options['crop']));
        imagedestroy($source);

        if (!$canvas) {
            return ['status' => FALSE, 'error' => 'Gambar gagal di-resize.'];
        }

        $basename = bin2hex(random_bytes(16));
        $temp = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $basename . '.png';
        $webp_name = $basename . '.webp';
        $jpeg_name = $basename . '.jpg';
        $webp_path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $webp_name;
        $jpeg_path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $jpeg_name;

        imagealphablending($canvas, FALSE);
        imagesavealpha($canvas, TRUE);
        if (!@imagepng($canvas, $temp, 6)) {
            imagedestroy($canvas);
            return ['status' => FALSE, 'error' => 'Gambar sementara gagal dibuat.'];
        }

        $webp_created = $this->convert_to_webp($temp, $webp_path, $quality);
        $jpeg_created = $this->save_jpeg($canvas, $jpeg_path, $quality);
        @unlink($temp);

        $thumbnail_name = NULL;
        $thumbnail_fallback = NULL;
        if ($thumbnail) {
            $thumb = $this->resize($canvas, imagesx($canvas), imagesy($canvas), $thumbnail_dimension, TRUE);
            if ($thumb) {
                $thumb_base = bin2hex(random_bytes(16));
                $thumb_temp = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $thumb_base . '.png';
                $thumb_webp_path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $thumb_base . '.webp';
                $thumb_jpeg_path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $thumb_base . '.jpg';
                imagealphablending($thumb, FALSE);
                imagesavealpha($thumb, TRUE);
                @imagepng($thumb, $thumb_temp, 6);
                if ($this->convert_to_webp($thumb_temp, $thumb_webp_path, max(75, $quality - 4))) {
                    $thumbnail_name = basename($thumb_webp_path);
                }
                if ($this->save_jpeg($thumb, $thumb_jpeg_path, max(75, $quality - 4))) {
                    $thumbnail_fallback = basename($thumb_jpeg_path);
                }
                @unlink($thumb_temp);
                imagedestroy($thumb);
            }
        }

        imagedestroy($canvas);

        if (!$webp_created && !$jpeg_created) {
            return ['status' => FALSE, 'error' => 'Gambar gagal dikonversi. Pastikan GD atau cwebp tersedia.'];
        }

        return [
            'status' => TRUE,
            'webp' => $webp_created ? $webp_name : NULL,
            'jpeg' => $jpeg_created ? $jpeg_name : NULL,
            'thumbnail_webp' => $thumbnail_name,
            'thumbnail_jpeg' => $thumbnail_fallback,
        ];
    }

    private function create_source($path, $mime)
    {
        if ($mime === 'image/jpeg' && function_exists('imagecreatefromjpeg')) return @imagecreatefromjpeg($path);
        if ($mime === 'image/png' && function_exists('imagecreatefrompng')) return @imagecreatefrompng($path);
        if ($mime === 'image/gif' && function_exists('imagecreatefromgif')) return @imagecreatefromgif($path);
        if ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) return @imagecreatefromwebp($path);
        return FALSE;
    }

    private function correct_orientation($image, $path, $mime)
    {
        if ($mime !== 'image/jpeg' || !function_exists('exif_read_data')) return $image;
        $exif = @exif_read_data($path);
        $orientation = isset($exif['Orientation']) ? (int) $exif['Orientation'] : 1;
        if ($orientation === 3) $image = imagerotate($image, 180, 0);
        if ($orientation === 6) $image = imagerotate($image, -90, 0);
        if ($orientation === 8) $image = imagerotate($image, 90, 0);
        return $image;
    }

    private function resize($source, $width, $height, $max_dimension, $crop = FALSE)
    {
        if ($max_dimension < 1) return FALSE;
        if ($crop) {
            $side = min($width, $height);
            $src_x = (int) (($width - $side) / 2);
            $src_y = (int) (($height - $side) / 2);
            $target_width = $target_height = $max_dimension;
        } else {
            $ratio = min(1, $max_dimension / max($width, $height));
            $src_x = $src_y = 0;
            $side = NULL;
            $target_width = max(1, (int) round($width * $ratio));
            $target_height = max(1, (int) round($height * $ratio));
        }

        $canvas = imagecreatetruecolor($target_width, $target_height);
        imagealphablending($canvas, FALSE);
        imagesavealpha($canvas, TRUE);
        $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefilledrectangle($canvas, 0, 0, $target_width, $target_height, $transparent);
        imagecopyresampled($canvas, $source, 0, 0, $src_x, $src_y, $target_width, $target_height, $crop ? $side : $width, $crop ? $side : $height);
        return $canvas;
    }

    private function convert_to_webp($input, $output, $quality)
    {
        if (function_exists('imagewebp')) {
            $image = @imagecreatefrompng($input);
            if ($image) {
                $result = @imagewebp($image, $output, $quality);
                imagedestroy($image);
                if ($result && is_file($output)) return TRUE;
            }
        }

        if (!function_exists('exec')) return FALSE;
        $commands = ['cwebp', '/usr/local/bin/cwebp', '/opt/homebrew/bin/cwebp', '/Applications/XAMPP/xamppfiles/bin/cwebp', '/Applications/XAMPP/bin/cwebp'];
        foreach ($commands as $command) {
            $cmd = escapeshellarg($command) . ' -quiet -q ' . (int) $quality . ' ' . escapeshellarg($input) . ' -o ' . escapeshellarg($output) . ' 2>&1';
            $result = [];
            $status = 1;
            @exec($cmd, $result, $status);
            if ($status === 0 && is_file($output) && filesize($output) > 0) return TRUE;
        }
        return FALSE;
    }

    private function save_jpeg($image, $path, $quality)
    {
        $background = imagecreatetruecolor(imagesx($image), imagesy($image));
        $white = imagecolorallocate($background, 255, 255, 255);
        imagefill($background, 0, 0, $white);
        imagecopy($background, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        $result = @imagejpeg($background, $path, $quality);
        imagedestroy($background);
        return $result && is_file($path);
    }
}
