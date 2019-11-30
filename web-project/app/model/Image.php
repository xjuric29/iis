<?php
/**
 * IIS project 2019
 * Description: Image driver for storing images and finding correct paths.
 * Author: Jiri Jurica (xjuric29)
 */

namespace App\Model;

use Nette;

class Image {
    private $database;

    // Image settings.
    private $IMAGES_PER_TICKET = 10;
    private $TICKET_IMAGE_PATH = '/img/usr/';

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
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
            $path = $this->TICKET_IMAGE_PATH . $this->getImageName($ticketId, $imageNumber);

            if (file_exists($path)) array_push($images, $path);
        }

        return $images;
    }

    public function getImageCountForTicket($ticketId) {
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

    public function getFreePathCountForTicket($ticketId) {
        /**Return count of free paths for specific ticket.
         * @param $ticketId: Specific ticket id. */
        return count($this->getArrayOfFreePaths($ticketId));
    }

    public  function deleteImage($hashedName) {
        /**Delete specified image.
         * @param $hashedName: Name with extension ONLY! We do not want to send whole path in GET parameter. */
        unlink($this->TICKET_IMAGE_PATH . $hashedName);
    }
}