<?php
/**
 * IIS project 2019
 * Description: Image driver for storing images and finding correct paths.
 * Author: Jiri Jurica (xjuric29)
 */

namespace App\Model;

use Nette;
use Tracy\Debugger;

class Image {
    private $database;
    private $workDir;

    // Image settings.
    private $IMAGES_PER_TICKET = 10;
    private $TICKET_IMAGE_REL_PATH = '/img/usr/';
    private $TICKET_IMAGE_PATH = '';

    public function __construct($wwwDir, Nette\Database\Context $database) {
        $this->database = $database;
        $this->workDir = $wwwDir;
        $this->TICKET_IMAGE_PATH = $wwwDir . $this->TICKET_IMAGE_REL_PATH;
    }

    public function getImageName($ticketId, $imageNumber) {
        /**Return hash name of image. It does not matter if exist or not.
         * @param $ticketId: Specific ticket id.
         * @param $imageNumber: Number in range from  0 to $IMAGES_PER_TICKET. */
        if ($imageNumber >= 1 && $imageNumber <= $this->IMAGES_PER_TICKET) {
            // Readable name as 42_8 for ticket with id 42 and images with number 8.
            $name = $ticketId . '_' . $imageNumber;
            $hashedName = md5($name);

            // We now support only one image extension.
            return $hashedName  . '.jpg';
        }
        else return null;
    }

    public function getArrayOfRealImages($ticketId) {
        /**Return array with paths to existing images for specific ticket.
         * @param $ticketId: Specific ticket id. */
        $images = array();

        for ($imageNumber = 1; $imageNumber <= $this->IMAGES_PER_TICKET; $imageNumber++) {
            // Relative path for latte.
            $webPath = $this->TICKET_IMAGE_REL_PATH . $this->getImageName($ticketId, $imageNumber);
            // Absolute system path for checking ig image exists.
            $path = $this->TICKET_IMAGE_PATH . $this->getImageName($ticketId, $imageNumber);

            if (file_exists($path)) array_push($images, $webPath);
        }

        return $images;
    }

    public function getImageCount($ticketId) {
        /**Return image count for specific ticket.
         * @param $ticketId: Specific ticket id. */
        return count($this->getArrayOfRealImages($ticketId));
    }

    public function getArrayOfFreePaths($ticketId) {
        /**Return array with paths to non existing images for specific ticket.
         * @param $ticketId: Specific ticket id. */
        $freePaths = array();

        for ($imageNumber = 1; $imageNumber <= $this->IMAGES_PER_TICKET; $imageNumber++) {
            $path = $this->TICKET_IMAGE_PATH . $this->getImageName($ticketId, $imageNumber);

            if (!file_exists($path)) array_push($freePaths, $path);
        }

        return $freePaths;
    }

    public function getFreePathCount($ticketId) {
        /**Return count of free paths for specific ticket.
         * @param $ticketId: Specific ticket id. */
        return count($this->getArrayOfFreePaths($ticketId));
    }

    public function saveImages($files, $ticketId) {
        /**Save uploaded images to filesystem.
         * @param $files: Array of Image objects.
         * @param $ticketId: Specific ticket id. */
        $freePathsArray = $this->getArrayOfFreePaths($ticketId);

        if (count($freePathsArray) < count($files)) throw new Exception('Not enough space for images!');

        for ($fileIndex = 0; $fileIndex < count($files); $fileIndex++) {
            $files[$fileIndex]->move($freePathsArray[$fileIndex]);
        }
    }

    public function deleteImage($relPath) {
        /**Delete specified image.
         * @param $relPath: Relative path from site root. */
        unlink($this->workDir . $relPath);
    }

    // Constant getters.
    public function getAllowedImageCount() {
        /**Getter for max image count for ticket. */
        return $this->IMAGES_PER_TICKET;
    }
}