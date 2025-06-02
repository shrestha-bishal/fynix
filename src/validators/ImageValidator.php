<?php 
namespace PhpValidationCore\Validators;

use ValidatePhpCore\ValidationError;
use ValidatePhpCore\ValidatorBase;

class ImageValidator extends ValidatorBase 
{
    private int $_maxFileSizeMB = 5; // - 5MB

    public function __construct(
        string $name, 
        string $fieldName, 
        bool $isRequired = true,
        ?int $maxFileSizeMB = null) 
    {
        parent::__construct(
            $name,
            $fieldName,
            $numMinImages = 1,
            $numMaxImages = 1,
            "image",
            $isRequired,
            $includeGenericValidation = false
        );

        if($maxFileSizeMB != null) 
        {
            $this->_maxFileSizeMB = $maxFileSizeMB;
        }
    }

    public function validate($fieldValue) : ?ValidationError 
    {
        $filename = $fieldValue['name'];

        if(empty($filename)) 
            return null;
            
        // Checking if there is a valid file (skip empty files)
        if(empty($filename) || $fieldValue['error'] === UPLOAD_ERR_NO_FILE)
            return new ValidationError($this, "$filename is not valid file.");

            // Checking if there was an upload error
        if ($fieldValue['error'] !== UPLOAD_ERR_OK)
            return new ValidationError($this, "File upload error for $filename");

        // Checking if it's a valid image
        // $imageInfo = getimagesize($fieldValue['tmp_name'][$key]);
        // if($imageInfo === false)
        //     return new ValidationError($this, "$filename is not a valid image.");

        // Checking file size
        if($fieldValue['size'] > ($this->_maxFileSizeMB * 1024 * 1024))  
            return new ValidationError($this, "$filename exceeds the maximum size limit of $this->_maxFileSizeMB.");

        // Checking for allowed extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'bmp'];
        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
        if(!in_array(strtolower($fileExtension), $allowedExtensions)) 
            return new ValidationError($this, "$filename has an invalid extension. Allowed extensions: jpg, jpeg, png, gif, webp, avif, bmp.");
        
        $fileContents = file_get_contents($fieldValue['tmp_name']);
        $hasValidImageFileContent = self::validateImageFileContent($fileContents);

        if(!$hasValidImageFileContent)
            return new ValidationError($this, "$filename does not contain a valid image content.");

        return null;
    }

    private static function validateImageFileContent(string $fileContents) : bool
    {
        if(self::isWebPImage($fileContents) == true) return true;
        if(self::isBmpImage($fileContents) == true) return true;
        if(self::isGifImage($fileContents) == true) return true;
        if(self::isPngImage($fileContents) == true) return true;
        if(self::isTiffImage($fileContents) == true) return true;
        if(self::isJpegImage($fileContents) == true) return true;
        if(self::isAvifImage($fileContents) == true) return true;

        return false;
    }

    private static function isWebPImage(string $fileContents) : bool 
    {
        $fileSignature = "RIFF"; //RIFF signature
        $byteSample = substr($fileContents, 0, strlen($fileSignature));
        return $byteSample === $fileSignature;
    }

    public static function isBmpImage(string $fileContents) : bool
    {
        $bmp = "BM";  // BMP signature
        $byteSample = substr($fileContents, 0, strlen($bmp));
        return $bmp === $byteSample;
    }

    public static function isGifImage(string $fileContents) : bool
    {
        $gif = "GIF";  // GIF signature
        $byteSample = substr($fileContents, 0, strlen($gif));
        return $gif === $byteSample;
    }

    public static function isPngImage(string $fileContents) : bool
    {
        $png = "\x89PNG";  // PNG signature (hex encoded)
        $byteSample = substr($fileContents, 0, strlen($png));
        return $png === $byteSample;
    }

    public static function isTiffImage(string $fileContents) : bool
    {
        $tiff1 = "\x49\x49\x2A";  // TIFF signature (Little Endian)
        $tiff2 = "\x4D\x4D\x2A";  // TIFF signature (Big Endian)
        $byteSample = substr($fileContents, 0, 3);

        return $tiff1 === $byteSample || $tiff2 === $byteSample;
    }

    public static function isJpegImage(string $fileContents) : bool
    {
        $startOfImage = "\xFF\xD8";  // JPEG start signature (SOI)
        $endOfImage = "\xFF\xD9";    // JPEG end signature (EOI)

        // Check for the SOI marker
        $startSample = substr($fileContents, 0, 2);
        if ($startSample !== $startOfImage) {
            return false;
        }

        // Check for the EOI marker (optional, but useful for confirmation)
        $endSample = substr($fileContents, -2);
        return $endSample === $endOfImage;
    }

    public static function isAvifImage(string $fileContents) : bool
    {
        // AVIF signature:
        // First 4 bytes = "ftyp"
        // Next 4 bytes = "avif"

        $ftyp = "ftyp";  // "ftyp" signature
        $avif = "avif";  // "avif" signature

        // Check if first 8 bytes match "ftypavif"
        $fileSample = substr($fileContents, 0, strlen($ftyp) + strlen($avif));
        return $ftyp . $avif === $fileSample;
    }
}