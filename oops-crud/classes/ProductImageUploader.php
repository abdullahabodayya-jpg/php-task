<?php

class ProductImageUploader
{
    private string $directory;
    private string $publicPrefix = "uploads/products/";

    public function __construct()
    {
        $this->directory = __DIR__ . "/../public/uploads/products/";

        if (!is_dir($this->directory)) {
            if (!mkdir($this->directory, 0755, true) &&
                !is_dir($this->directory)) {
                throw new RuntimeException(
                    "Could not create upload directory."
                );
            }
        }
    }

    public function upload(array $file): string
    {
        if (
            !isset($file["error"], $file["tmp_name"], $file["size"]) ||
            is_array($file["error"]) ||
            $file["error"] !== UPLOAD_ERR_OK
        ) {
            throw new RuntimeException("Image upload failed.");
        }

        if (
            !is_uploaded_file($file["tmp_name"]) ||
            $file["size"] < 1 ||
            $file["size"] > 5 * 1024 * 1024
        ) {
            throw new RuntimeException(
                "Image must be smaller than 5 MB."
            );
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file["tmp_name"]);

        $allowedTypes = [
            "image/jpeg" => "jpg",
            "image/png"  => "png",
            "image/webp" => "webp"
        ];

        if (!isset($allowedTypes[$mime])) {
            throw new RuntimeException(
                "Only JPEG, PNG and WebP images are allowed."
            );
        }

        if (@getimagesize($file["tmp_name"]) === false) {
            throw new RuntimeException("Invalid image file.");
        }

        $filename = bin2hex(random_bytes(16))
            . "."
            . $allowedTypes[$mime];

        $destination = $this->directory . $filename;

        if (!move_uploaded_file($file["tmp_name"], $destination)) {
            throw new RuntimeException(
                "Could not save uploaded image."
            );
        }

        return $this->publicPrefix . $filename;
    }

    public function deleteFile(string $relativePath): void
    {
        $filename = basename($relativePath);

        if (!preg_match('/\A[a-f0-9]{32}\.(jpg|png|webp)\z/', $filename)) {
            return;
        }

        $path = $this->directory . $filename;

        if (is_file($path)) {
            unlink($path);
        }
    }
}