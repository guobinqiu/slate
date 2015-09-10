<?php

namespace Jili\BackendBundle\Helper;

use Symfony\Component\Filesystem\Filesystem;

class ImageResizer
{

    /**
     * Resize an image
     *
     * @param string $image (The full image path with filename and extension)
     * @param string $targetDir (The new dir to where the image needs to be stored)
     * @param string $relativePath (The new relative path to where the image needs to be stored)
     * @param int $sidePx (The new side to resize the image to)
     * @return string (The new path to the reized image)
     */
    public function resizeImage($image, $targetDir, $relativePath, $sidePx = 0)
    {
        // Get current dimensions
        $ImageDetails = $this->getImageDetails($image);
        $name = $ImageDetails->name;
        $height_orig = $ImageDetails->height;
        $width_orig = $ImageDetails->width;
        $fileExtention = $ImageDetails->extension;
        $ratio = $ImageDetails->ratio;
        $jpegQuality = 100;

        $gd_image_dest = imagecreatetruecolor($sidePx, $sidePx);
        $gd_image_src = null;
        switch ($fileExtention) {
            case 'png' :
                $gd_image_src = imagecreatefrompng($image);
                imagealphablending($gd_image_dest, false);
                imagesavealpha($gd_image_dest, true);
                break;
            case 'jpeg' :
            case 'jpg' :
                $gd_image_src = imagecreatefromjpeg($image);
                break;
            case 'gif' :
                $gd_image_src = imagecreatefromgif($image);
                break;
            default :
                break;
        }
        if ($width_orig >= $height_orig) {
            $trim_px = (int) ($width_orig - $height_orig) / 2;
            imagecopyresampled($gd_image_dest, $gd_image_src, 0, 0, $trim_px, 0, $sidePx, $sidePx, $height_orig, $height_orig);
        } else {
            $trim_px = (int) ($height_orig - $width_orig) / 2;
            imagecopyresampled($gd_image_dest, $gd_image_src, 0, 0, 0, $trim_px, $sidePx, $sidePx, $width_orig, $width_orig);
        }

        $path = explode("/", $relativePath);
        $this->mkdir_chmod($targetDir, $path[0], 0666);
        $this->mkdir_chmod($targetDir . $path[0], $path[1], 0666);

        $newPath = $targetDir . $relativePath;

        switch ($fileExtention) {
            case 'png' :
                imagepng($gd_image_dest, $newPath);
                break;
            case 'jpeg' :
            case 'jpg' :
                imagejpeg($gd_image_dest, $newPath, $jpegQuality);
                break;
            case 'gif' :
                imagegif($gd_image_dest, $newPath);
                break;
            default :
                break;
        }

        return $newPath;
    }

    /**
     *
     * Gets image details such as the extension, sizes and filename and returns them as a standard object.
     *
     * @param $imageWithPath
     * @return \stdClass
     */
    private function getImageDetails($imageWithPath)
    {
        $size = getimagesize($imageWithPath);

        $imgParts = explode("/", $imageWithPath);
        $lastPart = $imgParts[count($imgParts) - 1];

        if (stristr("?", $lastPart)) {
            $lastPart = substr($lastPart, 0, stripos("?", $lastPart));
        }
        if (stristr("#", $lastPart)) {
            $lastPart = substr($lastPart, 0, stripos("#", $lastPart));
        }

        $dotPos = stripos($lastPart, ".");
        $name = substr($lastPart, 0, $dotPos);
        $extension = substr($lastPart, $dotPos + 1);

        $Details = new \stdClass();
        $Details->height = $size[1];
        $Details->width = $size[0];
        $Details->ratio = $size[0] / $size[1];
        $Details->extension = $extension;
        $Details->name = $name;

        return $Details;
    }

    private function mkdir_chmod($base_dir, $path, $mode)
    {
        $base_dir .= '/' . $path;

        if (!file_exists($base_dir)) {
            try {
                mkdir($base_dir, $mode, true);
                chmod($base_dir, $mode);
            } catch (Exception $e) {
            }
        }
    }
}
?>