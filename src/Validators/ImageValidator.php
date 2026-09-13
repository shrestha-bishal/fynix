<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class ImageValidator extends ValidatorBase 
{
    protected int $_maxFileSizeMB = 5; // - 5MB

    protected function __construct(string $name, string $propertyName)
    {
        parent::__construct($name, $propertyName);
        $this->includeGenericValidation = false;
    }

    public function maxFileSizeMB(int $megabytes): static
    {
        if ($megabytes < 1) {
            throw new \InvalidArgumentException('The maximum file size must be at least 1 MB.');
        }

        return $this->with('_maxFileSizeMB', $megabytes);
    }

    public function validate(mixed $fieldValue = null) : ?ValidationError
    {
        if ($this->boundObject !== null) return $this->validateBound($fieldValue);
        if (!is_array($fieldValue))
            return new ValidationError($this, "$this->name must be an uploaded image.", 'image.invalid');

        $filename = $fieldValue['name'] ?? '';

        if(empty($filename)) {
            return $this->isRequired
                ? new ValidationError($this, "$this->name is required.", 'required')
                : null;
        }

        if (!isset($fieldValue['error'], $fieldValue['size'], $fieldValue['tmp_name']))
            return new ValidationError($this, "$this->name is not a valid upload.", 'image.invalid');
            
        // Checking if there is a valid file (skip empty files)
        if($fieldValue['error'] === UPLOAD_ERR_NO_FILE)
            return new ValidationError($this, "$filename is not a valid file.", 'image.invalid');

            // Checking if there was an upload error
        if ($fieldValue['error'] !== UPLOAD_ERR_OK)
            return new ValidationError($this, "File upload error for $filename", 'image.upload_error');

        if (!is_string($fieldValue['tmp_name']) || !is_file($fieldValue['tmp_name']))
            return new ValidationError($this, "$filename is not a readable image.", 'image.invalid');

        // Checking file size
        if($fieldValue['size'] > ($this->_maxFileSizeMB * 1024 * 1024))  
            return new ValidationError($this, "$filename exceeds the maximum size limit of $this->_maxFileSizeMB.");

        // Checking for allowed extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'bmp'];
        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
        if(!in_array(strtolower($fileExtension), $allowedExtensions)) 
            return new ValidationError($this, "$filename has an invalid extension. Allowed extensions: jpg, jpeg, png, gif, webp, avif, bmp.");
        
        if (@getimagesize($fieldValue['tmp_name']) === false)
            return new ValidationError($this, "$filename does not contain valid image content.", 'image.invalid');

        return null;
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