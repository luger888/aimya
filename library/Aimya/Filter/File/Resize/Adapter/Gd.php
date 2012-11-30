<?php
// Aimya/Filter/File/Resize/Adapter/Gd.php
/**
 *
 * @category   Aimya
 * @package    Aimya_Filter
 */

require_once 'Aimya/Filter/File/Resize/Adapter/Abstract.php';

/**
 * Resizes a given file with the gd adapter and saves the created file
 *
 * @category   Aimya
 * @package    Aimya_Filter
 */
class Aimya_Filter_File_Resize_Adapter_Gd extends
    Aimya_Filter_File_Resize_Adapter_Abstract
{
    public function resize($width, $height, $keepRatio, $file, $target, $keepSmaller = true)
    {
        list($oldWidth, $oldHeight, $type) = getimagesize($file);

        switch ($type) {
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($file);
                break;
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($file);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($file);
                break;
        }

        if (!$keepSmaller || $oldWidth > $width || $oldHeight > $height) {
            if ($keepRatio) {
                list($width, $height) = $this->_calculateWidth($oldWidth, $oldHeight, $width, $height);
            }
        } else {
            $width = $oldWidth;
            $height = $oldHeight;
        }

        $thumb = imagecreatetruecolor($width, $height);

        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);

        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $oldWidth, $oldHeight);

        switch ($type) {
            case IMAGETYPE_PNG:
                imagepng($thumb, $target);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($thumb, $target);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumb, $target);
                break;
        }
        return $target;
    }
}